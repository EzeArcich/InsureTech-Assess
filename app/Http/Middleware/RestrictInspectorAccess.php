<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictInspectorAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('workshops.index') || ($request->user()?->hasAnyRole(['CEO', 'Administrativo'])
            && $request->routeIs('workshops.create', 'workshops.store', 'workshops.edit', 'workshops.update', 'workshops.destroy'))) {
            return $next($request);
        }
        if ($request->user()?->isRestrictedInspector()) {
            if (app()->environment('local') && $request->routeIs('users.index', 'users.impersonate')) {
                return $next($request);
            }
            abort_unless($request->routeIs(
                'route-sheets.index', 'route-sheets.location', 'route-sheets.geocode', 'inspection-payments.index', 'inspection-payments.show',
                'home', 'claims.index', 'claims.assigned', 'claims.in_progress',
                'claims.show', 'claims.edit', 'claims.update', 'claims.data.show', 'claims.data.update',
                'workshops.search', 'parts.search', 'claims.inspection', 'claims.parts', 'claims.workshops.store',
                'clients.show', 'inspectors.show', 'inspectors.show.data',
            ), 403);
        }

        if ($request->user()?->isRoutePlanner()) {
            if (app()->environment('local') && $request->routeIs('users.index', 'users.impersonate')) {
                return $next($request);
            }
            abort_unless($request->routeIs(
                'home', 'claims.index', 'claims.assigned', 'claims.coordinated', 'claims.show',
                'route-sheets.index', 'route-sheets.assign', 'route-sheets.location', 'route-sheets.geocode',
                'clients.show', 'inspectors.show',
            ), 403);
        }

        if ($request->user()?->isRestrictedCoordinator()) {
            if (app()->environment('local') && $request->routeIs('users.index', 'users.impersonate')) {
                return $next($request);
            }
            abort_unless($request->routeIs(
                'home', 'claims.index', 'claims.pending', 'claims.absent', 'claims.show', 'claims.edit', 'claims.update',
                'claims.data.show', 'claims.data.update', 'claims.location.search', 'claims.nearby',
                'clients.show', 'inspectors.show', 'workshops.data', 'workshops.search', 'producers.data',
                'coordination.*', 'email-templates.*', 'claims.producer-email.*',
            ), 403);
        }

        return $next($request);
    }
}
