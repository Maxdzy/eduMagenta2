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

    protected $_logger;

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
        ConfigResurce $configResurce,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->storeResource = $storeResource;
        $this->storeFactory = $storeFactory;
        $this->configFactory = $configFactory;
        $this->configResurce = $configResurce;
        $this->_logger = $logger;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function apply()
    {
        $this->_logger->info('info gogo start patch 5');

        $this->moduleDataSetup->startSetup();
        $store = $this->storeFactory->create();

        $store->setName('testtt');
        $store->setCode('tetett');
        $store->setWebsiteId(1);
        $store->setGroupId(1);
        $store->setSortOrder(0);
        $store->setIsActive(1);

        $storeTop = $this->storeResource->save($store);

        /*$config[] = [
            'scope' => "stores",
            'scopeId' => $storeId,
            'path_url_suffix' => "catalog/seo/product_url_suffix",
            'value_url_suffix' => "",
            'path_currency_default' => "currency/options/default",
            'value_currency_default' => "EUR",
            'path_currency_allow' => "currency/options/allow",
            'value_currency_allow' => "EUR",
        ];

        $configModel = $this->configFactory->create();
        $configModel->setWebsite($config['scopeId']);
        $configModel->setStore($config['scopeId']);
        $configModel->setDataByPath($config['path_url_suffix'], $config['value_url_suffix']);
        $configModel->setDataByPath($config['path_currency_default'], $config['value_currency_default']);
        $configModel->setDataByPath($config['path_currency_allow'], $config['value_currency_allow']);
        $this->configResurce->save($configModel);*/

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
