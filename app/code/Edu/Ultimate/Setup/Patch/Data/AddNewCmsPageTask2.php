<?php

namespace Edu\Ultimate\Setup\Patch\Data;

use Magento\Cms\Model\PageFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Psr\Log\LoggerInterface;
use Zend\Log as log;

/**
 * Class AddNewCmsPageTask2
 * @package Edu\Ultimate\Setup\Patch\Data
 */
class AddNewCmsPageTask2 implements
    DataPatchInterface,
    PatchRevertableInterface
{
    /**
     * @var PageFactory
     * @var log\Logger
     */
    protected $pageFactory;
    protected $logger;
    protected $moduleDataSetup;

    /**
     * @param PageFactory $pageFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        PageFactory $pageFactory,
        LoggerInterface $logger
    ) {
        /**
         * If before, we pass $setup as argument in install/upgrade function, from now we start
         * inject it with DI. If you want to use setup, you can inject it, with the same way as here
         */
        $this->moduleDataSetup = $moduleDataSetup;
        $this->pageFactory = $pageFactory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function apply()
    {
        $objectManager = ObjectManager::getInstance();
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        $rootPath = $directory->getRoot(); ///var/www/magento2.local

        $writer = new log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new log\Logger();
        $logger->addWriter($writer);

        $pageData = [
            'title' => 'task 2',
            'page_layout' => '1column',
            'meta_keywords' => 'Page task2',
            'meta_description' => 'Page description task2',
            'identifier' => 'task2',
            'content_heading' => 'task2 wep page',
            'content' => file_get_contents($rootPath . "/app/code/Edu/Ultimate/Template/a.html"),
            'layout_update_xml' => '',
            'url_key' => 'custom-page',
            'is_active' => 1,
            'stores' => [0], // store_id comma separated
            'sort_order' => 0
        ];
        $pageData2 = [
            'title' => 'gade-style',
            'page_layout' => '1column',
            'meta_keywords' => 'Page task2 gade-style',
            'meta_description' => 'Page description task2 gade-style',
            'identifier' => 'task2-gade-style',
            'content_heading' => 'task2 wep page gade-style',
            'content' => file_get_contents($rootPath . "/app/code/Edu/Ultimate/Template/b.html"),
            'layout_update_xml' => '',
            'url_key' => 'custom-page',
            'is_active' => 1,
            'stores' => [0], // store_id comma separated
            'sort_order' => 0
        ];

        $this->moduleDataSetup->startSetup();
        /* Save CMS Page logic */
        $this->pageFactory->create()->setData($pageData)->save();
        $logger->info('add page task2, get template = ' . $rootPath . '/app/code/Edu/Ultimate/Template/a.html');
        $this->pageFactory->create()->setData($pageData2)->save();
        $logger->info('add page task2 gade style, get template = ' . $rootPath . '/app/code/Edu/Ultimate/Template/b.html');
        $this->moduleDataSetup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        /**
         * This is dependency to another patch. Dependency should be applied first
         * One patch can have few dependencies
         * Patches do not have versions, so if in old approach with Install/Ugrade data scripts you used
         * versions, right now you need to point from patch with higher version to patch with lower version
         * But please, note, that some of your patches can be independent and can be installed in any sequence
         * So use dependencies only if this important for you
         */
        return [];
    }

    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        //Here should go code that will revert all operations from `apply` method
        //Please note, that some operations, like removing data from column, that is in role of foreign key reference
        //is dangerous, because it can trigger ON DELETE statement
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        /**
         * This internal Magento method, that means that some patches with time can change their names,
         * but changing name should not affect installation process, that's why if we will change name of the patch
         * we will add alias here
         */
        return [];
    }
}
