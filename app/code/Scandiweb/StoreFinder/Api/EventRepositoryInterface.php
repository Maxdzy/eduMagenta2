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
use Scandiweb\StoreFinder\Model\Event as EventModel;

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
