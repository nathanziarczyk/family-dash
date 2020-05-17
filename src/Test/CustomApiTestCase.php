<?php


namespace App\Test;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CustomApiTestCase extends ApiTestCase
{
    private $jwtAuth;
    private $jwtNoAuth;

    protected function createUsersAndLogInAndMakeGroup(Client $client, string $username, string $password, string $groupName)
    {
        $emailAuth = $username.'1@test.com';
        $emailNoAuth = $username.'2@test.com';

        $userAuth = $this->createUser($emailAuth, $password);
        $userNoAuth = $this->createUser($emailNoAuth, $password);

        $this->logInAuth($client, $emailAuth, $password);
        $this->logInNoAuth($client, $emailNoAuth, $password);

        $this->createGroup($client, $groupName);
    }

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

    protected function logInAuth(Client $client, string $email, string $password){
        $data = $client->request('POST', '/api/login',[
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => $email,
                'password' => $password
            ]
        ]);
        $this->setJwtAuth(json_decode($data->getContent(), true)['token']);
    }

    protected function logInNoAuth(Client $client, string $email, string $password){
        $data = $client->request('POST', '/api/login',[
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => $email,
                'password' => $password
            ]
        ]);
        $this->setJwtNoAuth(json_decode($data->getContent(), true)['token']);
    }

    protected function createGroup(Client $client, string $name){
        $data = $client->request('POST', '/api/groups',[
            'auth_bearer' => $this->getJwtAuth(),
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'name' => $name
            ]
        ]);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return self::$container->get('doctrine')->getManager();
    }

    public function getJwtAuth()
    {
        return $this->jwtAuth;
    }

    public function setJwtAuth($jwt): void
    {
        $this->jwtAuth = $jwt;
    }

    public function getJwtNoAuth()
    {
        return $this->jwtNoAuth;
    }

    public function setJwtNoAuth($jwtNoAuth): void
    {
        $this->jwtNoAuth = $jwtNoAuth;
    }

}
