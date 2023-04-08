<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\App;

use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{

    public function testRender()
    {
        View::render('Home/index', [
            'title' => 'PHP Login Management'
        ]);

        $this->expectOutputRegex('[Register]');
    }
}
