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

        $this->createUserAndLogInAndMakeGroup($client, 'test', 'test', 'test-group');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/api/users', [
            'auth_bearer' => $this->getJwt()
        ]);
        self::assertResponseIsSuccessful();

        $client->request('POST', '/api/notes', [
            'auth_bearer' => $this->getJwt(),
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'test',
                'body' => '<p>This is a paragraph.</p>
                           <p>This is another paragraph.</p>',
                'groep' => '/api/groups/1',
                'user' => '/api/users/1'
            ]
        ]);
        self::assertResponseIsSuccessful();

    }

}