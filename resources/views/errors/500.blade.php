@php
  $title = 'Erreur serveur';
  $subtitle = "Une erreur interne s’est produite.";
  $code = 500;
  $message = "Impossible de traiter votre demande pour le moment.";
  $hint = "Réessayez plus tard. Si le problème persiste, contactez l’équipe technique.";
  $showReload = true;

  $icon = '<svg class="h-5 w-5 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>';
@endphp

@include('errors.layout', compact('title','subtitle','code','message','hint','icon','showReload'))
