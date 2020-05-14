<?php


namespace App\Tests\Functional;


use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class NoteResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateNote()
    {
        $client = self::createClient();

        $client->request('POST', '/api/notes', [
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        self::assertResponseStatusCodeSame(401);

        $this->createUserAndLogIn($client, 'test', 'test');
        $client->request('GET', '/api/users', [
            'auth_bearer' => $this->getJwt()
        ]);
        self::assertResponseIsSuccessful();

        $this->createGroup($client, 'testGroup');
        self::assertResponseIsSuccessful();

        $client->request('POST', '/api/notes', [
            'auth_bearer' => $this->getJwt(),
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'test',
                'body' => 'lorem ipsum',
                'groep' => '/api/groups/1',
                'user' => '/api/users/1'
            ]
        ]);
        self::assertResponseIsSuccessful();

    }

}