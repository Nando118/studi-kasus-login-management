<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Nando118\StudiKasus\PHP\LoginManagement\App\Router;
use Nando118\StudiKasus\PHP\LoginManagement\Config\Database;
use Nando118\StudiKasus\PHP\LoginManagement\Controller\HomeController;
use Nando118\StudiKasus\PHP\LoginManagement\Controller\UserController;

Database::getConnection('prod');

//Home Controller
Router::add('GET', '/', HomeController::class, 'index', []);

//USer Controller
Router::add('GET', '/users/register', UserController::class, 'register', []);
Router::add('POST', '/users/register', UserController::class, 'postRegister', []);

Router::run();