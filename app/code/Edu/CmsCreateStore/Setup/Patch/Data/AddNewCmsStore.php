<?php

namespace Edu\CmsCreateStore\Setup\Patch\Data;

use Magento\Config\Model\ConfigFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
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
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        StoreFactory $storeFactory,
        StoreResource $storeResource,
        ConfigFactory $configFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->storeResource = $storeResource;
        $this->storeFactory = $storeFactory;
        $this->configFactory = $configFactory;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function apply()
    {
        $storeData = [
            'setName' => 'USD',
            'setCode' => "en",
            'setWebsiteId' => 1,
            'setGroupId' => 1,
            'setSortOrder' => 1,
            'setIsActive' => 1,
        ];
        $this->moduleDataSetup->startSetup();
        //$store = $this->storeFactory->create()->setData($storeData);
        //$this->storeResource->save($store);

        $config = [
            'scope' => "stores",
            'scopeId' => 5,
            'path' => "catalog/seo/product_url_suffix",
            'value' => "",
        ];

        $configModel = $this->configFactory->create();
        $configModel->setWebsite($config['scopeId']);
        $configModel->setStore($config['scopeId']);
        //$config->setScope(ScopeConfigInterface::SCOPE_TYPE_DEFAULT);

        $configModel->setDataByPath($config['path'], $config['value']);
        $configModel->save();

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
