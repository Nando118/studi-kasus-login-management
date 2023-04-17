<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\App {
    function header(string $value)
    {
        echo $value;
    }
}

namespace Nando118\StudiKasus\PHP\LoginManagement\Service {
    function setcookie(string $name, string $value)
    {
        echo "$name : $value";
    }
}

namespace Nando118\StudiKasus\PHP\LoginManagement\Controller {

    use Nando118\StudiKasus\PHP\LoginManagement\Config\Database;
    use Nando118\StudiKasus\PHP\LoginManagement\Domain\Session;
    use Nando118\StudiKasus\PHP\LoginManagement\Domain\User;
    use Nando118\StudiKasus\PHP\LoginManagement\Repository\SessionRepository;
    use Nando118\StudiKasus\PHP\LoginManagement\Repository\UserRepository;
    use Nando118\StudiKasus\PHP\LoginManagement\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void
        {
            $this->userController = new UserController();

            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();

            putenv("mode=test");
        }


        public function testRegister()
        {
            $this->userController->register();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
        }

        public function testPostRegisterSuccess()
        {
            $_POST['id'] = 'nando';
            $_POST['name'] = 'Nando';
            $_POST['password'] = 'asd123';

            $this->userController->postRegister();

            $this->expectOutputRegex("[Location: /users/login]");
        }

        public function testPostRegisterValidationError()
        {
            $_POST['id'] = 'nando';
            $_POST['name'] = '';
            $_POST['password'] = 'asd123';

            $this->userController->postRegister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id, Name, Password can not blank]");
        }

        public function testPostRegisterDuplicate()
        {
            $user = new User();
            $user->id = 'nando';
            $user->name = 'Nando';
            $user->password = 'asd123';

            $this->userRepository->save($user);

            $_POST['id'] = 'nando';
            $_POST['name'] = 'Nando';
            $_POST['password'] = 'asd123';

            $this->userController->postRegister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[User Id already exists]");
        }

        public function testLogin()
        {
            $this->userController->Login();

            $this->expectOutputRegex("[Sign On]");
        }

        public function testLoginSucess()
        {
            $user = new User();
            $user->id = 'nando';
            $user->name = 'Nando';
            $user->password = password_hash('nando', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['id'] = 'nando';
            $_POST['password'] = 'nando';

            $this->userController->postLogin();

            $this->expectOutputRegex("[S-LOGIN-MANAGEMENT-SESSION : ]");
        }

        public function testLoginValidateError()
        {
            $_POST['id'] = 'nando';
            $_POST['password'] = '';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Id, Password can not blank]");
        }

        public function testLoginUserNotFound()
        {
            $user = new User();
            $user->id = 'nando';
            $user->name = 'Nando';
            $user->password = password_hash('nando', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['id'] = 'asd';
            $_POST['password'] = 'asd';

            $this->userController->postLogin();

            $this->expectOutputRegex("[User not found!]");
        }

        public function testLoginWrongPassword()
        {
            $user = new User();
            $user->id = 'nando';
            $user->name = 'Nando';
            $user->password = password_hash('nando', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['id'] = 'nando';
            $_POST['password'] = 'asd';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Id or password is wrong]");
        }

        public function testLogout()
        {
            $user = new User();
            $user->id = 'nando';
            $user->name = 'Nando';
            $user->password = password_hash('nando', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->logout();

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[S-LOGIN-MANAGEMENT-SESSION : ]");
        }
    }
}
