<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\Service;

use Nando118\StudiKasus\PHP\LoginManagement\Config\Database;
use Nando118\StudiKasus\PHP\LoginManagement\Domain\User;
use Nando118\StudiKasus\PHP\LoginManagement\Exception\ValidationException;
use Nando118\StudiKasus\PHP\LoginManagement\Model\UserLoginRequest;
use Nando118\StudiKasus\PHP\LoginManagement\Model\UserPasswordUpdateRequest;
use Nando118\StudiKasus\PHP\LoginManagement\Model\UserProfileUpdateRequest;
use Nando118\StudiKasus\PHP\LoginManagement\Model\UserRegisterRequest;
use Nando118\StudiKasus\PHP\LoginManagement\Repository\SessionRepository;
use Nando118\StudiKasus\PHP\LoginManagement\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $connection = Database::getConnection();
        $this->sessionRepository = new SessionRepository($connection);
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testRegisterSuccess()
    {
        $request = new UserRegisterRequest();
        $request->id = "A001";
        $request->name = "Fernando";
        $request->password = "ASD123";

        $response = $this->userService->register($request);

        self::assertEquals($request->id, $response->user->id);
        self::assertEquals($request->name, $response->user->name);
        self::assertNotEquals($request->password, $response->user->password);

        self::assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testRegisterFailed()
    {
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "";
        $request->name = "";
        $request->password = "";

        $this->userService->register($request);
    }

    public function testRegisterDuplicate()
    {
        $user = new User();
        $user->id = "A001";
        $user->name = "Fernando";
        $user->password = "ASD123";

        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "A001";
        $request->name = "Fernando";
        $request->password = "ASD123";

        $this->userService->register($request);
    }

    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = "A003";
        $request->password = "asd123";

        $this->userService->login($request);
    }

    public function testLoginWrongPassword()
    {
        $user = new User();
        $user->id = "nando";
        $user->name = "nando";
        $user->password = password_hash("nando", PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = "nando";
        $request->password = "asdasd";

        $this->userService->login($request);
    }

    public function testLoginSuccess()
    {
        $user = new User();
        $user->id = "nando";
        $user->name = "nando";
        $user->password = password_hash("nando", PASSWORD_BCRYPT);
        $this->userRepository->save($user);

//        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = "nando";
        $request->password = "nando";

        $response = $this->userService->login($request);

        self::assertEquals($request->id, $response->user->id);
        self::assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testUpdateSuccess()
    {
        $user = new User();
        $user->id = "nando";
        $user->name = "Nando";
        $user->password = password_hash("nando", PASSWORD_BCRYPT);

        $this->userRepository->save($user);

        $request = new UserProfileUpdateRequest();
        $request->id = "nando";
        $request->name = "Fernando";

        $this->userService->updateProfile($request);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($request->name, $result->name);
    }

    public function testUpdateValidationError()
    {
        $this->expectException(ValidationException::class);

        $request = new UserProfileUpdateRequest();
        $request->id = "";
        $request->name = "";

        $this->userService->updateProfile($request);
    }

    public function testUpdateNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserProfileUpdateRequest();
        $request->id = "nando";
        $request->name = "Fernando";

        $this->userService->updateProfile($request);
    }

    public function testUpdatePasswordSuccess()
    {
        $user = new User();
        $user->id = "nando";
        $user->name = "Nando";
        $user->password = password_hash("nando", PASSWORD_BCRYPT);

        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->id = "nando";
        $request->oldPassword = "nando";
        $request->newPassword = "katasandi";

        $this->userService->updatePassword($request);

        $result = $this->userRepository->findById($user->id);
        self::assertTrue(password_verify($request->newPassword, $result->password));
    }

    public function testUpdatePasswordValidationError()
    {
        $this->expectException(ValidationException::class);

        $request = new UserPasswordUpdateRequest();
        $request->id = "nando";
        $request->oldPassword = "";
        $request->newPassword = "";

        $this->userService->updatePassword($request);
    }

    public function testUpdatePaswordWrongOldPassword()
    {
        $this->expectException(ValidationException::class);

        $user = new User();
        $user->id = "nando";
        $user->name = "Nando";
        $user->password = password_hash("nando", PASSWORD_BCRYPT);

        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->id = "nando";
        $request->oldPassword = "Nando";
        $request->newPassword = "katasandi";

        $this->userService->updatePassword($request);
    }

    public function testUpdatePasswordUserNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserPasswordUpdateRequest();
        $request->id = "nando2";
        $request->oldPassword = "nando";
        $request->newPassword = "katasandi";

        $this->userService->updatePassword($request);
    }


}
