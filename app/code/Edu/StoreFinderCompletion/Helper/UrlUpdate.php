<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Helper;

use Exception;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewrite as UrlRewriteResource;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Model\UrlRewrite;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite as UrlRewriteData;
use Edu\StoreFinderCompletion\Helper\Data as DataHelper;

class UrlUpdate extends AbstractHelper
{
    /**
     * @var UrlRewriteFactory
     */
    protected $urlRewriteFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var UrlRewriteCollection
     */
    protected $rewriteCollectionFactory;

    /**
     * @var UrlRewriteResource
     */
    protected $rewriteResource;

    /**
     * @var array
     */
    protected $storeFinderRewrites;

    /**
     * @param Context $context
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param StoreManagerInterface $storeManager
     * @param UrlRewriteCollectionFactory $rewriteCollectionFactory
     * @param UrlRewriteResource $rewriteResource
     */
    public function __construct(
        Context $context,
        UrlRewriteFactory $urlRewriteFactory,
        StoreManagerInterface $storeManager,
        UrlRewriteCollectionFactory $rewriteCollectionFactory,
        UrlRewriteResource $rewriteResource
    ) {
        parent::__construct($context);

        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->storeManager = $storeManager;
        $this->rewriteResource = $rewriteResource;
        $this->rewriteCollectionFactory = $rewriteCollectionFactory;
    }

    /**
     * @return array
     */
    public function getStoreFinderRewrites()
    {
        if ($this->storeFinderRewrites === null) {
            /** @var UrlRewrite[] $rewrites */
            $rewrites = $this->getStoreFinderRewriteCollection()->getItems();

            $this->storeFinderRewrites = [];
            foreach ($rewrites as $rewrite) {
                if (!array_key_exists($rewrite->getTargetPath(), $this->storeFinderRewrites)) {
                    $this->storeFinderRewrites[$rewrite->getTargetPath()] = [];
                }
                $this->storeFinderRewrites[$rewrite->getTargetPath()][$rewrite->getStoreId()] = $rewrite;
            }
        }

        return $this->storeFinderRewrites;
    }

    /**
     * @return UrlRewriteCollection
     */
    public function getStoreFinderRewriteCollection()
    {
        return $this->getExistingRewrites([
            DataHelper::URL_STORE_FINDER,
            DataHelper::URL_STORELIST,
            DataHelper::URL_EVENTLIST,
            DataHelper::URL_STORE_DETAIL
        ]);
    }

    /**
     * @param string $field
     * @param string $url
     * @param array $stores
     */
    public function addRewrite(string $field, string $url, array $stores)
    {
        if (empty($field) || empty($url)) {
            return;
        }
        foreach ($stores as $store) {
            $this->createRewrite(
                $field,
                $url,
                $store
            );
        }
    }

    /**
     * @param string $configPath
     * @param string $url
     * @param array $stores
     */
    public function addRewriteFromConfig(string $configPath, string $url, array $stores)
    {
        $field = $this->scopeConfig->getValue($configPath);
        if ($field !== null) {
            $this->addRewrite($field, $url, $stores);
        }
    }

    /**
     * @param bool $deleteOld
     */
    public function recreateUrlRewritesFromConfig(bool $deleteOld = false)
    {
        if ($deleteOld) {
            $this->removeRewrites($this->getStoreFinderRewriteCollection());
        }

        $stores = [];
        foreach ($this->storeManager->getStores() as $store) {
            $stores[] = $store->getId();
        }

        $this->addRewriteFromConfig(
            DataHelper::CONFIG_PATH_SEO_STOREFINDER,
            DataHelper::URL_STORE_FINDER,
            $stores
        );
        $this->addRewriteFromConfig(
            DataHelper::CONFIG_PATH_SEO_STORELIST,
            DataHelper::URL_STORELIST,
            $stores
        );
        $this->addRewriteFromConfig(
            DataHelper::CONFIG_PATH_SEO_EVENTLIST,
            DataHelper::URL_EVENTLIST,
            $stores
        );
        $this->addRewriteFromConfig(
            DataHelper::CONFIG_PATH_SEO_STOREDETAILS,
            DataHelper::URL_STORE_DETAIL,
            $stores
        );
    }

    /**
     * @param string|array $targetPath
     * @param int $storeId
     * @return UrlRewriteCollection
     */
    public function getExistingRewrites($targetPath, $storeId = null)
    {
        /** @var UrlRewriteCollection $rewriteCollection */
        $rewriteCollection = $this->rewriteCollectionFactory->create();

        if (!is_array($targetPath)) {
            $targetPath = [$targetPath];
        }
        $rewriteCollection->addFieldToFilter(UrlRewriteData::TARGET_PATH, ['in' => $targetPath]);
        if (is_numeric($storeId) || is_array($storeId)) {
            $rewriteCollection->addStoreFilter($storeId, false);
        }

        return $rewriteCollection;
    }

    /**
     * @param string $targetUrl
     * @param int $storeId
     * @return UrlRewrite
     */
    public function getStoreFinderRewrite(string $targetUrl, $storeId)
    {
        if (isset($this->getStoreFinderRewrites()[$targetUrl][$storeId])) {
            return $this->getStoreFinderRewrites()[$targetUrl][$storeId];
        }

        return $this->urlRewriteFactory->create();
    }

    /**
     * @param UrlRewriteCollection $rewriteCollection
     */
    public function removeRewrites($rewriteCollection)
    {
        $rewriteCollection->walk('delete');
    }

    /**
     * @param string $request
     * @param string $target
     * @param int $storeId
     */
    public function createRewrite(
        string $request,
        string $target,
        int $storeId
    ) {
        try {
            $urlRewriteModel = $this->getStoreFinderRewrite($target, $storeId);
            $urlRewriteModel->setStoreId($storeId);
            $urlRewriteModel->setIsSystem(1);
            $urlRewriteModel->setTargetPath($target);
            $urlRewriteModel->setRequestPath($request);
            $this->rewriteResource->save($urlRewriteModel);
        } catch (Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
    }
}
