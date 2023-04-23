<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\Service;
require_once __DIR__ . "/../Helper/helper.php";

use Nando118\StudiKasus\PHP\LoginManagement\Config\Database;
use Nando118\StudiKasus\PHP\LoginManagement\Domain\Session;
use Nando118\StudiKasus\PHP\LoginManagement\Domain\User;
use Nando118\StudiKasus\PHP\LoginManagement\Repository\SessionRepository;
use Nando118\StudiKasus\PHP\LoginManagement\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class SessionServiceTest extends TestCase
{
    private SessionService $sessionService;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = 'nando';
        $user->name = 'nando';
        $user->password = 'nando';

        $this->userRepository->save($user);
    }

    public function testCreate()
    {
        $session = $this->sessionService->create('nando');

        $this->expectOutputRegex("[S-LOGIN-MANAGEMENT-SESSION : $session->id]");

        $result = $this->sessionRepository->findById($session->id);

        self::assertEquals("nando", $result->userId);
    }

    public function testDestroy()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = 'nando';

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->sessionService->destroy();

        $this->expectOutputRegex('[S-LOGIN-MANAGEMENT-SESSION : ]');

        $result = $this->sessionRepository->findById($session->id);

        self::assertNull($result);
    }

    public function testCurrent()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = 'nando';

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $user = $this->sessionService->current();

        self::assertEquals($session->userId, $user->id);
    }
}
