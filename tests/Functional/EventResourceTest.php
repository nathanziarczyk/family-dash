<?php


namespace App\Tests\Functional;


use App\Entity\User;
use App\Test\CustomApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class EventResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateTask()
    {
        $client = self::createClient();

        $client->request('POST', '/api/events');
        self::assertResponseStatusCodeSame(401);

        $this->createUserAndLogIn($client,'test@task.com', 'test');
        self::assertResponseIsSuccessful();

        $client->request('POST', '/api/events');
        self::assertResponseStatusCodeSame(400);
    }
}