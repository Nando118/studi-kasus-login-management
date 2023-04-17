<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\Controller;

use Nando118\StudiKasus\PHP\LoginManagement\Config\Database;
use Nando118\StudiKasus\PHP\LoginManagement\Domain\Session;
use Nando118\StudiKasus\PHP\LoginManagement\Domain\User;
use Nando118\StudiKasus\PHP\LoginManagement\Repository\SessionRepository;
use Nando118\StudiKasus\PHP\LoginManagement\Repository\UserRepository;
use Nando118\StudiKasus\PHP\LoginManagement\Service\SessionService;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
{
    private HomeController $homeController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->homeController = new HomeController();
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testGuest()
    {
        $this->homeController->index();

        $this->expectOutputRegex("[Login Management]");
    }

    public function testUserLogin()
    {
        $user = new User();
        $user->id = "nando";
        $user->name = "Nando";
        $user->password = "nando";
        $this->userRepository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->userId = $user->id;
        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->homeController->index();

        $this->expectOutputRegex("[Hi Nando]");
    }
}
