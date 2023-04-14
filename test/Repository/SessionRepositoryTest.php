<?php

namespace Nando118\StudiKasus\PHP\LoginManagement\Repository;

use Nando118\StudiKasus\PHP\LoginManagement\Config\Database;
use Nando118\StudiKasus\PHP\LoginManagement\Domain\Session;
use PHPUnit\Framework\TestCase;

class SessionRepositoryTest extends TestCase
{
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();
    }

    public function testSaveSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = 'nando';

        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findById($session->id);

        self::assertEquals($session->id, $result->id);
        self::assertEquals($session->userId, $result->userId);
    }

    public function testDeleteByIdSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = 'nando';

        $this->sessionRepository->save($session);


        $this->sessionRepository->deleteById($session->id);

        $result = $this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }

    public function testFindByIdNotFound()
    {
        $result = $this->sessionRepository->findById('ABCAD');
        self::assertNull($result);
    }
}
