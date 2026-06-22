<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $subject ?? 'SIAKAD' }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: #fff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { border-bottom: 2px solid #4e73df; padding-bottom: 12px; margin-bottom: 20px; }
        .header h2 { color: #4e73df; margin: 0; }
        .content { color: #333; line-height: 1.7; white-space: pre-wrap; }
        .footer { margin-top: 30px; padding-top: 12px; border-top: 1px solid #eee; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>SIAKAD — Sistem Informasi Akademik</h2>
        </div>
        <p>Halo, <strong>{{ $nama }}</strong></p>
        <div class="content">{{ $body }}</div>
        <div class="footer">
            Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.
        </div>
    </div>
</body>
</html>
