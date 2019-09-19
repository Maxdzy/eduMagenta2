<?php

namespace Edu\CmsCreateStore\Setup\Patch\Data;

use Magento\Config\Model\ConfigFactory;
use Magento\Config\Model\ResourceModel\Config as ConfigResurce;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\ResourceModel\Store as StoreResource;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory;

/**
 * Class AddNewCmsStore
 * @package Edu\CmsCreateStore\Setup\Patch\Data
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
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectionFactory $themeCollectionFactory
     */
    private $collectionFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param StoreFactory $storeFactory
     * @param StoreResource $storeResource
     * @param ConfigFactory $configFactory
     * @param ConfigResurce $configResurce
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $themeCollectionFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        StoreFactory $storeFactory,
        StoreResource $storeResource,
        ConfigFactory $configFactory,
        ConfigResurce $configResurce,
        CollectionFactory $themeCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->storeResource = $storeResource;
        $this->storeFactory = $storeFactory;
        $this->configFactory = $configFactory;
        $this->configResurce = $configResurce;
        $this->collectionFactory = $themeCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $store = $this->storeFactory->create();
        $store->load('euro');

        if (!$store->getId()) {
            $themeCollection = $this->collectionFactory->create();
            $theme = $themeCollection->getThemeByFullPath("frontend/Skin/german");
            $themeId = $theme->getId();
            $websiteId = $this->storeManager->getWebsite()->getId();
            $groupId = $this->storeManager->getGroup()->getId();
            $store->setName('german')
                ->setCode('euro')
                ->setWebsiteId($websiteId)
                ->setGroupId($groupId)
                ->setSortOrder(0)
                ->setIsActive(1);
            $this->storeResource->save($store);
            $storeId = $store->getId();
            $configs = [
                [
                    'path' => "catalog/seo/product_url_suffix",
                    'value' => "",
                ],
                [
                    'path' => "currency/options/default",
                    'value' => "EUR",
                ],
                [
                    'path' => "currency/options/allow",
                    'value' => "EUR",
                ],
                [
                    'path' => "design/theme/theme_id",
                    'value' => $themeId,
                ],
            ];
            foreach ($configs as $config) {
                $configModel = $this->configFactory->create();
                $configModel->setWebsite($websiteId);
                $configModel->setStore($storeId);
                $configModel->setDataByPath($config['path'], $config['value']);
                $configModel->save();
            }
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
