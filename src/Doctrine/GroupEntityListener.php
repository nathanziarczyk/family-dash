<?php


namespace App\Doctrine;


use App\Entity\Group;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GroupEntityListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function prePersist(Group $group): void
    {
        if($group->getUser()) return ;
        if ($this->tokenStorage->getToken()->getUser()){
            $group->setUser($this->tokenStorage->getToken()->getUser());
        }
    }
}