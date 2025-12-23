@php
  $title = 'Erreur interne du serveur';
  $subtitle = "Le serveur a rencontré un problème inattendu.";
  $code = 500;
  $message = "Une erreur technique est survenue pendant le traitement de votre demande.";
  $hint = "Réessayez dans quelques minutes. Si le problème persiste, contactez le support (Service Informatique).";
  $icon = '<svg class="h-5 w-5 text-gray-700 dark:text-gray-200" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 7a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V7z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 9h8M8 13h5"/>
           </svg>';
@endphp

@include('errors.layout', compact('title','subtitle','code','message','hint','icon'))
