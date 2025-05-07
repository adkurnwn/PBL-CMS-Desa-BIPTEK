<!DOCTYPE html>
<html>
<head>
    <title>Event Reminder: {{ $event->title }}</title>
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
            background-color: #244031;
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
        .event-container {
            background-color: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }

        /* Event header section */
        .event-header {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .event-header h3 {
            color: #244031;
            margin: 0 0 10px 0;
            font-size: 20px;
        }

        /* Event details section */
        .event-details {
            background-color: #F5F5F5;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #244031;
            margin-bottom: 25px;
        }

        .event-details p {
            margin: 10px 0;
        }

        .event-details strong {
            color: #244031;
        }

        /* Description section */
        .event-description {
            margin-bottom: 25px;
        }

        .event-description h4 {
            color: #244031;
            margin: 0 0 10px 0;
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
            color: #C4A484;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Event Reminder</h2>
    </div>
    
    <div class="event-container">
        <div class="event-header">
            <h3>{{ $event->title }}</h3>
        </div>

        <div class="event-details">
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($event->date)->format('l, j F Y') }}</p>
            <p><strong>Waktu:</strong> {{ $event->time }}</p>
            <p><strong>Lokasi:</strong> {{ $event->location }}</p>
        </div>

        <div class="event-description">
            <h4>Deskripsi Event:</h4>
            <p>{{ $event->description }}</p>
        </div>

        <p class="accent-text">Terima kasih telah mendaftar untuk event ini.</p>
    </div>

    <div class="footer">
        <p>{{ config('app.name') }}</p>
        <p>© 2025 Powered by PT BIPTEK Tokodata Indonesia</p>
    </div>
</body>
</html>