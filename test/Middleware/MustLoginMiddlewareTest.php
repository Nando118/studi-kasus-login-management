<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\App {
    function header(string $value)
    {
        echo $value;
    }
}

namespace Nando118\StudiKasus\PHP\LoginManagement\Middleware {

    use Nando118\StudiKasus\PHP\LoginManagement\Config\Database;
    use Nando118\StudiKasus\PHP\LoginManagement\Domain\Session;
    use Nando118\StudiKasus\PHP\LoginManagement\Domain\User;
    use Nando118\StudiKasus\PHP\LoginManagement\Repository\SessionRepository;
    use Nando118\StudiKasus\PHP\LoginManagement\Repository\UserRepository;
    use Nando118\StudiKasus\PHP\LoginManagement\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class MustLoginMiddlewareTest extends TestCase
    {
        private MustLoginMiddleware $loginMiddleware;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void
        {
            $this->loginMiddleware = new MustLoginMiddleware();
            putenv("mode=test");

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }

        public function testBeforeGuest()
        {
            $this->loginMiddleware->before();

            $this->expectOutputRegex("[Location: /users/login]");
        }

        public function testBeforeLoginUser()
        {
            $user = new User();
            $user->id = 'nando';
            $user->name = 'Nando';
            $user->password = 'nando';

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->loginMiddleware->before();
            $this->expectOutputString("");

        }
    }
}
