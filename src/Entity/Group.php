<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\GroupMemberRepository;
use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\NewGroupController;
use App\Controller\GetGroupsController;

// TODO PUT & DELETE ADMIN
/**
 * @ApiResource(
 *     collectionOperations={
 *     "get"={
 *          "controller"=GetGroupsController::class
 *     },
 *     "post"={
 *          "controller"=NewGroupController::class
 *     },
 *     },
 *     itemOperations={
 *     "get"={
 *          "security" = "is_granted('ROLE_USER') and user.getGroups().contains(object)",
 *     },
 *     "put" = {
 *          "denormalization_context"={"groups"={"group:item:put"}},
 *          "security_post_denormalize"="is_granted('ROLE_USER') and user.getGroups().contains(object)"
 *     },
 *     "delete" = {
 *          "security" = "is_granted('ROLE_USER') and user.getGroups().contains(object)",
 *     },
 *      },
 *     normalizationContext={"groups"={"group:read"}},
 *     denormalizationContext={"groups"={"group:write"}},
 * )
 * @ApiFilter(PropertyFilter::class)
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 * @ORM\Table(name="`group`")
 * @ORM\EntityListeners({"App\Doctrine\GroupEntityListener"})
 */
class Group implements ObjectManagerAware
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
     * @Groups({"group:read", "group:write", "group:item:put"})
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @Groups({ "group:write"})
     */
    private $postGroupMembers;

    /**
     * @ORM\OneToMany(targetEntity=GroupMember::class, mappedBy="groep")
     */
    private $groupMembers;

    /**
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="groep")
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="groep")
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity=ShoppingList::class, mappedBy="groep")
     */
    private $shoppingLists;

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @Assert\NotBlank()
     */
    private $user;


    public function injectObjectManager(ObjectManager $objectManager, ClassMetadata $classMetadata): void
    {
        $this->em = $objectManager;
    }

    public function __construct()
    {
        $this->groupMembers = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->shoppingLists = new ArrayCollection();
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

    public function getPostGroupMembers()
    {
        return $this->postGroupMembers;
    }

    public function setPostGroupMembers(array $postGroupMembers): self
    {
        $this->postGroupMembers = $postGroupMembers;

        return $this;
    }

    /** ************************************** */
    /***/                                   /***/
    /***/           /* Members */           /***/
    /***/                                   /***/
    /** ************************************** */

    /**
     * @return Collection|User[]
     * @Groups({"group:read"})
     */
    public function getGroupMembers(): Collection
    {
        $members = array();
        foreach ($this->groupMembers as $member){
            if ($member->getAccepted() === true){
                $members[] = $member->getUser();
            }
        }
        return new ArrayCollection($members);
    }

    /**
     * @Groups({"group:item:put"})
     * @param User $user
     * @return ArrayCollection
     */
    public function setAddGroupMember(User $user): ArrayCollection
    {
        $groupMember = new GroupMember();
        $groupMember->setGroep($this);
        $groupMember->setUser($user);
        $this->groupMembers->add($groupMember);
        $this->em->persist($groupMember);
        $this->em->flush();

        return $this->groupMembers;
    }

    /**
     * @Groups({"group:item:put"})
     * @param User $user
     * @return
     */
    public function setRemoveGroupMember(User $user)
    {
        $member = $this->em->getRepository(GroupMember::class)->findBy([
            'user' => $user,
            'groep' => $this
        ]);
        if(!$member) throw new \InvalidArgumentException('This user is not a member of this group', 404);
        $this->groupMembers->removeElement($member[0]);
        $this->em->remove($member[0]);
        $this->em->flush();

        return $this->groupMembers;
    }

    /** ************************************** */
    /***/                                   /***/
    /***/           /* Events */            /***/
    /***/                                   /***/
    /** ************************************** */

    /**
     * @return Collection|Event[]
     * @Groups({"group:read"})
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    /** ************************************** */
    /***/                                   /***/
    /***/           /* Notes */             /***/
    /***/                                   /***/
    /** ************************************** */

    /**
     * @return Collection|Note[]
     * @Groups({"group:read"})
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    /** ************************************** */
    /***/                                   /***/
    /***/           /* Shop */              /***/
    /***/                                   /***/
    /** ************************************** */

    /**
     * @return Collection|ShoppingList[]
     * @Groups({"group:read"})
     */
    public function getShoppingLists(): Collection
    {
        return $this->shoppingLists;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

}
