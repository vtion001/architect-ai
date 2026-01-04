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

        if (!$this->authService->can($user, $permission)) {
            // Log denied attempt for anomaly detection
            $this->authService->audit(
                $user,
                'access.denied',
                $request->path(),
                'denied',
                "Unauthorized attempt to access resource with permission: $permission"
            );

            abort(403, "You do not have the required permission: $permission");
        }

        return $next($request);
    }
}