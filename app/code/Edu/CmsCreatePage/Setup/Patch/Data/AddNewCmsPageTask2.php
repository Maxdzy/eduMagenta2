<?php

namespace Edu\CmsCreatePage\Setup\Patch\Data;

use Magento\Cms\Model\PageFactory;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class AddNewCmsPageTask2
 * @package Edu\CmsCreatePage\Setup\Patch\Data
 */
class AddNewCmsPageTask2 implements
    DataPatchInterface
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;
    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;
    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @param PageFactory $pageFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param DirectoryList $DirectoryList
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        PageFactory $pageFactory,
        DirectoryList $directoryList
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->pageFactory = $pageFactory;
        $this->directoryList = $directoryList;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function apply()
    {
        $directory = $this->directoryList;
        $rootPath = $directory->getRoot();
        $templatePath = $rootPath . "/app/code/Edu/CmsCreatePage/Template/";

        $pagesData[] = [
            'title' => 'task 2',
            'page_layout' => '1column',
            'meta_keywords' => 'Page task2',
            'meta_description' => 'Page description task2',
            'identifier' => 'task2',
            'content_heading' => 'task2 wep page',
            'content' => file_get_contents($templatePath . "page_task2.html"),
            'layout_update_xml' => '',
            'url_key' => 'page_task2',
            'is_active' => 1,
            'stores' => [0],
            'sort_order' => 0
        ];
        $pagesData[] = [
            'title' => 'gade-style',
            'page_layout' => '1column',
            'meta_keywords' => 'Page task2 gade-style',
            'meta_description' => 'Page description task2 gade-style',
            'identifier' => 'task2-gade-style',
            'content_heading' => 'task2 wep page gade-style',
            'content' => file_get_contents($templatePath . "page_task2_gade-style.html"),
            'layout_update_xml' => '',
            'url_key' => 'page_task2_gade-style',
            'is_active' => 1,
            'stores' => [0],
            'sort_order' => 0
        ];

        $this->moduleDataSetup->startSetup();
        foreach ($pagesData as $page) {
            $this->pageFactory->create()->setData($page)->save();
        }
        $this->moduleDataSetup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
