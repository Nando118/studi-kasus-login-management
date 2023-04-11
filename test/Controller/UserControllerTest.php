<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\App {
    function header(string $value) {
        echo $value;
    }
}

namespace Nando118\StudiKasus\PHP\LoginManagement\Controller {

    use Nando118\StudiKasus\PHP\LoginManagement\Config\Database;
    use Nando118\StudiKasus\PHP\LoginManagement\Domain\User;
    use Nando118\StudiKasus\PHP\LoginManagement\Repository\UserRepository;
    use PHPUnit\Framework\TestCase;

    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        private UserRepository $userRepository;

        protected function setUp(): void
        {
            $this->userController = new UserController();

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

            $this->expectOutputRegex("[Location: users/login]");
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
    }
}
