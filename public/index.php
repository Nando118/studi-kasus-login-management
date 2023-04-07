<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Nando118\StudiKasus\PHP\LoginManagement\App\Router;
use Nando118\StudiKasus\PHP\LoginManagement\Controller\HomeController;
use Nando118\StudiKasus\PHP\LoginManagement\Controller\ProductController;
use Nando118\StudiKasus\PHP\LoginManagement\Middleware\AuthMiddleware;

Router::add('GET', '/products/([0-9a-zA-Z]*)/categories/([0-9a-zA-Z]*)', ProductController::class, 'categories');

Router::add('GET', '/', HomeController::class, 'index');
Router::add('GET', '/hello', HomeController::class, 'hello', [AuthMiddleware::class]);
Router::add('GET', '/world', HomeController::class, 'world', [AuthMiddleware::class]);
Router::add('GET', '/about', HomeController::class, 'about');

Router::run();