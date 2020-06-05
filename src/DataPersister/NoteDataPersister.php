<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Note;
use App\Helper\ShortenHtmlTrait;
use Doctrine\ORM\EntityManagerInterface;
use Urodoz\Truncate\TruncateService;

class NoteDataPersister implements DataPersisterInterface
{

    use ShortenHtmlTrait;

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    public function supports($data): bool
    {
        return $data instanceof Note;
    }

    /**
     * @param Note $data
     */
    public function persist($data)
    {
        $truncate = new TruncateService();
        if ($data->getBody()){
            if (strlen($data->getBody()) <= 50){
                $data->setShortBody($data->getBody());
            } else {
                $data->setShortBody(
                    $truncate->truncate($data->getBody(), 50)
                );
            }
        }
        $this->em->persist($data);
        $this->em->flush();
    }

    /**
     * @param Note $data
     */
    public function remove($data)
    {
        $this->em->remove($data);
        $this->em->flush();
    }
}