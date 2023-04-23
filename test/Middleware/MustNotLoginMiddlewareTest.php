<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\Middleware {
    require_once __DIR__ . "/../Helper/helper.php";

    use Nando118\StudiKasus\PHP\LoginManagement\Config\Database;
    use Nando118\StudiKasus\PHP\LoginManagement\Domain\Session;
    use Nando118\StudiKasus\PHP\LoginManagement\Domain\User;
    use Nando118\StudiKasus\PHP\LoginManagement\Repository\SessionRepository;
    use Nando118\StudiKasus\PHP\LoginManagement\Repository\UserRepository;
    use Nando118\StudiKasus\PHP\LoginManagement\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class MustNotLoginMiddlewareTest extends TestCase
    {
        private MustNotLoginMiddleware $mustNotLoginMiddleware;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void
        {
            $this->mustNotLoginMiddleware = new MustNotLoginMiddleware();
            putenv("mode=test");

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }

        public function testBeforeGuest()
        {
            $this->mustNotLoginMiddleware->before();

            $this->expectOutputString("");
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

            $this->mustNotLoginMiddleware->before();
            $this->expectOutputRegex("[Location: /]");

        }
    }
}
