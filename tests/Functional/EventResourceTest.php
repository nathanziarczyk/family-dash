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

        $client->request('POST', '/api/events', [
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        self::assertResponseStatusCodeSame(400);

        $this->createUserAndLogIn($client,'test@task.com', 'test');
        self::assertResponseIsSuccessful();

        dd($client->request('POST', '/api/events', [
            'headers' => [
                'Content-Type' => 'application/json',
                'auth_bearer' => 'Bearer '.$this->getJwt()
            ],
            'json' => [
                'title' => 'test event',
                'description' => 'test',
                'start' => date('Y-m-d H:i:s'),
                'end' => date('Y-m-d H:i:s'),
                'groep' => '/api/groups/1'
            ]
        ]));
        self::assertResponseStatusCodeSame(400);
    }
}