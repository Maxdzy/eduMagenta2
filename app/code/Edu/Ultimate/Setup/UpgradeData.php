<?php

namespace Edu\Ultimate\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * Construct
     *
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     */
    public function __construct(
        \Magento\Cms\Model\PageFactory $pageFactory
    ) {
        $this->_pageFactory = $pageFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.5') < 0) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');

            $rootPath  =  $directory->getRoot();

            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/debug.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($rootPath);
/*
            $page = $this->_pageFactory->create();
            $page->setTitle('Example CMS page')
                ->setIdentifier('example-cms-page')
                ->setIsActive(true)
                ->setPageLayout('1column')
                ->setStores([0])
                ->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit.')
                ->save();*/
        }

        $setup->endSetup();
    }
}
