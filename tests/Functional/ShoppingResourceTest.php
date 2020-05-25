<?php


namespace App\Test;


use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class ShoppingResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testGetShopping(){
        $client = self::createClient();

        /* Test of een Note kan aangemaakt worden zonder in te loggen, mag niet mogelijk zijn */
        $client->request('POST', '/api/notes', [
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        self::assertResponseStatusCodeSame(401);

        /* User reg, login en groep aanmaken */
        $this->createUsersAndLogInAndMakeGroup($client, 'test', 'test', 'test-group');
        self::assertResponseIsSuccessful();

        /* Test of user shopping list kan aanmaken */
        $client->request('POST', '/api/shopping_lists', [
            'headers' => ['Content-Type' => 'application/json'],
            'auth_bearer' => $this->getJwtAuth(),
            'json' => [
                'title' => 'test Shopping list',
                'groep' => '/api/groups/1'
            ]
        ]);
        self::assertResponseIsSuccessful();

        /* Test of user shopping list item kan aanmaken */
        $client->request('POST', '/api/shopping_list_items', [
            'headers' => ['Content-Type' => 'application/json'],
            'auth_bearer' => $this->getJwtAuth(),
            'json' => [
                'title' => 'test Shopping list Item',
                'shoppingList' => '/api/shopping_lists/1',
                'user' => '/api/users/1'
            ]
        ]);
        self::assertResponseIsSuccessful();

        /* deze user mag wel items kunnen ophalen */
        $client->request('GET', '/api/shopping_lists/1', [
            'auth_bearer' => $this->getJwtAuth(),
        ]);
        self::assertResponseIsSuccessful();

        /* deze user mag geen items kunnen ophalen */
        $client->request('GET', '/api/shopping_lists/1', [
            'auth_bearer' => $this->getJwtNoAuth(),
        ]);
        self::assertResponseStatusCodeSame(403);

    }

    public function testCreateShoppingList(){

        $client = self::createClient();

        /* Test of een Note kan aangemaakt worden zonder in te loggen, mag niet mogelijk zijn */
        $client->request('POST', '/api/notes', [
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        self::assertResponseStatusCodeSame(401);

        /* User reg, login en groep aanmaken */
        $this->createUsersAndLogInAndMakeGroup($client, 'test', 'test', 'test-group');
        self::assertResponseIsSuccessful();

        /* Test of user shopping list kan aanmaken */
        $client->request('POST', '/api/shopping_lists', [
            'headers' => ['Content-Type' => 'application/json'],
            'auth_bearer' => $this->getJwtAuth(),
            'json' => [
                'title' => 'test Shopping list',
                'groep' => '/api/groups/1'
            ]
        ]);
        self::assertResponseIsSuccessful();

        /* Test of user shopping list item kan aanmaken */
        $client->request('POST', '/api/shopping_list_items', [
            'headers' => ['Content-Type' => 'application/json'],
            'auth_bearer' => $this->getJwtAuth(),
            'json' => [
                'title' => 'test Shopping list Item',
                'shoppingList' => '/api/shopping_lists/1',
            ]
        ]);
        self::assertResponseIsSuccessful();

        /* deze user mag geen list kunnen aanmaken */
        $client->request('POST', '/api/shopping_list_items', [
            'headers' => ['Content-Type' => 'application/json'],
            'auth_bearer' => $this->getJwtNoAuth(),
            'json' => [
                'title' => 'test Shopping list Item',
                'shoppingList' => '/api/shopping_lists/1',
                'user' => '/api/users/2'
            ]
        ]);
        self::assertResponseStatusCodeSame(403);

        /* deze user mag geen items kunnen toevoegen aan lijst */
        $client->request('POST', '/api/shopping_list_items', [
            'headers' => ['Content-Type' => 'application/json'],
            'auth_bearer' => $this->getJwtNoAuth(),
            'json' => [
                'title' => 'test Shopping list Item',
                'shoppingList' => '/api/shopping_lists/1',
            ]
        ]);
        self::assertResponseStatusCodeSame(403);
    }

    public function testDeleteItem() {
        $client = self::createClient();

        /* Test of een Note kan aangemaakt worden zonder in te loggen, mag niet mogelijk zijn */
        $client->request('POST', '/api/notes', [
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        self::assertResponseStatusCodeSame(401);

        /* User reg, login en groep aanmaken */
        $this->createUsersAndLogInAndMakeGroup($client, 'test', 'test', 'test-group');
        self::assertResponseIsSuccessful();

        /* Test of user shopping list kan aanmaken */
        $client->request('POST', '/api/shopping_lists', [
            'headers' => ['Content-Type' => 'application/json'],
            'auth_bearer' => $this->getJwtAuth(),
            'json' => [
                'title' => 'test Shopping list',
                'groep' => '/api/groups/1'
            ]
        ]);
        self::assertResponseIsSuccessful();

        /* Test of user shopping list item kan aanmaken */
        $client->request('POST', '/api/shopping_list_items', [
            'headers' => ['Content-Type' => 'application/json'],
            'auth_bearer' => $this->getJwtAuth(),
            'json' => [
                'title' => 'test Shopping list Item',
                'shoppingList' => '/api/shopping_lists/1',
                'user' => '/api/users/1'
            ]
        ]);
        self::assertResponseIsSuccessful();

        /* Deze user mag het item niet verwijderen */
        $client->request('DELETE', '/api/shopping_list_items/1', [
            'auth_bearer' => $this->getJwtNoAuth(),
        ]);
        self::assertResponseStatusCodeSame(403);

        /* Deze user mag het item verwijderen */
        $client->request('DELETE', '/api/shopping_list_items/1', [
            'auth_bearer' => $this->getJwtAuth(),
        ]);
        self::assertResponseIsSuccessful();
    }
}