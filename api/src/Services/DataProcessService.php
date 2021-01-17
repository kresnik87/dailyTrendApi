<?php

namespace App\Services;

use App\Entity\Feed;
use App\Entity\Publisher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class DataProcessService
{


    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * SendMailService constructor.
     * @param LoggerInterface $logger
     * @param  EntityManagerInterface $em
     */


    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $em
    )
    {
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     * @param array $data
     * @param Publisher $publisher
     * @return mixed
     */
    public function processDataToEntity($data, $publisher)
    {
        $feeds = new ArrayCollection();
        foreach ($data as $element => $item) {
            $feed = new Feed();
            $feed->setTitle($item["title"]);
            $feed->setBody($item["body"]);
            $feed->setImage($item["images"]);
            $feed->setSource($item["source"]);
            $feed->setPublisher($publisher);
            $this->em->persist($feed);
            $feeds->add($feed);
        }
        $this->em->flush();
        return $feeds;
    }


}
