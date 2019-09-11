<?php

namespace Edu\CmsCreateSlider\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\StoreRepository;
use Magento\Store\Model\StoreManagerInterface;

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
     * @var StoreRepository
     */
    protected $storeRepository;
    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param StoreFactory $storeFactory
     * @param StoreRepository $storeRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        StoreFactory $storeFactory,
        StoreRepository $storeRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->storeFactory = $storeFactory;
        $this->storeRepository = $storeRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $storeManager = $this->storeManager->getStore();
        //$store=createOrUpdateStore();
        //$page = $this->pageFactory->create()->setData($pageData);
        //$this->pageRepository->save($page);
        $this->moduleDataSetup->endSetup();
    }

    /**
     * @param $group
     * @param $websiteId
     * @param $storeViewName
     * @param $storeViewCode
     * @param $data
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createOrUpdateStore($group, $websiteId, $storeViewName, $storeViewCode, $data)
    {
        $store = $this->storeFactory->create();
        if ($group->getId() && $group->getDefaultStoreId()) {
            $store->load($group->getDefaultStoreId());
        }
        $store->setName($storeViewName . ' - Store View');
        $store->setCode($storeViewCode);
        $store->setWebsiteId($websiteId);
        $store->setGroupId($group->getId());
        $store->setSortOrder($data['sort_order']);
        $store->setIsActive($data['is_active']);
        $create_store = $this->storeRepository->save($store);
        return $create_store;
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
