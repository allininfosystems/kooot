<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Magmodules\TradeTracker\Model\Feed as FeedModel;
use Magmodules\TradeTracker\Helper\General as GeneralHelper;

/**
 * Class FeedGenerate
 *
 * @package Magmodules\TradeTracker\Console\Command
 */
class FeedGenerate extends Command
{

    const COMMAND_NAME = 'tradetracker:feed:generate';
    /**
     * @var FeedModel
     */
    private $feedModel;
    /**
     * @var GeneralHelper
     */
    private $generalHelper;

    /**
     * GenerateFeed constructor.
     *
     * @param FeedModel     $feedModel
     * @param GeneralHelper $generalHelper
     */
    public function __construct(
        FeedModel $feedModel,
        GeneralHelper $generalHelper
    ) {
        $this->feedModel = $feedModel;
        $this->generalHelper = $generalHelper;
        parent::__construct();
    }

    /**
     *  {@inheritdoc}
     */
    public function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Generate TradeTracker XML Feed');
        $this->addOption(
            'store-id',
            null,
            InputOption::VALUE_OPTIONAL,
            'Store ID of the export feed. If not specified all enabled stores will be exported'
        );
        parent::configure();
    }

    /**
     *  {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $storeId = $input->getOption('store-id');
        if (empty($storeId) || !is_numeric($storeId)) {
            $output->writeln('<info>Start Generating feed for all stores</info>');
            $storeIds = $this->generalHelper->getEnabledArray('magmodules_tradetracker/generate/enable');
            foreach ($storeIds as $storeId) {
                $result = $this->feedModel->generateByStore($storeId, 'cli');
                $msg = sprintf(
                    'Store ID %s: Generated feed with %s product in %s',
                    $storeId,
                    $result['qty'],
                    $result['time']
                );
                $output->writeln($msg);
            }
        } else {
            $output->writeln('<info>Start Generating feed for Store ID ' . $storeId . '</info>');
            $result = $this->feedModel->generateByStore($storeId, 'cli');
            $msg = sprintf(
                'Store ID %s: Generated feed with %s product in %s',
                $storeId,
                $result['qty'],
                $result['time']
            );
            $output->writeln($msg);
        }
    }
}
