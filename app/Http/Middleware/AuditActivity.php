<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $isMutation = in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true);
        $isSuccessful = $response->isSuccessful() || $response->isRedirection();

        if ($request->user() && $isMutation && $isSuccessful) {
            $route = $request->route();
            $parameters = $route?->parameters() ?? [];
            $record = collect($parameters)->first(fn ($value) => is_object($value) && isset($value->id));
            $resource = $route?->getName() ?? trim($request->path(), '/');
            $action = match ($request->method()) {
                'POST' => 'create',
                'PUT', 'PATCH' => 'update',
                'DELETE' => 'delete',
            };

            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => $action,
                'table_name' => $resource,
                'record_id' => $record?->id,
                'new_values' => $request->except(['_token', 'password', 'password_confirmation', 'token', 'file']),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        }

        return $response;
    }
}