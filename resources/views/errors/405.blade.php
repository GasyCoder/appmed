@php
  $title = 'Méthode non autorisée';
  $subtitle = "Cette action n’est pas autorisée pour cette page.";
  $code = 405;
  $message = "La méthode HTTP utilisée n’est pas acceptée (GET/POST/PUT...).";
  $hint = "Revenez en arrière, rechargez la page, ou réessayez depuis l’interface (pas via l’URL directe).";
  $icon = '<svg class="h-5 w-5 text-gray-700 dark:text-gray-200" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01M5.6 5.6l12.8 12.8M7 3h10a4 4 0 014 4v10a4 4 0 01-4 4H7a4 4 0 01-4-4V7a4 4 0 014-4z"/>
          </svg>';
@endphp

@include('errors.layout', compact('title','subtitle','code','message','hint','icon'))
