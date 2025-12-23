@php
  $title = 'Accès interdit';
  $subtitle = "Vous n’avez pas l’autorisation d’accéder à cette page.";
  $code = 403;
  $message = "Accès refusé.";
  $hint = "Si vous pensez qu’il s’agit d’une erreur, contactez l’administrateur.";

  $icon = '<svg class="h-5 w-5 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 20a6 6 0 0112 0"/>
          </svg>';
@endphp

@include('errors.layout', compact('title','subtitle','code','message','hint','icon'))
