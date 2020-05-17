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

        /* Test of een Note kan aangemaakt worden zonder in te loggen, mag niet mogelijk zijn */
        $client->request('POST', '/api/notes', [
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        self::assertResponseStatusCodeSame(401);

        /* User reg, login en groep aanmaken */
        $this->createUserAndLogInAndMakeGroup($client, 'test', 'test', 'test-group');
        self::assertResponseIsSuccessful();

        /* Testen of geregistreerde user met JWT data kan opvragen */
        $client->request('GET', '/api/users', [
            'auth_bearer' => $this->getJwt()
        ]);
        self::assertResponseIsSuccessful();

        /* Testen of geregistreerde user Note kan aanmaken na inloggen */
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