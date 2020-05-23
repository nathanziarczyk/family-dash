<?php


namespace App\Doctrine;


use App\Entity\Note;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NoteEntityListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function prePersist(Note $note)
    {
        if($note->getUser()) return ;
        if ($this->tokenStorage->getToken()->getUser()){
            $note->setUser($this->tokenStorage->getToken()->getUser());
        }
    }
}