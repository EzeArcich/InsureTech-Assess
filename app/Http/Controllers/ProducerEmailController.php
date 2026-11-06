<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendProducerEmailRequest;
use App\Mail\ProducerMessage;
use App\Models\Claim;
use App\Models\EmailDelivery;
use App\Models\EmailTemplateVersion;
use App\Models\Producer;
use App\Services\RenderEmailTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProducerEmailController extends Controller
{
    public function preview(SendProducerEmailRequest $request, Claim $claim, RenderEmailTemplate $renderer): JsonResponse
    {
        return response()->json($this->message($request, $claim, $renderer));
    }

    public function send(SendProducerEmailRequest $request, Claim $claim, RenderEmailTemplate $renderer): JsonResponse
    {
        $message = $this->message($request, $claim, $renderer);
        $delivery = EmailDelivery::firstOrCreate(['request_id' => $request->validated('request_id')], [
            'claim_id' => $claim->id, 'email_template_version_id' => $request->validated('template_version_id'), 'sent_by' => $request->user()->id,
            'recipient' => $message['recipient'], 'cc' => $message['cc'], 'subject' => $message['subject'], 'body' => $message['body'], 'status' => 'sending',
        ]);
        if (! $delivery->wasRecentlyCreated) {
            abort_unless($delivery->sent_by === $request->user()->id && $delivery->claim_id === $claim->id, 409);
            abort_unless($delivery->status === 'sent', 409, 'El envío ya está en curso o falló. Revisá el resultado antes de intentar otro envío.');

            return response()->json(['message' => 'Este correo ya fue enviado.']);
        }
        try {
            Mail::to($message['recipient'])->cc($message['cc'])->send(new ProducerMessage($message['subject'], $message['body']));
        } catch (Throwable $exception) {
            $delivery->update(['status' => 'failed']);
            report($exception);

            return response()->json(['message' => 'No se pudo enviar el correo. Revisá la configuración de correo e intentá un nuevo envío.'], 503);
        }
        $delivery->update(['status' => 'sent', 'sent_at' => now()]);

        return response()->json(['message' => 'Correo enviado al productor.']);
    }

    /** @return array{subject: string, body: string, recipient: string, cc: array<int, string>} */
    private function message(SendProducerEmailRequest $request, Claim $claim, RenderEmailTemplate $renderer): array
    {
        $claim = Claim::visibleTo($request->user())->findOrFail($claim->id);
        $producer = Producer::findOrFail($request->validated('producer_id'));
        if (! filter_var($producer->email, FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::withMessages(['producer_id' => 'El productor seleccionado no tiene un email válido.']);
        }
        $version = EmailTemplateVersion::with('template')->findOrFail($request->validated('template_version_id'));
        if (! $version->template->is_active) {
            throw ValidationException::withMessages(['template_version_id' => 'La plantilla está desactivada.']);
        }
        $cc = collect($request->validated('cc') ?? [])->map(fn (string $email): string => mb_strtolower(trim($email)))
            ->reject(fn (string $email): bool => $email === mb_strtolower($producer->email))->unique()->values()->all();

        return [...$renderer->render($version, $claim, $producer, $request->user()), 'recipient' => $producer->email, 'cc' => $cc];
    }
}
