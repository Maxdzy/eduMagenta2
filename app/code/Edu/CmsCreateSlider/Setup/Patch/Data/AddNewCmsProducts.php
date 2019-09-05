<?php

namespace Edu\CmsCreateSlider\Setup\Patch\Data;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class AddNewCmsPageTask2
 * @package Edu\CmsCreateSlider\Setup\Patch\Data
 */
class AddNewCmsProducts implements
    DataPatchInterface
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;
    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * @param ProductFactory $productFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ProductFactory $productFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->productFactory = $productFactory;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        #$this->productFactory->create()->setData($productData)->save();

        $simpleProduct1 = $this->productFactory->create();
        $simpleProduct1->setData('sku', 'Test Simple Product 1');
        $simpleProduct1->setData('name', 'Test Simple Product 1');
        $simpleProduct1->setData('website_ids', [1]); // product can be found in main website
        $simpleProduct1->setData('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $simpleProduct1->setData('visibility', 4);
        $simpleProduct1->setData('price', 12);
        $simpleProduct1->setData('type_id', 'simple');

        $simpleProduct1->save();

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
