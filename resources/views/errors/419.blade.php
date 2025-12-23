@php
  $title = 'Page expirée';
  $subtitle = "Votre session a expiré (inactivité).";
  $code = 419;
  $message = "Le jeton de sécurité a expiré.";
  $hint = "Rechargez la page puis réessayez.";
  $icon = '<svg class="h-5 w-5 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>';
@endphp

@include('errors.layout', compact('title','subtitle','code','message','hint','icon'))
