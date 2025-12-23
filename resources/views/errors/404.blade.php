@php
  $title = 'Page introuvable';
  $subtitle = "L’adresse demandée n’existe pas ou a été déplacée.";
  $code = 404;
  $message = "Impossible de trouver la page.";
  $hint = "Vérifiez le lien ou revenez à la page précédente.";
  $icon = '<svg class="h-5 w-5 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 2a7 7 0 00-4 12.74V17a1 1 0 001 1h6a1 1 0 001-1v-2.26A7 7 0 0012 2z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 18h6M10 22h4"/>
          </svg>';
@endphp

@include('errors.layout', compact('title','subtitle','code','message','hint','icon'))
