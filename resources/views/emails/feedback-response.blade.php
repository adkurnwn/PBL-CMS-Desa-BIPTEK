<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .feedback-container {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .feedback-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .response {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <h2>Tanggapan untuk Feedback Anda</h2>
    
    <div class="feedback-container">
        <div class="feedback-header">
            <h3>{{ $feedback->judul }}</h3>
            <p>Dikirim pada: {{ $feedback->created_at->format('d F Y H:i') }}</p>
        </div>

        <div class="feedback-content">
            <p><strong>Kategori:</strong> {{ $feedback->kategori }}</p>
            <p><strong>Feedback Anda:</strong></p>
            <p>{{ $feedback->isi }}</p>
        </div>

        <div class="response">
            <p><strong>Tanggapan dari Admin:</strong></p>
            <p>{{ $feedback->tanggapan }}</p>
        </div>
    </div>

    <p>Terima kasih telah memberikan feedback kepada kami.</p>
</body>
</html>