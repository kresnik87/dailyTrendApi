<?php

namespace App\Controller;

use App\Entity\Feed;
use App\Entity\Publisher;
use App\Services\DataProcessService;
use App\Services\ScrapService;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\VarDumper\Cloner\Data;

class WebCrapController extends AbstractController
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ScrapService
     */
    private $webCrapService;

    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * @var DataProcessService
     */
    private $dataService;

    /**
     * WebCrapController constructor.
     * @param LoggerInterface $logger
     * @param ScrapService $scrapService
     * @param NormalizerInterface $normalizer
     * @param  DataProcessService $dataService
     */

    public function __construct(
        LoggerInterface $logger,
        ScrapService $scrapService,
        NormalizerInterface $normalizer,
        DataProcessService  $dataService
    )
    {
        $this->logger = $logger;
        $this->webCrapService = $scrapService;
        $this->normalizer = $normalizer;
        $this->dataService = $dataService;
    }

    public function getWebCrap($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Publisher $publisher */
        $publisher = $this->getDoctrine()->getRepository(Publisher::class)->find($id);
        if (!empty($publisher) && !is_null($publisher)) {
            $data = $this->webCrapService->getWebInfo($publisher->getUrl(), Publisher::MIN_FEED_VALUE);
            if (is_array($data)) {

                $feeds = $this->dataService->processDataToEntity($data,$publisher);
                return new JsonResponse($this->normalizer->normalize($feeds, 'json', ['feed-read']));
            } else {
                return new Response($data, 400);
            }
        } else {
            return new Response('Not Found Publisher', 404);
        }

    }
}
