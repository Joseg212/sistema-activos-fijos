<?php

use App\Kernel;

ini_set('date.timezone', 'America/La_Paz');

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    setlocale(LC_MONETARY, 'es_ES');
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
