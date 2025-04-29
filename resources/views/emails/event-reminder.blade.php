<x-mail::message>
# Event Reminder: {{ $event->title }}

This is a reminder that the event "{{ $event->title }}" is happening tomorrow.

Event Details:
- Date: {{ \Carbon\Carbon::parse($event->date)->format('l, j F Y') }}
- Time: {{ $event->time }}
- Location: {{ $event->location }}

Description:
{{ $event->description }}

Thank you for registering for this event.

Best regards,
{{ config('app.name') }}
</x-mail::message>