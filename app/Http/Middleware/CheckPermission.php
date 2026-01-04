<?php

namespace App\Http\Middleware;

use App\Services\AuthorizationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function __construct(protected AuthorizationService $authService) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();

        if (!$user) {
            abort(401);
        }

        // The resource can be passed from the route or inferred (e.g., from model binding)
        // For simplicity now, we check the permission string (e.g., 'content.create')
        if (!$this->authService->can($user, $permission)) {
            abort(403, "You do not have the required permission: $permission");
        }

        return $next($request);
    }
}