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

        /* Test of een Event kan aangemaakt worden zonder in te loggen, mag niet mogelijk zijn */
        $client->request('POST', '/api/events', [
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        self::assertResponseStatusCodeSame(401);

        /* User reg, login en groep aanmaken */
        $this->createUserAndLogInAndMakeGroup($client,'test@task.com', 'test', 'test-group');
        self::assertResponseIsSuccessful();

        /* Testen of user een event kan aanmaken na login */
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