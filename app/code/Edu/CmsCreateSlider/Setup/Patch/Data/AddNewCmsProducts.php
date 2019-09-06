<?php

namespace Edu\CmsCreateSlider\Setup\Patch\Data;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
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
     * @var State
     */
    private $state;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ProductFactory $productFactory
     * @param State $state
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ProductFactory $productFactory,
        State $state
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
        $this->state->setAreaCode(Area::AREA_FRONTEND);
        for ($i = 50; $i <= 60; $i++) {
            $product = [
                'sku' => 'Simple Product ' . $i,
                'name' => 'Simple Product ' . $i,
                'attribute_set_id' => '4',
                'website_ids' => [1],
                'status' => Status::STATUS_ENABLED,
                'visibility' => 4,
                'price' => 14,
                'type_id' => 'simple',
                'stock_data' => [
                    'use_config_manage_stock' => 0,
                    'manage_stock' => 3,
                    'is_in_stock' => 5,
                    'qty' => 100
                ],
            ];
            $this->productFactory->create()->setData($product)->save();
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
