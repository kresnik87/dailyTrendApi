<?php


namespace App\Command;

use App\Entity\Publisher;
use App\Services\DataProcessService;
use App\Services\ScrapService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\StatusPayment;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\DateTime;


class GenerateFeedCommand extends Command
{


    protected static $defaultName = 'app:generate:feed';


    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var LoggerInterface
     */
    private $logging;

    /**
     * @var ScrapService
     */
    private $webCrapService;

    /**
     * @var DataProcessService
     */
    private $dataService;

    /**
     * GenerateFeedCommand constructor.
     * @param EntityManager $em
     * @param LoggerInterface $logger
     * @param ScrapService $scrapService
     * @param DataProcessService $dataService
     */
    public function __construct(
        EntityManager $em,
        LoggerInterface $logger,
        ScrapService $scrapService,
        DataProcessService $dataService
    )
    {
        parent::__construct();
        $this->em = $em;
        $this->logging = $logger;
        $this->webCrapService = $scrapService;
        $this->dataService = $dataService;
    }

    protected function configure()
    {
        $this->setDescription('Get Feeds by All publisher');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Publisher[] $publishers */
        $publishers = $this->em->getRepository(Publisher::class)->findAll();
        $this->logging->info("[GenerateFeedCommand][Publishers Found]: " . count($publishers));
        $output->writeln([
            'Publishers Found: ' . count($publishers),
            '================='
        ]);
        foreach ($publishers as $publisher) {
            $this->logging->info("[GenerateFeedCommand][Publisher]: " . $publisher->getName());
            $data = $this->webCrapService->getWebInfo($publisher->getUrl(), Publisher::MIN_FEED_VALUE);
            if (is_array($data)) {
                $feeds = $this->dataService->processDataToEntity($data, $publisher);
                $this->logging->info("[GenerateFeedCommand][Feeds Created]: " . count($feeds));
                $output->writeln("Feeds Create: " . count($feeds));

            } else {
                $this->logging->error("[GenerateFeedCommand][Publishers Data Error]: " . $data);
                $output->write("Data Process Error: " . $data);
            }
        }

    }


}
