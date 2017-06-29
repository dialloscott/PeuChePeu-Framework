<?php

use App\Registration\Controller\RegistrationController;

return [
    RegistrationController::class => \DI\object()
        ->methodParameter('register', 'request', \DI\get('request'))
        ->methodParameter('register', 'userTable', \DI\get(\App\Auth\Table\UserTable::class))
];
