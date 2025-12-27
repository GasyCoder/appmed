<x-mail::message>
@php
  /** @var \App\Models\Announcement $announcement */
  $title = $announcement->title;
  $body  = (string) $announcement->body;

  // ✅ défaut bouton
  $btnLabel = $announcement->action_label ?: 'En savoir plus';

  // ✅ fallback URL pour avoir toujours un bouton (à adapter si tu as une route dédiée)
  $btnUrl = $announcement->action_url ?: config('app.url');
@endphp

# Bonjour{{ !empty($receiverName) ? ' '.$receiverName : '' }},

## {{ $title }}

{!! nl2br(e(strip_tags($body))) !!}

<x-mail::button :url="$btnUrl">
    {{ $btnLabel }}
</x-mail::button>

Merci,  
{{ config('app.name') }}
</x-mail::message>
