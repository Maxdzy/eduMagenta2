<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Api;

use Magento\Framework\Exception\NotFoundException;
use Edu\StoreFinderCompletion\Model\Event as EventModel;

interface EventRepositoryInterface
{
    /**
     * @param int $storeId
     * @return EventModel
     * @throws NotFoundException
     */
    public function getById($storeId);

    /**
     * @param EventModel $store
     * @return EventModel
     */
    public function save(EventModel $store);

    /**
     * @param EventModel $store
     * @throws NotFoundException
     */
    public function delete(EventModel $store);

    /**
     * @return EventModel
     */
    public function create();
}
