<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\Controller {
    require_once __DIR__ . "/../Helper/helper.php";

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

        public function testUpdateProfile()
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

            $this->userController->updateProfile();

            $this->expectOutputRegex("[nando]");
        }

        public function testPostUpdateProfileSuccess()
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

            $_POST['name'] = "Fernando";
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById("nando");
            self::assertEquals("Fernando", $result->name);
        }

        public function testPostUpdateProfileValidationError()
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

            $_POST['name'] = "";
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Id, Name can not blank]");
        }

        public function testUpdatePassword()
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

            $this->userController->updatePassword();

            $this->expectOutputRegex("[Change Password]");
        }

        public function testUpdatePasswordSuccess()
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

            $_POST['oldPassword'] = "nando";
            $_POST['newPassword'] = "baru";
            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById($user->id);

            self::assertTrue(password_verify("baru", $result->password));
        }

        public function testUpdatePasswordValidationError()
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

            $_POST['oldPassword'] = "";
            $_POST['newPassword'] = "";
            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Id, Old Password, New Password can not blank]");
        }

        public function testWrongOldPassword()
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

            $_POST['oldPassword'] = "salah";
            $_POST['newPassword'] = "budi";
            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Old password is wrong]");
        }


    }
}
