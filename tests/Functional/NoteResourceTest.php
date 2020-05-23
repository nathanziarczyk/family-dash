<?php


namespace App\Tests\Functional;


use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class NoteResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testGetNotes()
    {
        $client = self::createClient();

        /* Test of een Note kan aangemaakt worden zonder in te loggen, mag niet mogelijk zijn */
        $client->request('POST', '/api/notes', [
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        self::assertResponseStatusCodeSame(401);

        /* Users reg, login en groep aanmaken */
        $this->createUsersAndLogInAndMakeGroup($client, 'test', 'test', 'test-group');
        self::assertResponseIsSuccessful();

        /* Testen of geregistreerde user Note kan aanmaken na inloggen */
        $client->request('POST', '/api/notes', [
            'auth_bearer' => $this->getJwtAuth(),
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

        /* deze user moet data kunnen opvragen */
        $client->request('GET', '/api/notes/1', [
            'auth_bearer' => $this->getJwtAuth(),
        ]);
        self::assertResponseIsSuccessful();

        /* deze user mag geen data kunnen opvragen */
        $client->request('GET', '/api/notes/1', [
            'auth_bearer' => $this->getJwtNoAuth(),
        ]);
        self::assertResponseStatusCodeSame(403);
    }

    public function testCreateNote()
    {
        $client = self::createClient();

        /* Test of een Note kan aangemaakt worden zonder in te loggen, mag niet mogelijk zijn */
        $client->request('POST', '/api/notes', [
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        self::assertResponseStatusCodeSame(401);

        /* User reg, login en groep aanmaken */
        $this->createUsersAndLogInAndMakeGroup($client, 'test', 'test', 'test-group');
        self::assertResponseIsSuccessful();

        /* Testen of geregistreerde user Note kan aanmaken na inloggen */
        $client->request('POST', '/api/notes', [
            'auth_bearer' => $this->getJwtAuth(),
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'test',
                'body' => '<p>This is a paragraph.</p>
                           <p>This is another paragraph.</p>',
                'groep' => '/api/groups/1'
            ]
        ]);
        self::assertResponseIsSuccessful();

        /* Deze user mag geen notes kunnen aanmaken */
        $client->request('POST', '/api/notes', [
            'auth_bearer' => $this->getJwtNoAuth(),
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'test',
                'body' => '<p>This is a paragraph.</p>
                           <p>This is another paragraph.</p>',
                'groep' => '/api/groups/1',
            ]
        ]);
        self::assertResponseStatusCodeSame(403);

    }

    public function testUpdateNote()
    {
        $client = self::createClient();

        /* User reg, login en groep aanmaken */
        $this->createUsersAndLogInAndMakeGroup($client, 'test', 'test', 'test-group');
        self::assertResponseIsSuccessful();

        /* note aanmaken */
        $client->request('POST', '/api/notes', [
            'auth_bearer' => $this->getJwtAuth(),
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

        /* owner moet note kunnen wijzigen */
        $client->request('PUT', '/api/notes/1', [
            'auth_bearer' => $this->getJwtAuth(),
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'wijzig'
            ]
        ]);
        self::assertResponseIsSuccessful();

        /* niet owner mag niet kunnen wijzigen */
        $client->request('PUT', '/api/notes/1', [
            'auth_bearer' => $this->getJwtNoAuth(),
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'wijzig'
            ]
        ]);
        self::assertResponseStatusCodeSame(403);

    }

    public function testDeleteNote()
    {
        $client = self::createClient();

        /* User reg, login en groep aanmaken */
        $this->createUsersAndLogInAndMakeGroup($client, 'test', 'test', 'test-group');
        self::assertResponseIsSuccessful();

        /* note aanmaken */
        $client->request('POST', '/api/notes', [
            'auth_bearer' => $this->getJwtAuth(),
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

        /* niet owner mag niet kunnen verwijderen */
        $client->request('DELETE', '/api/notes/1', [
            'auth_bearer' => $this->getJwtNoAuth(),
        ]);
        self::assertResponseStatusCodeSame(403);

        /* owner moet note kunnen verwijderen */
        $client->request('DELETE', '/api/notes/1', [
            'auth_bearer' => $this->getJwtAuth(),
        ]);
        self::assertResponseIsSuccessful();

    }

}