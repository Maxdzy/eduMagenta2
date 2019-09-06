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
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ProductFactory $productFactory
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ProductFactory $productFactory,
        \Magento\Framework\App\State $state
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->productFactory = $productFactory;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        for ($i = 5; $i <= 15; $i++) {
            $simpleProduct1 = $this->productFactory->create();
            $simpleProduct1->setData('sku', 'Simple Product ' . $i);
            $simpleProduct1->setData('name', 'Simple Product ' . $i);
            $simpleProduct1->setData('attribute_set_id', '4');
            $simpleProduct1->setData('website_ids', [1]); // product can be found in main website
            $simpleProduct1->setData('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            $simpleProduct1->setData('visibility', 4);
            $simpleProduct1->setData('price', 12);
            $simpleProduct1->setData('type_id', 'simple');
            $simpleProduct1->setData('stock_data', [
                'use_config_manage_stock' => 0,
                'manage_stock' => 1,
                'is_in_stock' => 1,
                'qty' => 100
            ]);
            $simpleProduct1->save();
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
