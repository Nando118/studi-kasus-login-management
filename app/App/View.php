<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\App;

class View
{

    public static function render(string $view, $model)
    {
        require __DIR__ . '/../View/' . $view . '.php';
    }

}