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
    protected $_logger;

    /**
     * Construct
     *
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     */
    public function __construct(
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Psr\Log\LoggerInterface $logger
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
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
            $rootPath  =  $directory->getRoot(); ///var/www/magento2.local

            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
            $logger = new \Zend\Log\Logger();
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
