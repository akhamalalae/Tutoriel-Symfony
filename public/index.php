<?php
use Symfony\Component\ErrorHandler\Debug;
use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';
Debug::enable();
return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
