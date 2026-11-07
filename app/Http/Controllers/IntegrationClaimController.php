<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReceiveIntegrationClaimRequest;
use App\Http\Resources\IntegratedClaimResource;
use App\Services\ReceiveIntegrationClaim;
use Illuminate\Http\JsonResponse;

class IntegrationClaimController extends Controller
{
    public function store(ReceiveIntegrationClaimRequest $request, ReceiveIntegrationClaim $receive): JsonResponse
    {
        $result = $receive->handle($request->attributes->get('integration_credential'), $request->validated());
        $request->attributes->set('integration_payload_hash', $result['hash']);

        return (new IntegratedClaimResource($result['data']))->additional(['meta' => ['replayed' => $result['replayed'], 'request_id' => $request->attributes->get('integration_request_id')]])->response()->setStatusCode($result['replayed'] ? 200 : 201);
    }
}
