<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesan baru dari portfolio</title>
</head>
<body style="margin:0;padding:24px;background:#0b0b14;font-family:Segoe UI,Arial,sans-serif;color:#e6e6f0;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px;margin:0 auto;background:#12121e;border:1px solid #26263a;border-radius:14px;overflow:hidden;">
        <tr>
            <td style="padding:22px 28px;background:linear-gradient(90deg,#e11d48,#2563eb);color:#fff;">
                <div style="font-size:12px;letter-spacing:2px;text-transform:uppercase;opacity:.85;">Portfolio · Pesan baru</div>
                <div style="font-size:20px;font-weight:700;margin-top:4px;">{{ $contact->subject ?: 'Permintaan project baru' }}</div>
            </td>
        </tr>
        <tr>
            <td style="padding:24px 28px;">
                <table role="presentation" width="100%" style="font-size:14px;line-height:1.6;">
                    <tr><td style="color:#8b8ba7;width:110px;">Nama</td><td>{{ $contact->name }}</td></tr>
                    <tr><td style="color:#8b8ba7;">Email</td><td><a href="mailto:{{ $contact->email }}" style="color:#60a5fa;">{{ $contact->email }}</a></td></tr>
                    @if ($contact->phone)
                        <tr><td style="color:#8b8ba7;">Telepon</td><td>{{ $contact->phone }}</td></tr>
                    @endif
                    @if ($contact->budget)
                        <tr><td style="color:#8b8ba7;">Budget</td><td>{{ $contact->budget }}</td></tr>
                    @endif
                </table>
                <div style="margin-top:18px;padding:16px 18px;background:#0b0b14;border-radius:10px;border:1px solid #26263a;font-size:14px;line-height:1.7;white-space:pre-line;">{{ $contact->message }}</div>
                <p style="margin-top:20px;font-size:12px;color:#6b6b85;">Balas email ini untuk langsung menjawab {{ $contact->name }}. Dikirim {{ $contact->created_at->format('d M Y H:i') }} dari IP {{ $contact->ip }}.</p>
            </td>
        </tr>
    </table>
</body>
</html>
