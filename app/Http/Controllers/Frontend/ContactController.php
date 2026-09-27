<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\PortfolioContactMail;
use App\Models\Portfolio\ContactMessage;
use App\Support\Portfolio\PortfolioData;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

/**
 * Public contact form → stored in contact_messages and e-mailed over SMTP.
 *
 * Spam defence on top of the "contact" rate limiter: a hidden honeypot field
 * and an encrypted render timestamp (bots that post instantly or replay an
 * old form are rejected without telling them why).
 */
class ContactController extends Controller
{
    private const MIN_SECONDS = 3;

    private const MAX_AGE = 7200;

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $content = PortfolioData::layout()['content'];
        abort_unless(($content['contact_form_enabled'] ?? '1') === '1', 404);

        if ($this->looksLikeBot($request)) {
            // Pretend success so the bot learns nothing.
            return $this->respond($request, 'Terima kasih! Pesan Anda sudah terkirim.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[\d\s+\-()]*$/'],
            'subject' => ['nullable', 'string', 'max:150'],
            'budget' => ['nullable', 'string', 'max:60'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        // Links are the usual spam payload; a real brief rarely needs many.
        if (preg_match_all('#https?://#i', $data['message']) > 3) {
            throw ValidationException::withMessages(['message' => 'Pesan berisi terlalu banyak link.']);
        }

        $message = ContactMessage::create($data + [
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        Cache::forget('pf.unread');

        $to = $content['contact_email'] ?: config('mail.from.address');

        try {
            Mail::to($to)->send(new PortfolioContactMail($message));
            $message->forceFill(['mail_sent' => true])->save();
        } catch (\Throwable $e) {
            // The message is stored either way; the inbox in the admin shows it.
            Log::warning('Portfolio contact mail failed', ['id' => $message->id, 'error' => $e->getMessage()]);
        }

        return $this->respond($request, 'Terima kasih, ' . $data['name'] . '! Pesan Anda sudah terkirim, saya akan segera membalas.');
    }

    private function looksLikeBot(Request $request): bool
    {
        if (filled($request->input('website'))) {
            return true;
        }

        try {
            $renderedAt = (int) Crypt::decryptString((string) $request->input('_ts'));
        } catch (DecryptException) {
            return true;
        }

        $age = time() - $renderedAt;

        return $age < self::MIN_SECONDS || $age > self::MAX_AGE;
    }

    private function respond(Request $request, string $text): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $text]);
        }

        return redirect()->to(url('/') . '#contact')->with('contact_success', $text);
    }
}
