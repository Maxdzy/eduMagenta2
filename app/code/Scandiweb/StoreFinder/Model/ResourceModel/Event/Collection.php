<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Model\ResourceModel\Event;

use InvalidArgumentException;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;
use Scandiweb\StoreFinder\Model\Event as EventModel;
use Scandiweb\StoreFinder\Model\ResourceModel\Event as EventResource;
use Scandiweb\StoreFinder\Helper\Data as DataHelper;
use Scandiweb\StoreFinder\Model\Store as StoreModel;

class Collection extends AbstractCollection
{
    const COLUMN_ALIAS_BINDS = 'binds';
    const COLUMN_ALIAS_STORES = 'stores';
    const COLUMN_ALIAS_MAIN_TABLE = 'main_table';

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var array
     */
    protected $bindings;

    /**
     * Collection constructor.
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param SerializerInterface $serializer
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        SerializerInterface $serializer,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->serializer = $serializer;
        $this->initBindings();
    }

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init(EventModel::class, EventResource::class);
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return AbstractCollection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === StoreModel::COLUMN_COUNTRY) {
            $bindTableName = $this->getConnection()->getTableName(DataHelper::TABLE_STORE_EVENT_BIND);
            $storeTableName = $this->getConnection()->getTableName(DataHelper::TABLE_STORES);
            $this->_select->joinInner(
                [self::COLUMN_ALIAS_BINDS => $bindTableName],
                sprintf(
                    '%s.%s = %s.%s',
                    self::COLUMN_ALIAS_MAIN_TABLE,
                    EventModel::COLUMN_EVENT_ID,
                    self::COLUMN_ALIAS_BINDS,
                    EventModel::COLUMN_EVENT_ID
                ),
                []
            )->joinInner(
                [self::COLUMN_ALIAS_STORES => $storeTableName],
                sprintf(
                    '%s.%s = %s.%s',
                    self::COLUMN_ALIAS_BINDS,
                    StoreModel::COLUMN_STORE_ID,
                    self::COLUMN_ALIAS_STORES,
                    StoreModel::COLUMN_STORE_ID
                ),
                []
            )->distinct(true);
            $field = sprintf('%s.%s', self::COLUMN_ALIAS_STORES, StoreModel::COLUMN_COUNTRY);
        }

        if ($field === StoreModel::COLUMN_STORE_ID) {
            $bindTableName = $this->getConnection()->getTableName(DataHelper::TABLE_STORE_EVENT_BIND);
            $this->_select->joinInner(
                [self::COLUMN_ALIAS_BINDS => $bindTableName],
                sprintf(
                    '%s.%s = %s.%s',
                    self::COLUMN_ALIAS_MAIN_TABLE,
                    EventModel::COLUMN_EVENT_ID,
                    self::COLUMN_ALIAS_BINDS,
                    EventModel::COLUMN_EVENT_ID
                ),
                []
            )->distinct(true);
            $field = sprintf('%s.%s', self::COLUMN_ALIAS_BINDS, StoreModel::COLUMN_STORE_ID);
        }

        if ($field === EventModel::COLUMN_EVENT_ID) {
            $field = sprintf('%s.%s', self::COLUMN_ALIAS_MAIN_TABLE, EventModel::COLUMN_EVENT_ID);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Unserialize image Json array
     */
    protected function _afterLoad()
    {
        /** @var EventModel $item */
        foreach ($this->_items as &$item) {
            $item->setData(
                EventModel::COLUMN_RSVP_OPTIONS,
                $this->unserialize(
                    $item->getData(EventModel::COLUMN_RSVP_OPTIONS)
                )
            );
            $storeIds = $this->getBindings($item->getEventId());
            $item->setStoreIds($storeIds);
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
     * @param int $storeId
     * @return array
     */
    protected function getBindings(int $storeId)
    {
        if (array_key_exists($storeId, $this->bindings)) {
            return $this->bindings[$storeId];
        }

        return [];
    }

    /**
     * Fetch bindings from DB for caching
     */
    protected function initBindings()
    {
        $connection = $this->getConnection();
        $bindTableName = $connection->getTableName(DataHelper::TABLE_STORE_EVENT_BIND);

        $bindSelect = $connection->select()->from($bindTableName);

        $binds = $connection->fetchAll($bindSelect);
        $this->bindings = [];
        foreach ($binds as $bind) {
            $storeId = $bind[StoreModel::COLUMN_STORE_ID];
            $eventId = $bind[EventModel::COLUMN_EVENT_ID];
            if (!array_key_exists($eventId, $this->bindings)) {
                $this->bindings[$eventId] = [];
            }
            $this->bindings[$eventId][] = $storeId;
        }
    }

    /**
     * @param array $storeIds
     * @return array
     */
    protected function getStoreAddresses($storeIds)
    {
        $connection = $this->getConnection();
        $storeTableName = $connection->getTableName(DataHelper::TABLE_STORES);

        $addressSelect = $connection->select()
            ->from($storeTableName, StoreModel::COLUMN_ADDRESS)
            ->where(EventModel::COLUMN_EVENT_ID . ' IN (?)', $storeIds);

        return $connection->fetchCol($addressSelect);
    }
}
