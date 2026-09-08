<!DOCTYPE html>
<html>
<head>
    <title>Kode Verifikasi</title>
</head>
<body>
    <h2>Halo {{ $user->name }},</h2>
    <p>Terima kasih telah mendaftar di SajiPOS.</p>
    <p>Kode verifikasi (OTP) Anda adalah:</p>
    <h1 style="color: #4CAF50; font-size: 32px; letter-spacing: 5px;">{{ $otpCode }}</h1>
    <p>Kode ini berlaku selama 10 menit. Jangan bagikan kode ini kepada siapapun.</p>
    <p>Jika Anda tidak merasa mendaftar, abaikan email ini.</p>
    <br>
    <p>Salam,<br>Tim SajiPOS</p>
</body>
</html>
