<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\Config;

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    // Test koneksi tidak null
    public function testGetConnection()
    {
        $connection = Database::getConnection();
        self::assertNotNull($connection);
        var_dump($connection);
    }

    // Test koneksi apakah mengembalikan objek yang sama atau tidak saat function dijalankan berulang kali
    // Karena saat menjalankan function getConnection akan membuat objek PDO dan mengembalikan ojek PDO lagi
    public function testGetConnectionSingleton()
    {
        $connection1 = Database::getConnection();
        $connection2 = Database::getConnection();
        self::assertSame($connection1, $connection2);
        var_dump($connection1);
        var_dump($connection2);
    }


}
