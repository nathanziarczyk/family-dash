<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Group;
use App\Entity\GroupMember;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

class GetGroupsController extends AbstractController
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    private $serializer;

    public function __construct(TokenStorageInterface $tokenStorage, SerializerInterface $serializer)
    {
        $this->tokenStorage = $tokenStorage;
        $this->serializer = $serializer;
    }

    /**
     * Als een user GET naar /groups krijgt deze enkel de groepen te zien waar hij lid van is
     * of voor is uitgenodigd
     * @Route(
     *     name="get_groups",
     *     path="/api/groups",
     *     methods={"GET"},
     *     defaults={"api_collection_operation_name"="get_groups"}
     * )
     */
    public function __invoke (Request $request)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $groups = $user->getGroups();
        $invitations = $user->getInvitations();
        return $this->json(['groups' => $groups, 'invitations' => $invitations], 200);
    }
}
