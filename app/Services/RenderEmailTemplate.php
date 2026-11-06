<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\EmailTemplateVersion;
use App\Models\Producer;
use App\Models\User;
use DOMDocument;
use DOMNode;
use Illuminate\Validation\ValidationException;

class RenderEmailTemplate
{
    public const VARIABLES = [
        'claim_number' => 'Número de siniestro', 'company_name' => 'Compañía', 'client_name' => 'Asegurado',
        'producer_name' => 'Productor', 'license_plate' => 'Patente', 'inspection_date' => 'Fecha de inspección',
        'inspection_time' => 'Horario de inspección', 'inspection_mode' => 'Modalidad',
        'inspection_address' => 'Domicilio de la visita', 'coordinator_name' => 'Usuario que envía',
    ];

    /** @return array{subject: string, body: string} */
    public function render(EmailTemplateVersion $version, Claim $claim, Producer $producer, User $user): array
    {
        $values = ['claim_number' => $claim->claim_number, 'company_name' => $claim->company?->name,
            'client_name' => $claim->client_name, 'producer_name' => $producer->name, 'license_plate' => $claim->license_plate,
            'inspection_date' => $claim->inspection_date?->format('d/m/Y'), 'inspection_time' => $claim->inspection_time,
            'inspection_mode' => $claim->inspection_mode, 'inspection_address' => $claim->inspection_address ?: trim(($claim->address ?? '').', '.($claim->city ?? ''), ', '),
            'coordinator_name' => $user->name];
        $replace = function (string $text, bool $html) use ($values): string {
            return preg_replace_callback('/{{\s*([a-z_]+)\s*}}/', fn (array $match): string => $html ? e($values[$match[1]] ?? '') : (string) ($values[$match[1]] ?? ''), $text);
        };
        $subject = trim(preg_replace('/[\r\n]+/', ' ', strip_tags($replace($version->subject, false))));
        if (mb_strlen($subject) > 191 || $subject === '') {
            throw ValidationException::withMessages(['template_version_id' => 'El asunto generado está vacío o supera los 191 caracteres.']);
        }

        return ['subject' => $subject, 'body' => $replace($this->sanitize($version->body), true)];
    }

    public function sanitize(string $html): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="UTF-8"><html><body>'.$html.'</body></html>', LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
            $body = $document->getElementsByTagName('body')->item(0);
            $clean = function (DOMNode $node) use (&$clean): void {
                foreach (iterator_to_array($node->childNodes) as $child) {
                    if ($child->nodeType === XML_COMMENT_NODE || in_array(strtolower($child->nodeName), ['script', 'style', 'iframe', 'object', 'svg', 'math', 'form', 'input', 'button', 'img'], true)) {
                        $node->removeChild($child);
                        continue;
                    }
                    $clean($child);
                    if ($child instanceof \DOMElement) {
                        foreach (iterator_to_array($child->attributes) as $attribute) {
                            $child->removeAttribute($attribute->name);
                        }
                        if (! in_array(strtolower($child->tagName), ['p', 'div', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'h2', 'h3', 'blockquote'], true)) {
                            while ($child->firstChild) {
                                $node->insertBefore($child->firstChild, $child);
                            }
                            $node->removeChild($child);
                        }
                    }
                }
            };
            $clean($body);
            $result = '';
            foreach ($body->childNodes as $child) {
                $result .= $document->saveHTML($child);
            }

            return $result;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }
}
