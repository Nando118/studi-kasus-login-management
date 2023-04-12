<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\Service;

use mysql_xdevapi\Exception;
use Nando118\StudiKasus\PHP\LoginManagement\Config\Database;
use Nando118\StudiKasus\PHP\LoginManagement\Domain\User;
use Nando118\StudiKasus\PHP\LoginManagement\Exception\ValidationException;
use Nando118\StudiKasus\PHP\LoginManagement\Model\UserLoginRequest;
use Nando118\StudiKasus\PHP\LoginManagement\Model\UserLoginResponse;
use Nando118\StudiKasus\PHP\LoginManagement\Model\UserRegisterRequest;
use Nando118\StudiKasus\PHP\LoginManagement\Model\UserRegisterResponse;
use Nando118\StudiKasus\PHP\LoginManagement\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        $this->validateUserRegistrationRequest($request);

        $user = $this->userRepository->findById($request->id);
        if ($user != null) {
            throw new ValidationException("User Id already exists");
        }

        try {
            Database::beginTransaction();

            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $response = new UserRegisterResponse();
            $response->user = $user;

            Database::commitTransaction();

            return $response;
        } catch (\Exception $exception) {
            Database::rollBackTransaction();
            throw $exception;
        }
    }

    private function validateUserRegistrationRequest(UserRegisterRequest $request)
    {
        if ($request->id == null || $request->name == null || $request->password == null
            || trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == "") {
            throw new ValidationException("Id, Name, Password can not blank");
        }
    }

    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->validateUserLoginRequest($request);

        $user = $this->userRepository->findById($request->id);
        if ($user == null) {
            throw new ValidationException("Id or password is wrong");
        }

        if (password_verify($request->password, $user->password)){
            $response = new UserLoginResponse();
            $response->user = $user;
            return $response;
        }else{
            throw new ValidationException("Id or password is wrong");
        }
    }

    private function validateUserLoginRequest(UserLoginRequest $request)
    {
        if ($request->id == null || $request->password == null
            || trim($request->id) == "" || trim($request->password) == "") {
            throw new ValidationException("Id, Password can not blank");
        }
    }
}