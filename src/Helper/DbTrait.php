<?php


namespace App\Helper;


use App\Entity\GroupMember;
use Doctrine\ORM\EntityManagerInterface;

trait DbTrait
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function saveToDatabase($entityToSave)
    {
        dd($entityToSave);
        $this->em->persist($entityToSave);
        $this->em->flush();
    }
}