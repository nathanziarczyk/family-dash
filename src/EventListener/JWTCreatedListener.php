<?php


namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{


    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    /**
     * De ID toevoegen aan de JWT payload
     * @param JWTCreatedEvent $event
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $userName = $event->getUser()->getUsername();

        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $userName]);

        $payload = $event->getData();
        $payload['id'] = $user->getId();
        $payload['first_name'] = $user->getFirstName();
        $payload['last_name'] = $user->getLastName();

        $event->setData($payload);
    }
}