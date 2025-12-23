@php
  $title = 'Accès refusé';
  $subtitle = "Vous n’avez pas l’autorisation d’accéder à cette page.";
  $code = 403;
  $message = "Votre compte ne dispose pas des droits nécessaires pour cette action.";
  $hint = "Si vous pensez qu’il s’agit d’une erreur, contactez le support ou reconnectez-vous avec un compte autorisé.";
  $icon = '<svg class="h-5 w-5 text-gray-700 dark:text-gray-200" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 11V7a4 4 0 00-8 0v4m2 0h8m-9 0h10a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2z"/>
           </svg>';
@endphp

@include('errors.layout', compact('title','subtitle','code','message','hint','icon'))
