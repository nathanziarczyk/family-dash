<?php


namespace App\Test;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CustomApiTestCase extends ApiTestCase
{
    private $jwt;

    protected function createUser(string $email, string $password){
        $user = new User();
        $user->setEmail($email);
        $user->setFirstName(substr($email, 0, 3));
        $user->setLastName(substr($email, 0, 3));
        $encoded = self::$container->get(UserPasswordEncoderInterface::class)
            ->encodePassword($user, $password);
        $user->setPassword($encoded);
        $user->setRegKey('renew');

        $em = self::$container->get(EntityManagerInterface::class);

        $em->persist($user);
        $em->flush();

        return $user;
    }

    protected function logIn(Client $client, string $email, string $password){
        $data = $client->request('POST', '/api/login',[
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => $email,
                'password' => $password
            ]
        ]);
        $this->setJwt(json_decode($data->getContent(), true)['token']);
    }

    protected function createGroup(Client $client, string $name){
        $data = $client->request('POST', '/api/groups',[
            'auth_bearer' => $this->getJwt(),
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'name' => $name
            ]
        ]);
    }

    protected function createUserAndLogInAndMakeGroup(Client $client, string $email, string $password, string $groupName)
    {
        $user = $this->createUser($email, $password);
        $this->logIn($client, $email, $password);
        $this->createGroup($client, $groupName);
        return $user;
    }

    public function getJwt()
    {
        return $this->jwt;
    }

    public function setJwt($jwt): void
    {
        $this->jwt = $jwt;
    }
}
