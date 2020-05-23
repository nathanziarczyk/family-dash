<?php


namespace App\Doctrine;


use App\Entity\Event;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EventEntityListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function prePersist(Event $event)
    {
        if($event->getUser()) return ;
        if ($this->tokenStorage->getToken()->getUser()){
            $event->setUser($this->tokenStorage->getToken()->getUser());
        }
    }
}