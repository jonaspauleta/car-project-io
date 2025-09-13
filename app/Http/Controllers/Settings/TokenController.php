<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\CreateTokenRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TokenController extends Controller
{
    /**
     * Display the token management page.
     */
    public function index(): Response
    {
        $tokens = Auth::user()->tokens()->get()->map(function ($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'last_used_at' => $token->last_used_at?->diffForHumans(),
                'expires_at' => $token->expires_at?->format('Y-m-d H:i:s'),
                'created_at' => $token->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return Inertia::render('settings/tokens', [
            'tokens' => $tokens,
        ]);
    }

    /**
     * Store a newly created token.
     */
    public function store(CreateTokenRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $expiresAt = $request->validated()['expires_at'] ?? null;

        $token = $user->createToken(
            $request->validated()['name'],
            ['*'],
            $expiresAt ? now()->parse($expiresAt) : null
        );

        return redirect()->back()->with([
            'token' => $token->plainTextToken,
            'message' => 'Token created successfully. Please copy it now as it will not be shown again.',
        ]);
    }

    /**
     * Remove the specified token.
     */
    public function destroy(Request $request, int $tokenId): RedirectResponse
    {
        $user = Auth::user();

        $token = $user->tokens()->findOrFail($tokenId);
        $token->delete();

        return redirect()->back()->with('message', 'Token deleted successfully.');
    }

    /**
     * Revoke all tokens for the authenticated user.
     */
    public function revokeAll(): RedirectResponse
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return redirect()->back()->with('message', 'All tokens have been revoked.');
    }
}
