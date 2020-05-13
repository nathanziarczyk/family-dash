<?php


namespace App\Controller;


use App\Entity\Group;
use App\Entity\GroupMember;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NewGroupController extends AbstractController
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route(
     *     name="new_group",
     *     path="/api/new_group",
     *     methods={"POST"},
     *     defaults={"api_collection_operation_name"="new_group"}
     * )
     */
    public function __invoke (Request $request)
    {
        $content = json_decode($request->getContent(), false);
        $user = $this->tokenStorage->getToken()->getUser();
        $groupName = $content->name;

        $group = new Group();
        $group->setName($groupName);
        $em = $this->getDoctrine()->getManager();
        $em->persist($group);

        $groupMember = new GroupMember();
        $groupMember->setUser($user);
        $groupMember->setGroep($group);
        $groupMember->setAccepted(true);
        $em->persist($groupMember);
        $em->flush();

        return $group;
    }

}