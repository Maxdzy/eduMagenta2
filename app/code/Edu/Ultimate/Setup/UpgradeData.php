<?php

namespace Edu\Ultimate\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Cms\Model\PageFactory;
use Psr\Log\LoggerInterface;
use Zend\Log as log;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var PageFactory
     */
    protected $_pageFactory;
    protected $_logger;

    /**
     * Construct
     *
     * @param PageFactory $pageFactory
     */
    public function __construct(
        PageFactory $pageFactory,
        LoggerInterface $logger
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_logger = $logger;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.6.1') < 0) {
            $objectManager = ObjectManager::getInstance();
            $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
            $rootPath  =  $directory->getRoot(); ///var/www/magento2.local

            $writer = new log\Writer\Stream(BP . '/var/log/test.log');
            $logger = new log\Logger();
            $logger->addWriter($writer);
            $logger->info('debug1234' . $rootPath);

            $page = $this->_pageFactory->create();
            $page->setTitle('test')
                ->setIdentifier('task2')
                ->setIsActive(true)
                ->setPageLayout('1column')
                ->setStores([0])
                ->setContent(file_get_contents($rootPath . "/app/code/Edu/Ultimate/Template/a.html"))
                ->save();

            $page2 = $this->_pageFactory->create();
            $page2->setTitle('gade-style')
                ->setIdentifier('task2-gade-style')
                ->setIsActive(true)
                ->setPageLayout('1column')
                ->setStores([0])
                ->setContent(file_get_contents($rootPath . "/app/code/Edu/Ultimate/Template/b.html"))
                ->save();
        }

        $setup->endSetup();
    }
}
