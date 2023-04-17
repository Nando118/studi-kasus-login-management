<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\Controller;

use Nando118\StudiKasus\PHP\LoginManagement\App\View;
use Nando118\StudiKasus\PHP\LoginManagement\Config\Database;
use Nando118\StudiKasus\PHP\LoginManagement\Repository\SessionRepository;
use Nando118\StudiKasus\PHP\LoginManagement\Repository\UserRepository;
use Nando118\StudiKasus\PHP\LoginManagement\Service\SessionService;

class HomeController
{

    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    function index()
    {
        $user = $this->sessionService->current();

        if ($user == null) {
            View::render('Home/index', [
                "title" => "PHP Login Management"
            ]);
        } else {
            View::render('Home/dashboard', [
                "title" => "Dashboard",
                "user" => [
                    "name" => $user->name
                ]
            ]);
        }
    }

}