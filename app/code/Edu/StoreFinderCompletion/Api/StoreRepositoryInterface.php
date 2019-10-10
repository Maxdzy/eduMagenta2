<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Api;

use Magento\Framework\Exception\NotFoundException;
use Edu\StoreFinderCompletion\Model\Store as StoreModel;

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
