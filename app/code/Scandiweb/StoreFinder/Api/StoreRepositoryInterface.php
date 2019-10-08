<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Api;

use Magento\Framework\Exception\NotFoundException;
use Scandiweb\StoreFinder\Model\Store as StoreModel;

interface StoreRepositoryInterface
{
    /**
     * @param int $storeId
     * @return StoreModel
     * @throws NotFoundException
     */
    public function getById($storeId);

    /**
     * @param string $storeIdentifier
     * @return StoreModel
     * @throws NotFoundException
     */
    public function getByIdentifier($storeIdentifier);

    /**
     * @param StoreModel $store
     * @return StoreModel
     */
    public function save(StoreModel $store);

    /**
     * @param StoreModel $store
     * @throws NotFoundException
     */
    public function delete(StoreModel $store);

    /**
     * @return StoreModel
     */
    public function create();
}
