<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\Controller;

use Nando118\StudiKasus\PHP\LoginManagement\App\View;
use Nando118\StudiKasus\PHP\LoginManagement\Config\Database;
use Nando118\StudiKasus\PHP\LoginManagement\Exception\ValidationException;
use Nando118\StudiKasus\PHP\LoginManagement\Model\UserRegisterRequest;
use Nando118\StudiKasus\PHP\LoginManagement\Repository\UserRepository;
use Nando118\StudiKasus\PHP\LoginManagement\Service\UserService;

class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);
    }


    public function register()
    {
        View::render('User/register', [
            'title' => 'Register New User'
        ]);
    }

    public function postRegister()
    {
        $request = new UserRegisterRequest();
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->password = $_POST['password'];

        try {
            $this->userService->register($request);
            View::redirect('users/login');
        } catch (ValidationException $exception) {
            View::render('User/register', [
                'title' => 'Register New User',
                'error' => $exception->getMessage()
            ]);
        }
    }
}