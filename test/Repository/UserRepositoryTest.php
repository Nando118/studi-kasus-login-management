<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\Repository;

use Nando118\StudiKasus\PHP\LoginManagement\Config\Database;
use Nando118\StudiKasus\PHP\LoginManagement\Domain\User;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testSaveSuccess()
    {
        $user = new User();
        $user->id = 'nando';
        $user->name = 'nando';
        $user->password = 'nando';

        $this->userRepository->save($user);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }

    public function testFindByIdNotFound()
    {
        $user = $this->userRepository->findById("A001");
        self::assertNull($user);
    }

    public function testUpdateSuccess()
    {
        $user = new User();
        $user->id = "A001";
        $user->name = "Fernando";
        $user->password = "ASD123";

        $this->userRepository->save($user);

        $user->name = "Fernando Verdy";
        $user->password = "fernando";
        $this->userRepository->update($user);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }

}
