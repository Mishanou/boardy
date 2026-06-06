<?php

use App\Providers\AppServiceProvider;
use Laravel\Passport\PassportServiceProvider;

return [
    AppServiceProvider::class,
    Laravel\Passport\PassportServiceProvider::class,
    PassportServiceProvider::class,
];
