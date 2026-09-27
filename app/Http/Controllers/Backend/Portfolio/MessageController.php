<?php

namespace App\Http\Controllers\Backend\Portfolio;

use App\Http\Controllers\Controller;
use App\Models\Portfolio\ContactMessage;
use App\Support\ActivityRecorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/** Inbox for the portfolio contact form. */
class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $like = '%' . addcslashes($search, '%_\\') . '%';

        $messages = ContactMessage::query()
            ->select(['id', 'name', 'email', 'subject', 'budget', 'mail_sent', 'read_at', 'created_at'])
            ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'ilike', $like)->orWhere('email', 'ilike', $like)->orWhere('subject', 'ilike', $like)))
            ->when($request->query('status') === 'unread', fn ($q) => $q->whereNull('read_at'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('backend.portfolio.message.index', [
            'messages' => $messages,
            'search' => $search,
            'unread' => ContactMessage::whereNull('read_at')->count(),
        ]);
    }

    public function show(ContactMessage $message): View
    {
        if ($message->read_at === null) {
            $message->forceFill(['read_at' => now()])->save();
            Cache::forget('pf.unread');
        }

        return view('backend.portfolio.message.show', ['message' => $message]);
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $before = $message->toArray();
        $message->delete();
        Cache::forget('pf.unread');

        ActivityRecorder::deleted('portfolio', 'Menghapus pesan dari ' . $before['email'], $before);

        return redirect()->route('pf.messages.index')->with('success', 'Pesan berhasil dihapus.');
    }
}
