<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ShoppingCategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 * )
 * @ORM\Entity(repositoryClass=ShoppingCategoriesRepository::class)
 */
class ShoppingCategories
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"shoppingListItem:read", "shoppingList:read", "group:item:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"shoppingListItem:read", "shoppingList:read", "group:item:read"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=ShoppingListItem::class, mappedBy="category")
     */
    private $shoppingListItems;

    public function __construct()
    {
        $this->shoppingListItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function addShoppingListItem(ShoppingListItem $shoppingListItem): self
    {
        if (!$this->shoppingListItems->contains($shoppingListItem)) {
            $this->shoppingListItems[] = $shoppingListItem;
            $shoppingListItem->setCategory($this);
        }

        return $this;
    }

    public function removeShoppingListItem(ShoppingListItem $shoppingListItem): self
    {
        if ($this->shoppingListItems->contains($shoppingListItem)) {
            $this->shoppingListItems->removeElement($shoppingListItem);
            // set the owning side to null (unless already changed)
            if ($shoppingListItem->getCategory() === $this) {
                $shoppingListItem->setCategory(null);
            }
        }

        return $this;
    }
}
