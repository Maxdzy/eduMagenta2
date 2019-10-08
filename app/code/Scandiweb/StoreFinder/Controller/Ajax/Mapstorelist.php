<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Controller\Ajax;

use Exception;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Scandiweb\StoreFinder\Helper\Map;
use Scandiweb\StoreFinder\Model\ResourceModel\Store\Collection as StoreCollection;
use Scandiweb\StoreFinder\Helper\Data as DataHelper;
use Scandiweb\StoreFinder\Model\Store as StoreModel;

class Mapstorelist extends Storelist
{
    const MORE_DETAILS_URL = 'more_details_url';

    /**
     * @var array
     */
    protected $distances;

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $json */
        $json = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $stores = $this->getStoreCollection()->getItems();
            $storeData = [];

            /** @var StoreModel $store */
            foreach ($this->getDistances() as $key => $distance) {
                if (!array_key_exists($key, $stores)) {
                    continue;
                }
                $store = $stores[$key];
                $data = $this->getStoreData($store);
                $data[Map::KEY_DISTANCE] = $distance[Map::KEY_DISTANCE];
                $storeData[] = $data;
            }

            $data = [];
            $data[DataHelper::JSON_KEY_STATUS] = 'success';
            $data[DataHelper::JSON_KEY_STORES] = $storeData;
            $data[DataHelper::JSON_KEY_PARAMS] = $this->getParams();
            $data[DataHelper::JSON_KEY_TOTAL_SIZE] = count($this->getDistances());
            $json->setData($data);
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
        $storeData = $store->getData();

        $storeData[StoreModel::COLUMN_IMAGE] = [];
        foreach ($store->getImages() as $key => $image) {
            $storeData[StoreModel::COLUMN_IMAGE][$key] = $this->fileInfo->getUrl($image);
        }

        $baseImage = $store->getBaseImage();
        if ($baseImage === null) {
            $storeData['base_image_url'] = $this->dataHelper->getViewFileUrl(
                'Scandiweb_StoreFinder::images/store-placeholder.png'
            );
            $storeData['image_placeholder'] = 'placeholder';
        } else {
            $storeData['base_image_url'] = $this->fileInfo->getUrl($baseImage);
            $storeData['image_placeholder'] = '';
        }

        $storeData[StoreModel::COLUMN_ADDRESS] = nl2br($store->getAddress());

        $storeData[StoreModel::COLUMN_ADDITIONAL_INFO] = $store->getAdditionalInfoHtml();
        $storeData[StoreModel::COLUMN_STORE_HOURS] = $store->getStoreHourHtml();

        $storeData[Map::KEY_DISTANCE] = $this->getDistances()[$store->getId()][Map::KEY_DISTANCE];

        if ($store->getHasStorePage()) {
            $storeData[self::MORE_DETAILS_URL] = $this->dataHelper->getStorePageUrl($store);
        }

        return $storeData;
    }

    /**
     * @return array
     */
    protected function processParams()
    {
        $params = parent::processParams();

        $distances = array_keys($this->dataHelper->getDistances());
        $params[DataHelper::URI_PARAM_DISTANCE] = $this->getRequest()->getParam(
            DataHelper::URI_PARAM_DISTANCE,
            array_key_exists(0, $distances) ? $distances[0] : null
        );

        $params[DataHelper::URI_PARAM_LATITUDE] = $this->getRequest()->getParam(
            DataHelper::URI_PARAM_LATITUDE,
            $this->dataHelper->getDefaultLatitude()
        );
        $params[DataHelper::URI_PARAM_LONGITUDE] = $this->getRequest()->getParam(
            DataHelper::URI_PARAM_LONGITUDE,
            $this->dataHelper->getDefaultLongitude()
        );

        $params[DataHelper::URI_PARAM_LAST_INDEX] = $this->getRequest()->getParam(
            DataHelper::URI_PARAM_LAST_INDEX,
            1
        );

        $params[DataHelper::URI_PARAM_BOUNDS] = $this->getRequest()->getParam(
            DataHelper::URI_PARAM_BOUNDS,
            null
        );

        return $params;
    }

    /**
     * @return array
     */
    protected function getDistances()
    {
        if ($this->distances === null) {
            $bounds = $this->getParams()[DataHelper::URI_PARAM_BOUNDS];
            if ($this->isValidBounds($bounds)) {
                $this->distances = $this->dataHelper->getMapHelper()->getStoresWithinBounds(
                    $bounds[DataHelper::BOUNDS_PARAM_LAT_MAX],
                    $bounds[DataHelper::BOUNDS_PARAM_LNG_MAX],
                    $bounds[DataHelper::BOUNDS_PARAM_LAT_MIN],
                    $bounds[DataHelper::BOUNDS_PARAM_LNG_MIN],
                    $this->getParams()[DataHelper::URI_PARAM_LATITUDE],
                    $this->getParams()[DataHelper::URI_PARAM_LONGITUDE]
                );
            } else {
                $this->distances = $this->dataHelper->getMapHelper()->getCloseStoreLocations(
                    $this->getParams()[DataHelper::URI_PARAM_LATITUDE],
                    $this->getParams()[DataHelper::URI_PARAM_LONGITUDE],
                    $this->getParams()[DataHelper::URI_PARAM_DISTANCE]
                );
            }
        }

        return $this->distances;
    }

    /**
     * @return StoreCollection
     */
    protected function createStoreCollection()
    {
        $collection = parent::createStoreCollection();

        if ($this->getParams()[DataHelper::URI_PARAM_DISTANCE] !== null) {
            $collection->addFieldToFilter(StoreModel::COLUMN_STORE_ID, ['in' => array_keys($this->getDistances())]);
        }

        return $collection;
    }

    /**
     * @param array $bounds
     * @return bool
     */
    protected function isValidBounds($bounds)
    {
        return is_array($bounds) && (array_key_exists(DataHelper::BOUNDS_PARAM_LAT_MAX, $bounds)
                & array_key_exists(DataHelper::BOUNDS_PARAM_LNG_MAX, $bounds)
                & array_key_exists(DataHelper::BOUNDS_PARAM_LAT_MIN, $bounds)
                & array_key_exists(DataHelper::BOUNDS_PARAM_LNG_MIN, $bounds));
    }
}
