<?php


namespace App\Tests\Functional;


use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class GroupResourceTest extends CustomApiTestCase
{

    use ReloadDatabaseTrait;

    public function testCreateGroup()
    {
        $client = self::createClient();

        /* Users reg, login en groep aanmaken */
        $this->createUsersAndLogInAndMakeGroup($client, 'test', 'test', 'test-group');
        self::assertResponseIsSuccessful();

    }

    public function testGetGroup()
    {
        $client = self::createClient();

        /* Users reg, login en groep aanmaken */
        $this->createUsersAndLogInAndMakeGroup($client, 'test', 'test', 'test-group');
        self::assertResponseIsSuccessful();

        /* deze user mag de groep opvragen */
        $client->request('GET', '/api/groups/1', [
            'auth_bearer' => $this->getJwtAuth(),
        ]);
        self::assertResponseIsSuccessful();

        /* deze user mag de groep niet opvragen */
        $client->request('GET', '/api/groups/1', [
            'auth_bearer' => $this->getJwtNoAuth(),
        ]);
        self::assertResponseStatusCodeSame(403);
    }

}