<?php

use EragLaravelPwa\EragLaravelPwaServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\JetstreamServiceProvider::class,
    EragLaravelPwaServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
];
