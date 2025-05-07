<!DOCTYPE html>
<html>
<head>
    <title>Tanggapan {{ $feedback->kategori }} {{ $feedback->created_at->format('d F Y H:i') }}</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Merriweather', serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }

        /* Header section */
        .header {
            background-color: #244031; /* Updated accent1 color */
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        /* Main content container */
        .feedback-container {
            background-color: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }

        /* Feedback header section */
        .feedback-header {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .feedback-header h3 {
            color: #244031; /* Updated accent1 color */
            margin: 0 0 10px 0;
            font-size: 20px;
        }

        .feedback-header p {
            color: #666;
            margin: 0;
            font-size: 14px;
        }

        /* Feedback content styles */
        .feedback-content {
            margin-bottom: 25px;
        }

        .feedback-content p {
            margin: 10px 0;
        }

        .feedback-content strong {
            color: #244031; /* Updated accent1 color */
        }

        /* Response section */
        .response {
            background-color: #F5F5F5;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #244031; /* Updated accent1 color */
        }

        .response p {
            margin: 10px 0;
        }

        .response strong {
            color: #244031; /* Updated accent1 color */
        }

        /* Footer section */
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            color: #666;
            font-size: 14px;
        }

        /* Accent colors */
        .accent-text {
            color: #C4A484; /* accent2 color from other components */
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Tanggapan untuk {{ $feedback->kategori }} Anda - {{ $feedback->created_at->format('d F Y H:i') }}</h2>
    </div>
    
    <div class="feedback-container">
        <div class="feedback-header">
            <h3>{{ $feedback->judul }}</h3>
            <p>Dikirim pada: {{ $feedback->created_at->format('d F Y H:i') }}</p>
        </div>

        <div class="feedback-content">
            <p><strong>Kategori:</strong> <span class="accent-text">{{ $feedback->kategori }}</span></p>
            <p><strong>{{ $feedback->kategori }} Anda:</strong></p>
            <p>{{ $feedback->isi }}</p>
        </div>

        <div class="response">
            <p><strong>Tanggapan dari Admin:</strong></p>
            <p>{{ $feedback->tanggapan }}</p>
        </div>
    </div>

    <div class="footer">
        <p>Terima kasih telah memberikan {{ strtolower($feedback->kategori) }} kepada kami.</p>
        <p>Desa {{ $profile->village->name }}</p>
        <p>Kecamatan {{ $profile->district->name }}, {{ $profile->regency->name }}</p>
        <p>Provinsi {{ $profile->province->name }}</p>
        <p>{{ $profile->email }} | {{ $profile->phone }}</p>
    </div>
</body>
</html>