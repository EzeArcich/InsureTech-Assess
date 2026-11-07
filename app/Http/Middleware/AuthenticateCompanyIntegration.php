<?php

namespace App\Http\Middleware;

use App\Models\CompanyIntegrationCredential;
use App\Models\IntegrationRequest;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class AuthenticateCompanyIntegration
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');
        $start = microtime(true);
        $requestId = (string) Str::uuid();
        $credential = null;
        $request->attributes->set('integration_request_id', $requestId);
        try {
            $token = $request->bearerToken() ?? '';
            if (preg_match('/^([1-9][0-9]*)\|([a-f0-9]{64})$/D', $token, $parts)) {
                $credential = CompanyIntegrationCredential::find($parts[1]);
            }
            if (! $credential || ! hash_equals($credential->token_hash, hash('sha256', $parts[2] ?? '')) || $credential->revoked_at || ($credential->expires_at && $credential->expires_at->isPast())) {
                $credential = null;
                $response = response()->json(['message' => 'Credencial de aseguradora inválida.'], 401);
            } elseif (! $credential->active || ! in_array('claims:create', $credential->abilities ?? [], true)) {
                $response = response()->json(['message' => 'La aseguradora no tiene autorización para registrar siniestros.'], 403);
            } else {
                $request->attributes->set('integration_credential', $credential);
                $response = $next($request);
            }
        } catch (ValidationException $e) {
            $response = response()->json(['message' => 'Los datos enviados no son válidos.', 'errors' => $e->errors()], 422);
        } catch (Throwable $e) {
            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
            $response = response()->json(['message' => $status >= 500 ? 'No se pudo registrar el siniestro.' : $e->getMessage()], $status);
            Log::warning('claim_integration.failure', ['request_id' => $requestId, 'exception' => get_class($e), 'status' => $status]);
        }
        $status = $response->getStatusCode();
        if ($status >= 500) {
            $response = response()->json(['message' => 'No se pudo registrar el siniestro.'], $status);
        }
        $outcome = $status === 201 ? 'created' : ($status === 200 ? 'duplicate' : 'rejected');
        try {
            IntegrationRequest::create(['id' => $requestId, 'credential_id' => $credential?->id, 'payload_hash' => $request->attributes->get('integration_payload_hash'), 'http_status' => $status, 'outcome' => $outcome, 'duration_ms' => (int) ((microtime(true) - $start) * 1000)]);
        } catch (Throwable $e) {
            Log::warning('claim_integration.audit_unavailable', ['request_id' => $requestId, 'exception' => get_class($e)]);
        }
        Log::info('claim_integration.request', ['request_id' => $requestId, 'credential_id' => $credential?->id, 'status' => $status, 'outcome' => $outcome]);
        $response->headers->set('X-Request-ID', $requestId);
        $response->headers->set('Cache-Control', 'no-store');

        return $response;
    }
}
