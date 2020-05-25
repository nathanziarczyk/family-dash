<?php


namespace App\Doctrine;


use App\Entity\ShoppingListItem;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ShoppingListItemEntityListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function prePersist(ShoppingListItem $shoppingListItem)
    {
        if($shoppingListItem->getUser()) return ;
        if ($this->tokenStorage->getToken()->getUser()){
            $shoppingListItem->setUser($this->tokenStorage->getToken()->getUser());
        }
    }
}