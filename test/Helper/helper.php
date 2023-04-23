<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\App {
    function header(string $value)
    {
        echo $value;
    }
}

namespace Nando118\StudiKasus\PHP\LoginManagement\Service {
    function setcookie(string $name, string $value)
    {
        echo "$name : $value";
    }
}