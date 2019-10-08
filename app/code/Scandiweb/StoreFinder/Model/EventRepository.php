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
use Scandiweb\StoreFinder\Api\EventRepositoryInterface;
use Scandiweb\StoreFinder\Model\ResourceModel\Event as EventResource;
use Scandiweb\StoreFinder\Model\Event as EventModel;
use Scandiweb\StoreFinder\Model\EventFactory as EventModelFactory;
use Throwable;

class EventRepository implements EventRepositoryInterface
{
    /**
     * @var EventResource
     */
    protected $eventResource;

    /**
     * @var EventModelFactory
     */
    protected $eventFactory;

    /**
     * StoreRepository constructor.
     * @param EventResource $eventResource
     * @param EventModelFactory $eventFactory
     */
    public function __construct(
        EventResource $eventResource,
        EventModelFactory $eventFactory
    ) {
        $this->eventResource = $eventResource;
        $this->eventFactory = $eventFactory;
    }

    /**
     * @param int $storeId
     * @return EventModel
     * @throws NotFoundException
     */
    public function getById($storeId)
    {
        $store = $this->create();
        $this->eventResource->load($store, $storeId);

        if (!$store->getId()) {
            throw new NotFoundException(__('Event with ID %1 does not exist.', $storeId));
        }

        return $store;
    }

    /**
     * @param EventModel $event
     * @return EventModel
     * @throws CouldNotSaveException
     */
    public function save(EventModel $event)
    {
        try {
            $this->eventResource->save($event);
        } catch (Throwable $exception) {
            throw new CouldNotSaveException(__('Unable to save event with ID: %s', $event->getId()));
        }

        return $event;
    }

    /**
     * @param EventModel $event
     * @throws CouldNotDeleteException
     */
    public function delete(EventModel $event)
    {
        try {
            $this->eventResource->delete($event);
        } catch (Throwable $exception) {
            throw new CouldNotDeleteException(__('Unable to delete event ID: %s', $event->getId()));
        }
    }

    /**
     * @return EventModel
     */
    public function create()
    {
        return $this->eventFactory->create();
    }
}
