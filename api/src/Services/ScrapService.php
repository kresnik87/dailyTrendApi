<?php

namespace App\Services;

use Facebook\WebDriver\Exception\TimeoutException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class ScrapService
{
    const TITLE_TAGS_H2 = "h2";
    const TITLE_TAGS_H3 = "h3";


    /**
     * @var LoggerInterface
     */
    private $logger;

    private $client;


    /**
     * SendMailService constructor.
     * @param LoggerInterface $logger
     */


    public function __construct(
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
        $this->client = Client::createChromeClient();
    }

    /**
     * @param string $url
     * @param int $max
     * @return mixed
     */
    public function getWebInfo($url, $max)
    {
        $crawler = $this->client->request('GET', $url);
        try {
            for ($i = 0; $i < $max; $i++) {
                if ($crawler->filter('article')->eq($i)->filter('h2')->count() > 0) {
                    $data[$i]["title"] = $crawler->filter('article')->eq($i)->filter('h2')->getText();
                } else {
                    $data[$i]["title"] = $crawler->filter('article')->eq($i)->getText();
                }
                if ($crawler->filter('article')->eq($i)->filter('p')->count() > 0) {
                    $data[$i]["body"] = $crawler->filter('article')->eq($i)->filter('p')->text();
                } else {
                    $data[$i]["body"] = "";
                }
                if ($crawler->filter('article')->eq($i)->filter('img')->count() > 0) {
                    $data[$i]["images"] = $crawler->filter('article')->eq($i)->filter('img')->eq(0)->attr('src');
                } else {
                    $data[$i]["images"] = "";
                }
                $data[$i]["source"] = $crawler->filter('article')->eq($i)->filter('a')->eq(0)->attr('href');

            }
            return $data;
        } catch (TimeoutException $exception) {
            $this->logger->error("[ScrapService][Error]: " . $exception->getMessage());
            return $exception->getMessage();
        }

    }


}
