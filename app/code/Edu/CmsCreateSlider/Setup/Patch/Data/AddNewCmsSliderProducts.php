<?php

namespace Edu\CmsCreateSlider\Setup\Patch\Data;

use Magento\Cms\Model\PageFactory;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class AddNewCmsPageTask2
 * @package Edu\CmsCreatePage\Setup\Patch\Data
 */
class AddNewCmsSliderProducts implements
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

        $pageData = [
            'title' => 'task 3 slick',
            'page_layout' => '1column',
            'meta_keywords' => 'Page task3',
            'meta_description' => 'Page description task3',
            'identifier' => 'slick',
            'content_heading' => 'task3 web page',
            'content' => '<p>{{widget type="Magento\CatalogWidget\Block\Product\ProductsList" title="Related products" show_pager="0" products_count="10" template="Magento_CatalogWidget::product/widget/content/grid.phtml" conditions_encoded="^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^]^]"}}</p>',
            'layout_update_xml' => '',
            'url_key' => 'page_task2',
            'is_active' => 1,
            'stores' => [0],
            'sort_order' => 0
        ];

        $this->moduleDataSetup->startSetup();
        $this->pageFactory->create()->setData($pageData)->save();
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
