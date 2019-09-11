<?php

namespace Edu\CmsCreateStore\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
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
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;



    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param StoreFactory $storeFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        StoreFactory $storeFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->storeFactory = $storeFactory;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $store = $this->storeFactory->create();
        $store->setName('USD');
        $store->setCode("en");
        $store->setWebsiteId(1);
        $store->setGroupId(1);
        $store->setSortOrder(1);
        $store->setIsActive(1);
        $store->save();
        //$create_store = $this->storeRepository->save($store);
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
