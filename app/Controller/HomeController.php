<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\Controller;

use Nando118\StudiKasus\PHP\LoginManagement\App\View;

class HomeController
{

    function index()
    {
        View::render('Home/index', [
            "title" => "PHP Login Management"
        ]);
    }

}