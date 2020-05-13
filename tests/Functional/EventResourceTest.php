<?php


namespace App\Tests\Functional;


use App\Entity\User;
use App\Test\CustomApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class EventResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateEvent()
    {
        $client = self::createClient();

        $client->request('POST', '/api/events', [
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        self::assertResponseStatusCodeSame(401);

        $this->createUserAndLogIn($client,'test@task.com', 'test');
        self::assertResponseIsSuccessful();

        $this->createGroup($client, 'test');
        self::assertResponseIsSuccessful();

        $client->request('POST', '/api/events', [
            'auth_bearer' => $this->getJwt(),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'title' => 'test event',
                'description' => 'test',
                'start' => date('Y-m-d H:i:s'),
                'end' => date('Y-m-d H:i:s'),
                'groep' => '/api/groups/1'
            ]
        ]);
        self::assertResponseIsSuccessful();
    }
}