<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveEmailTemplateRequest;
use App\Models\EmailTemplate;
use App\Models\EmailTemplateVersion;
use App\Services\RenderEmailTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EmailTemplateController extends Controller
{
    public function index(): View
    {
        $templates = EmailTemplate::with('latestVersion')->orderBy('name')->get();

        return view('email-templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('email-templates.form', ['template' => new EmailTemplate(['is_active' => true]), 'version' => null]);
    }

    public function store(SaveEmailTemplateRequest $request, RenderEmailTemplate $renderer): RedirectResponse
    {
        $template = DB::transaction(function () use ($request, $renderer): EmailTemplate {
            $template = EmailTemplate::create($request->safe()->only(['name', 'is_active']));
            $template->versions()->create(['number' => 1, 'subject' => $request->validated('subject'),
                'body' => $renderer->sanitize($request->validated('body')), 'created_by' => $request->user()->id]);

            return $template;
        });

        return redirect()->route('email-templates.show', $template)->with('success', 'Plantilla creada.');
    }

    public function edit(EmailTemplate $email_template): View
    {
        return view('email-templates.form', ['template' => $email_template, 'version' => $email_template->latestVersion]);
    }

    public function update(SaveEmailTemplateRequest $request, EmailTemplate $email_template, RenderEmailTemplate $renderer): RedirectResponse
    {
        DB::transaction(function () use ($request, $email_template, $renderer): void {
            $template = EmailTemplate::lockForUpdate()->findOrFail($email_template->id);
            $latest = $template->versions()->max('number');
            if ((int) $request->validated('base_version') !== (int) $latest) {
                throw ValidationException::withMessages(['base_version' => 'La plantilla cambió mientras la editabas. Recargá la página antes de guardar.']);
            }
            $template->update($request->safe()->only(['name', 'is_active']));
            $template->versions()->create(['number' => $latest + 1, 'subject' => $request->validated('subject'),
                'body' => $renderer->sanitize($request->validated('body')), 'created_by' => $request->user()->id]);
        });

        return redirect()->route('email-templates.show', $email_template)->with('success', 'Nueva versión guardada.');
    }

    public function show(EmailTemplate $email_template): View
    {
        $versions = $email_template->versions()->orderByDesc('number')->get();

        return view('email-templates.show', ['template' => $email_template, 'versions' => $versions]);
    }

    public function restore(EmailTemplate $email_template, EmailTemplateVersion $version): RedirectResponse
    {
        abort_unless($version->email_template_id === $email_template->id, 404);
        DB::transaction(function () use ($email_template, $version): void {
            $template = EmailTemplate::lockForUpdate()->findOrFail($email_template->id);
            $template->versions()->create(['number' => $template->versions()->max('number') + 1, 'subject' => $version->subject,
                'body' => $version->body, 'created_by' => auth()->id()]);
        });

        return back()->with('success', 'Versión restaurada como una nueva revisión.');
    }

    public function destroy(EmailTemplate $email_template): RedirectResponse
    {
        $email_template->update(['is_active' => false]);

        return redirect()->route('email-templates.index')->with('success', 'Plantilla desactivada. Se conserva su historial.');
    }
}
