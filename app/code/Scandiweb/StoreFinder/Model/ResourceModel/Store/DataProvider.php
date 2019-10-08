<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Model\ResourceModel\Store;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Scandiweb\StoreFinder\Controller\Adminhtml\AbstractStore;
use Scandiweb\StoreFinder\Controller\Adminhtml\Stores\Save;
use Scandiweb\StoreFinder\Model\Store as StoreModel;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var string
     */
    protected $mediaUrl;

    /**
     * @var FileInfo
     */
    protected $fileInfo;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $storeCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param FileInfo $fileInfo
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $storeCollectionFactory,
        DataPersistorInterface $dataPersistor,
        FileInfo $fileInfo,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $storeCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->fileInfo = $fileInfo;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (!isset($this->loadedData)) {
            $this->loadedData = [];
            /** @var StoreModel $item */
            foreach ($this->collection->getItems() as $item) {
                $data = $item->getData();
                $data[StoreModel::COLUMN_IMAGE] = $item->getBaseImage()
                    ? $this->getImageData($item->getBaseImage())
                    : null;
                $data[Save::KEY_EXTRA_IMAGE_ARRAY] = $this->getImageData($item->getImages());
                foreach ($data[Save::KEY_EXTRA_IMAGE_ARRAY] as $key => $image) {
                    $data[sprintf('%s[%s]', Save::KEY_EXTRA_IMAGE_ARRAY, $key)] = $image;
                }
                unset($data[Save::KEY_EXTRA_IMAGE_ARRAY]);
                $this->loadedData[$item->getId()] = $data;
            }
        }

        $data = $this->dataPersistor->get(AbstractStore::PERSISTOR_KEY_CURRENT_STORE);
        if (!empty($data)) {
            /** @var StoreModel $store */
            $store = $this->collection->getNewEmptyItem();
            $store->setData($data);
            $storeData = $store->getData();
            foreach ($storeData[Save::KEY_EXTRA_IMAGE_ARRAY] as $key => $image) {
                $storeData[sprintf('%s[%s]', Save::KEY_EXTRA_IMAGE_ARRAY, $key)] = $image;
            }
            unset($storeData[Save::KEY_EXTRA_IMAGE_ARRAY]);
            $this->loadedData[$store->getId()] = $storeData;
            $this->dataPersistor->clear(AbstractStore::PERSISTOR_KEY_CURRENT_STORE);
        }

        return $this->loadedData;
    }

    /**
     * @param string|array $image
     * Returns array of array on purpose because Magento
     * @return array
     */
    protected function getImageData($image)
    {
        if (is_string($image)) {
            return [[
                'name' => $image,
                'file' => $image,
                'url' => $this->fileInfo->getUrl($image),
                'type' => $this->fileInfo->getMimeType($image),
                'size' => $this->fileInfo->getSize($image)
            ]];
        }

        if (!is_array($image)) {
            return null;
        }

        $data = [];
        foreach ($image as $key => $item) {
            if (is_array($item)) {
                $data[$key] = [$item];
            } elseif (is_string($item)) {
                $data[$key] = [
                    [
                        'name' => $item,
                        'file' => $item,
                        'url' => $this->fileInfo->getUrl($item),
                        'type' => $this->fileInfo->getMimeType($item),
                        'size' => $this->fileInfo->getSize($item)
                    ]
                ];
            }
        }

        return $data;
    }
}
