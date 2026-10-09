<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    private const TEMPLATES = ['classic', 'jawa'];

    public function show(Request $request, Invitation $invitation, ?string $token = null): Response
    {
        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            return $this->withSafeHeaders(response()->view('invitation.expired', [], 410));
        }

        $guest = $this->findGuest($invitation, $token);

        if ($guest && ! $this->isLinkPreviewBot($request)) {
            $this->recordView($guest, $request);
        }

        $template = in_array($invitation->template, self::TEMPLATES, true)
            ? $invitation->template
            : 'classic';
        
        return $this->withSafeHeaders(response()->view("invitation.templates.{$template}", [
            'invitation' => $invitation,
            'guest' => $guest,
        ]));
    }

    private function findGuest(Invitation $invitation, ?string $token): ?Guest
    {
        if ($token === null || ! preg_match('/^[a-z0-9]{20}$/', $token)) {
            return null;
        }

        // Dicari lewat relasi: tamu undangan lain tidak akan pernah ditemukan.
        return $invitation->guests()->where('token', $token)->first();
    }

    private function recordView(Guest $guest, Request $request): void
    {
        // Hindari mencatat setiap refresh: maksimal satu catatan per 10 menit.
        $recent = $guest->views()
            ->where('viewed_at', '>', now()->subMinutes(10))
            ->exists();

        if ($recent) {
            return;
        }

        $guest->views()->create([
            'viewed_at' => now(),
            'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
        ]);
    }

    private function isLinkPreviewBot(Request $request): bool
    {
        return (bool) preg_match(
            '/bot|crawler|spider|facebookexternalhit|WhatsApp\/|TelegramBot|Slackbot|preview/i',
            (string) $request->userAgent()
        );
    }

    private function withSafeHeaders(Response $response): Response
    {
        return $response->withHeaders([
            'Referrer-Policy' => 'no-referrer',
            'Cache-Control' => 'private, no-store',
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }
}
