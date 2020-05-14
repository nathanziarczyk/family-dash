<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ShoppingListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *     "get",
 *     "post"
 *      },
 *     itemOperations={
 *     "get",
 *     "put",
 *     "delete"
 *      },
 *     normalizationContext={"groups"={"shoppingList:read"}},
 *     denormalizationContext={"groups"={"shoppingList:write"}},
 * )
 * @ORM\Entity(repositoryClass=ShoppingListRepository::class)
 */
class ShoppingList
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"group:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"group:read", "shoppingList:write"})
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="shoppingLists")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"shoppingList:write"})
     */
    private $groep;

    /**
     * @ORM\OneToMany(targetEntity=ShoppingListItem::class, mappedBy="shoppingList")
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getGroep(): ?Group
    {
        return $this->groep;
    }

    public function setGroep(?Group $groep): self
    {
        $this->groep = $groep;

        return $this;
    }

    /**
     * @return Collection|ShoppingListItem[]
     */
    public function getShoppingListItems(): Collection
    {
        return $this->shoppingListItems;
    }

    public function addShoppingListItem(ShoppingListItem $shoppingListItem): self
    {
        if (!$this->shoppingListItems->contains($shoppingListItem)) {
            $this->shoppingListItems[] = $shoppingListItem;
            $shoppingListItem->setShoppingList($this);
        }

        return $this;
    }

    public function removeShoppingListItem(ShoppingListItem $shoppingListItem): self
    {
        if ($this->shoppingListItems->contains($shoppingListItem)) {
            $this->shoppingListItems->removeElement($shoppingListItem);
            // set the owning side to null (unless already changed)
            if ($shoppingListItem->getShoppingList() === $this) {
                $shoppingListItem->setShoppingList(null);
            }
        }

        return $this;
    }
}
