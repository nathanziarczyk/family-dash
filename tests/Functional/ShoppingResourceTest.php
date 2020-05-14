<?php


namespace App\Test;


use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class ShoppingResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateShoppingList(){
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

        $client->request('POST', '/api/shopping_lists', [
            'headers' => ['Content-Type' => 'application/json'],
            'auth_bearer' => $this->getJwt(),
            'json' => [
                'title' => 'test Shopping list',
                'groep' => '/api/groups/1'
            ]
        ]);
        self::assertResponseIsSuccessful();

        $client->request('POST', '/api/shopping_list_items', [
            'headers' => ['Content-Type' => 'application/json'],
            'auth_bearer' => $this->getJwt(),
            'json' => [
                'title' => 'test Shopping list Item',
                'shoppingList' => '/api/shopping_lists/1',
                'user' => '/api/users/1'
            ]
        ]);
        self::assertResponseIsSuccessful();

    }

}