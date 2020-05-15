<?php


namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CheckGroupAccesPreSerializeSubscriber implements EventSubscriberInterface
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['checkGroupPreSerializer', EventPriorities::POST_SERIALIZE],
        ];
    }

    public function checkGroupPreSerializer(ViewEvent $event)
    {
//        $response = json_decode($event->getControllerResult(), false);
//        $user = $this->tokenStorage->getToken()->getUser();
//        $responseGroupURI = sprintf('/api/groups/%s', $response->groep);
//        if($response->groep === null)
//        {
//        } else
//        {
//            foreach ($user->getGroups() as $group){
//                if ($group === $responseGroupURI){
//                }
//            }
//        }

        return ;
    }

}