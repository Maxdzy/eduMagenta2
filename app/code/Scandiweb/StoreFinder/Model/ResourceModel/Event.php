<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Model\ResourceModel;

use InvalidArgumentException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Serialize\SerializerInterface;
use Scandiweb\StoreFinder\Helper\Data as DataHelper;
use Scandiweb\StoreFinder\Model\Event as EventModel;
use Scandiweb\StoreFinder\Model\Store as StoreModel;

class Event extends AbstractDb
{
    /**
     * Store constructor.
     * @param Context $context
     * @param SerializerInterface $serializer
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        SerializerInterface $serializer,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->serializer = $serializer;
    }

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init(DataHelper::TABLE_EVENTS, EventModel::COLUMN_EVENT_ID);
    }

    /**
     * @param AbstractModel $object
     * @return AbstractDb
     */
    protected function _beforeSave(AbstractModel $object)
    {
        parent::_beforeSave($object);

        /** @var EventModel $eventModel */
        $eventModel = $object;

        $object->setData(
            EventModel::COLUMN_RSVP_OPTIONS,
            $this->serialize($eventModel->getRsvpOptions())
        );

        return $this;
    }

    /**
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        parent::_afterSave($object);

        if ($object instanceof EventModel) {
            $this->updateBinds($object);
        }

        return $this;
    }

    /**
     * @param AbstractModel $object
     * @return AbstractDb
     */
    protected function _afterLoad(AbstractModel $object)
    {
        parent::_afterLoad($object);

        /** @var EventModel $eventModel */
        $eventModel = &$object;

        $eventModel->setRsvpOptions(
            $this->unserialize($eventModel->getData(EventModel::COLUMN_RSVP_OPTIONS))
        );

        $eventModel->setStoreIds($this->getBindings($eventModel->getId()));

        return $this;
    }

    /**
     * @param array $data
     * @return string
     */
    protected function serialize($data)
    {
        try {
            return $this->serializer->serialize($data) ?: '{}';
        } catch (InvalidArgumentException $exception) {
            return '{}';
        }
    }

    /**
     * @param string $data
     * @return array
     */
    protected function unserialize($data)
    {
        try {
            $jsonOptions = $this->serializer->unserialize($data);
            if (!is_array($jsonOptions)) {
                $jsonOptions = [];
            }
        } catch (InvalidArgumentException $exception) {
            $jsonOptions = [];
        }

        return $jsonOptions;
    }

    /**
     * @param int $eventId
     * @return array
     */
    protected function getBindings($eventId)
    {
        if ($eventId === null) {
            return [];
        }

        $connection = $this->getConnection();
        $bindTableName = $connection->getTableName(DataHelper::TABLE_STORE_EVENT_BIND);

        $bindSelect = $connection->select()
            ->from($bindTableName, StoreModel::COLUMN_STORE_ID)
            ->where(EventModel::COLUMN_EVENT_ID . ' = ?', $eventId);

        return $connection->fetchCol($bindSelect);
    }

    /**
     * @param EventModel $eventModel
     */
    protected function updateBinds(EventModel $eventModel)
    {
        $connection = $this->getConnection();
        $bindTableName = $connection->getTableName(DataHelper::TABLE_STORE_EVENT_BIND);

        $storesDb = $this->getBindings($eventModel->getEventId());
        $storesModel = $eventModel->getStoreIds();
        if (!is_array($storesModel)) {
            $storesModel = [];
        }

        $bindsToDelete = array_diff($storesDb, $storesModel);
        $bindsToCreate = array_diff($storesModel, $storesDb);

        if (!empty($bindsToDelete)) {
            $connection->delete(
                $bindTableName,
                [
                    EventModel::COLUMN_EVENT_ID . ' = ?' => $eventModel->getEventId(),
                    StoreModel::COLUMN_STORE_ID . ' IN (?)' => $bindsToDelete
                ]
            );
        }

        if (!empty($bindsToCreate)) {
            $saveData = [];
            foreach ($bindsToCreate as $storeId) {
                $saveData[] = [
                    $eventModel->getEventId(),
                    $storeId
                ];
            }
            $connection->insertArray(
                $bindTableName,
                [
                    EventModel::COLUMN_EVENT_ID,
                    StoreModel::COLUMN_STORE_ID
                ],
                $saveData
            );
        }
    }
}
