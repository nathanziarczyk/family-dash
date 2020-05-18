<?php


namespace App\Tests\Functional;


use App\Entity\User;
use App\Test\CustomApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class EventResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testGetEvent()
    {
        $client = self::createClient();

        /* Test of een Event kan aangemaakt worden zonder in te loggen, mag niet mogelijk zijn */
        $client->request('POST', '/api/events', [
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        self::assertResponseStatusCodeSame(401);

        /* User reg, login en groep aanmaken */
        $this->createUsersAndLogInAndMakeGroup($client,'test', 'test', 'test-group');
        self::assertResponseIsSuccessful();

        /* event aanmaken */
        $client->request('POST', '/api/events', [
            'auth_bearer' => $this->getJwtAuth(),
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

        /* Deze gebruiker moet het evenement kunnen opvragen */
        $client->request('GET', '/api/events/1', [
            'auth_bearer' => $this->getJwtAuth(),
        ]);
        self::assertResponseIsSuccessful();

        /* Deze gebruiker mag het evenement niet kunnen opvragen */
        $client->request('GET', '/api/events/1', [
            'auth_bearer' => $this->getJwtNoAuth(),
        ]);
        self::assertResponseStatusCodeSame(403);
    }

    public function testCreateEvent()
    {
        $client = self::createClient();

        /* Test of een Event kan aangemaakt worden zonder in te loggen, mag niet mogelijk zijn */
        $client->request('POST', '/api/events', [
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        self::assertResponseStatusCodeSame(401);

        /* User reg, login en groep aanmaken */
        $this->createUsersAndLogInAndMakeGroup($client,'test', 'test', 'test-group');
        self::assertResponseIsSuccessful();

        /* Testen of user een event kan aanmaken na login */
        $client->request('POST', '/api/events', [
            'auth_bearer' => $this->getJwtAuth(),
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

        /* deze user mag geen event kunnen aanmaken */
        $client->request('POST', '/api/events', [
            'auth_bearer' => $this->getJwtNoAuth(),
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
        self::assertResponseStatusCodeSame(403);

    }
}