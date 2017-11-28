<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\Channable\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Magmodules\Channable\Model\Item as ItemModel;
use Magmodules\Channable\Helper\General as GeneralHelper;

/**
 * Class ItemUpdate
 *
 * @package Magmodules\Channable\Console\Command
 */
class ItemUpdate extends Command
{

    /**
     *
     */
    const COMMAND_NAME = 'channable:item:update';
    /**
     * @var ItemModel
     */
    private $itemModel;
    /**
     * @var GeneralHelper
     */
    private $generalHelper;

    /**
     * ItemUpdate constructor.
     *
     * @param ItemModel     $itemModel
     * @param GeneralHelper $generalHelper
     */
    public function __construct(
        ItemModel $itemModel,
        GeneralHelper $generalHelper
    ) {
        $this->itemModel = $itemModel;
        $this->generalHelper = $generalHelper;
        parent::__construct();
    }

    /**
     *  {@inheritdoc}
     */
    public function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Push Invalidated Items to Channable');
        $this->addOption(
            'store-id',
            null,
            InputOption::VALUE_OPTIONAL,
            'Store ID, if not specified all enabled stores will be processed'
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
            $output->writeln('<info>Running All Stores</info>');
            $storeIds = $this->generalHelper->getEnabledArray('magmodules_channable_marketplace/item/enable');
            foreach ($storeIds as $storeId) {
                $result = $this->itemModel->updateByStore($storeId);
                $msg = sprintf(
                    'Store ID: %s - %s - Products: %s',
                    $storeId,
                    $result['status'],
                    $result['qty']
                );
                $output->writeln($msg);
            }
        } else {
            $output->writeln('<info>Running Store ' . $storeId . '</info>');
            $result = $this->itemModel->updateByStore($storeId);
            $msg = sprintf(
                'Store ID: %s - %s - Products: %s',
                $storeId,
                $result['status'],
                $result['qty']
            );
            $output->writeln($msg);
        }
    }
}