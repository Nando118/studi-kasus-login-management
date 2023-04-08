<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Nando118\StudiKasus\PHP\LoginManagement\App\Router;
use Nando118\StudiKasus\PHP\LoginManagement\Controller\HomeController;

Router::add('GET', '/', HomeController::class, 'index', []);

Router::run();