<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NotFoundException;
use Scandiweb\StoreFinder\Api\StoreRepositoryInterface;
use Scandiweb\StoreFinder\Model\ResourceModel\Store as StoreResource;
use Scandiweb\StoreFinder\Model\Store as StoreModel;
use Scandiweb\StoreFinder\Model\StoreFactory as StoreModelFactory;
use Throwable;

class StoreRepository implements StoreRepositoryInterface
{
    /**
     * @var StoreResource
     */
    protected $storeResource;

    /**
     * @var StoreFactory
     */
    protected $storeFactory;

    /**
     * StoreRepository constructor.
     * @param StoreResource $storeResource
     * @param StoreFactory $storeFactory
     */
    public function __construct(
        StoreResource $storeResource,
        StoreModelFactory $storeFactory
    ) {
        $this->storeResource = $storeResource;
        $this->storeFactory = $storeFactory;
    }

    /**
     * @param int $storeId
     * @return StoreModel
     * @throws NotFoundException
     */
    public function getById($storeId)
    {
        $store = $this->create();
        $this->storeResource->load($store, $storeId);

        if (!$store->getId()) {
            throw new NotFoundException(__('Store with ID %1 does not exist.', $storeId));
        }

        return $store;
    }

    /**
     * @param string $storeIdentifier
     * @return StoreModel
     * @throws NotFoundException
     */
    public function getByIdentifier($storeIdentifier)
    {
        $store = $this->create();
        $this->storeResource->load($store, $storeIdentifier, StoreModel::COLUMN_STORE_IDENTIFIER);

        if (!$store->getId()) {
            throw new NotFoundException(__('Store with identifier %1 does not exist.', $storeIdentifier));
        }

        return $store;
    }

    /**
     * @param StoreModel $store
     * @return StoreModel
     * @throws CouldNotSaveException
     */
    public function save(StoreModel $store)
    {
        try {
            $this->storeResource->save($store);
        } catch (Throwable $exception) {
            throw new CouldNotSaveException(__('Unable to save store with identifier: %1', [$store->getIdentifier()]));
        }

        return $store;
    }

    /**
     * @param StoreModel $store
     * @throws CouldNotDeleteException
     */
    public function delete(StoreModel $store)
    {
        try {
            $this->storeResource->delete($store);
        } catch (Throwable $exception) {
            throw new CouldNotDeleteException(__('Unable to delete store ID: %s', $store->getId()));
        }
    }

    /**
     * @return StoreModel
     */
    public function create()
    {
        return $this->storeFactory->create();
    }
}
