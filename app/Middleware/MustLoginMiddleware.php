<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\Middleware;

use Nando118\StudiKasus\PHP\LoginManagement\App\View;
use Nando118\StudiKasus\PHP\LoginManagement\Config\Database;
use Nando118\StudiKasus\PHP\LoginManagement\Repository\SessionRepository;
use Nando118\StudiKasus\PHP\LoginManagement\Repository\UserRepository;
use Nando118\StudiKasus\PHP\LoginManagement\Service\SessionService;

class MustLoginMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $sessionRepository = new SessionRepository(Database::getConnection());
        $userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepository, $userRepository);

    }


    function before(): void
    {
        $user = $this->sessionService->current();
        if ($user == null){
            View::redirect('/users/login');
        }
    }

}