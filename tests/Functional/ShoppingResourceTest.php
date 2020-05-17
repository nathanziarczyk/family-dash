<?php


namespace App\Test;


use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class ShoppingResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateShoppingList(){

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

        /* Test of user shopping list kan aanmaken */
        $client->request('POST', '/api/shopping_lists', [
            'headers' => ['Content-Type' => 'application/json'],
            'auth_bearer' => $this->getJwt(),
            'json' => [
                'title' => 'test Shopping list',
                'groep' => '/api/groups/1'
            ]
        ]);
        self::assertResponseIsSuccessful();

        /* Test of user shopping list item kan aanmaken */
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