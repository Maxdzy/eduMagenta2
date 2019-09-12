<?php

namespace Edu\CmsCreateStore\Setup\Patch\Data;

use Magento\Config\Model\ConfigFactory;
use Magento\Config\Model\ResourceModel\Config as ConfigResurce;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\ResourceModel\Store as StoreResource;
use Magento\Store\Model\StoreFactory;

/**
 * Class AddNewCmsPageTask2
 * @package Edu\CmsCreateSlider\Setup\Patch\Data
 */
class AddNewCmsStore implements
    DataPatchInterface
{
    /**
     * @var StoreFactory
     */
    protected $storeFactory;
    /**
     * @var ConfigFactory
     */
    protected $configFactory;
    /**
     * @var ConfigResurce
     */
    protected $configResurce;

    /**
     * @var StoreResource
     */
    protected $storeResource;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param StoreFactory $storeFactory
     * @param StoreResource $storeResource
     * @param ConfigFactory $configFactory
     * @param ConfigResurce $configResurce
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        StoreFactory $storeFactory,
        StoreResource $storeResource,
        ConfigFactory $configFactory,
        ConfigResurce $configResurce
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->storeResource = $storeResource;
        $this->storeFactory = $storeFactory;
        $this->configFactory = $configFactory;
        $this->configResurce = $configResurce;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $store = $this->storeFactory->create();
        $store->setName('lEURO');
        $store->setCode('ded');
        $store->setWebsiteId(1);
        $store->setGroupId(1);
        $store->setSortOrder(0);
        $store->setIsActive(1);
        $this->storeResource->save($store);
        $storeId=$store->getId();
        $configs[] = [
            'path' => "catalog/seo/product_url_suffix",
            'value' => "",
        ];
        $configs[] = [
            'path' => "currency/options/default",
            'value' => "EUR",
        ];
        $configs[] = [
            'path' => "currency/options/allow",
            'value' => "EUR",
        ];
        foreach ($configs as $config) {
            $configModel = $this->configFactory->create();
            $configModel->setWebsite($storeId);
            $configModel->setStore($storeId);
            $configModel->setDataByPath($config['path'], $config['value']);
            //$this->configResurce->save($configModel);
            $configModel->save();
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
