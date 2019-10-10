<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>
 */

namespace Edu\StoreFinderCompletion\Controller\Ajax;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;
use Edu\StoreFinderCompletion\Model\ResourceModel\Store\Collection as StoreCollection;
use Edu\StoreFinderCompletion\Model\ResourceModel\Store\CollectionFactory as StoreCollectionFactory;
use Edu\StoreFinderCompletion\Helper\Data as DataHelper;
use Edu\StoreFinderCompletion\Model\ResourceModel\Store\FileInfo;
use Edu\StoreFinderCompletion\Model\Store as StoreModel;

class Storelist extends Action
{
    const CACHE_TIME_TO_LIVE_IN_SECONDS = 86400;

    /**
     * @var StoreCollectionFactory
     */
    protected $storeCollectionFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var FileInfo
     */
    protected $fileInfo;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var StoreCollection
     */
    protected $storeCollection;

    /**
     * @var array
     */
    protected $params;

    /**
     * Storelist constructor.
     * @param Context $context
     * @param StoreCollectionFactory $storeCollectionFactory
     * @param LoggerInterface $logger
     * @param FileInfo $fileInfo
     * @param DataHelper $dataHelper
     * @internal param UrlInterface $urlBuilder
     */
    public function __construct(
        Context $context,
        StoreCollectionFactory $storeCollectionFactory,
        LoggerInterface $logger,
        FileInfo $fileInfo,
        DataHelper $dataHelper
    ) {
        parent::__construct($context);
        $this->storeCollectionFactory = $storeCollectionFactory;
        $this->logger = $logger;
        $this->fileInfo = $fileInfo;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var Json $json */
        $json = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $stores = $this->getStoreCollection()->getItems();
            $storeData = [];

            /** @var StoreModel $store */
            foreach ($stores as $store) {
                $storeData[] = $this->getStoreData($store);
            }

            $data = [];
            $data[DataHelper::JSON_KEY_STATUS] = 'success';
            $data[DataHelper::JSON_KEY_STORES] = $storeData;
            $data[DataHelper::JSON_KEY_PARAMS] = $this->getParams();
            $data[DataHelper::JSON_KEY_TOTAL_SIZE] = $this->getStoreCollection()->getSize();
            $json->setData($data);
            $this->getResponse()->setPublicHeaders(static::CACHE_TIME_TO_LIVE_IN_SECONDS);
        } catch (Exception $exception) {
            $json->setData([
                DataHelper::JSON_KEY_STATUS => 'error',
                DataHelper::JSON_KEY_MESSAGE => __('Something went wrong')
            ]);
            $this->logger->error($exception->getMessage());
        }

        return $json;
    }

    /**
     * @param StoreModel $store
     * @return array
     */
    protected function getStoreData(StoreModel $store)
    {
        $data = $store->getData();

        $data[StoreModel::COLUMN_IMAGE] = [];
        foreach ($store->getImages() as $key => $image) {
            $data[StoreModel::COLUMN_IMAGE][$key] = $this->fileInfo->getUrl($image);
        }

        $baseImage = $store->getBaseImage();
        if ($baseImage === null) {
            $data['base_image_url'] = $this->dataHelper->getViewFileUrl(
                'Edu_StoreFinderCompletion::images/store-placeholder.png'
            );
        } else {
            $data['base_image_url'] = $this->fileInfo->getUrl($baseImage);
        }

        $data[StoreModel::COLUMN_ADDRESS] = nl2br($store->getAddress());

        $data[StoreModel::COLUMN_ADDITIONAL_INFO] = $store->getAdditionalInfoHtml();
        $data[StoreModel::COLUMN_STORE_HOURS] = $store->getStoreHourHtml();

        $data['detail_page_url'] = $this->dataHelper->getStorePageUrl($store);

        return $data;
    }

    protected function processParams()
    {
        $params = $this->getRequest()->getParams();

        $limit = $this->getRequest()
            ->getParam(DataHelper::URI_PARAM_LIMIT, null);
        $params[DataHelper::URI_PARAM_LIMIT] = is_numeric($limit) ? (int)$limit : null;

        $params[DataHelper::URI_PARAM_PAGE] = (int)$this->getRequest()
            ->getParam(DataHelper::URI_PARAM_PAGE, 1);

        $storeTypes = $this->getRequest()->getParam(DataHelper::URI_PARAM_STORE_TYPE, null);
        if ($storeTypes !== null) {
            $storeTypes = explode(DataHelper::URI_PARAM_DELIMITER, $storeTypes);
        }
        $params[DataHelper::URI_PARAM_STORE_TYPE] = $this->validateStoreTypes($storeTypes);

        $storeCountries = $this->getRequest()->getParam(DataHelper::URI_PARAM_STORE_COUNTRY, null);
        if ($storeCountries !== null) {
            $storeCountries = explode(DataHelper::URI_PARAM_DELIMITER, $storeCountries);
        }
        $params[DataHelper::URI_PARAM_STORE_COUNTRY] = $this->validateStoreCountries($storeCountries);

        return $params;
    }

    /**
     * @return array
     */
    protected function getParams()
    {
        if (!isset($this->params)) {
            $this->params = $this->processParams();
        }

        return $this->params;
    }

    /**
     * @return StoreCollection
     */
    protected function createStoreCollection()
    {
        $limit = $this->getParams()[DataHelper::URI_PARAM_LIMIT];
        $page = $this->getParams()[DataHelper::URI_PARAM_PAGE];
        $storeTypes = $this->getParams()[DataHelper::URI_PARAM_STORE_TYPE];
        $storeCountries = $this->getParams()[DataHelper::URI_PARAM_STORE_COUNTRY];

        /** @var StoreCollection $collection */
        $collection = $this->storeCollectionFactory->create();
        if ($this->getParams()[DataHelper::URI_PARAM_LIMIT] !== null) {
            $collection->setPageSize($limit);
            $collection->setCurPage($page);
        }
        if ($storeTypes !== null) {
            $collection->addFieldToFilter(StoreModel::COLUMN_STORE_TYPE, ['in' => $storeTypes]);
        }
        if ($storeCountries !== null) {
            $collection->addFieldToFilter(StoreModel::COLUMN_COUNTRY, ['in' => $storeCountries]);
        }

        $collection->addFieldToFilter(StoreModel::COLUMN_IS_ACTIVE, ['eq' => DataHelper::STATUS_ENABLED]);
        $collection->setOrder('position', StoreCollection::SORT_ORDER_ASC);

        return $collection;
    }

    /**
     * @return StoreCollection
     */
    protected function getStoreCollection()
    {
        if ($this->storeCollection === null) {
            $this->storeCollection = $this->createStoreCollection();
        }

        return $this->storeCollection;
    }

    /**
     * @param array|null $data
     * @param array $allowed
     * @return array|null
     */
    protected function validateArray($data, $allowed)
    {
        if (!is_array($data)) {
            return null;
        }

        foreach ($data as $key => $item) {
            if (!in_array($item, $allowed)) {
                unset($data[$key]);
            }
        }

        if (empty($data)) {
            $data = null;
        }

        return $data;
    }

    /**
     * @param array|null $data
     * @return array|null
     */
    protected function validateStoreTypes($data)
    {
        return $this->validateArray($data, array_keys($this->dataHelper->getAllowedStoreTypes()));
    }

    /**
     * @param array|null $data
     * @return array|null
     */
    protected function validateStoreCountries($data)
    {
        return $this->validateArray($data, array_keys($this->dataHelper->getAllowedCountries()));
    }
}
