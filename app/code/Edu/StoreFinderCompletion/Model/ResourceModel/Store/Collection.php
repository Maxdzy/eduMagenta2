<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Model\ResourceModel\Store;

use InvalidArgumentException;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;
use Edu\StoreFinderCompletion\Model\ResourceModel\Store as StoreResource;
use Edu\StoreFinderCompletion\Helper\Data as DataHelper;
use Edu\StoreFinderCompletion\Model\Event as EventModel;
use Edu\StoreFinderCompletion\Model\Store as StoreModel;

class Collection extends AbstractCollection
{
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
        $this->_init(StoreModel::class, StoreResource::class);
    }

    /**
     * Unserialize image Json array
     */
    protected function _afterLoad()
    {
        /** @var StoreModel $item */
        foreach ($this->_items as &$item) {
            try {
                $jsonOptions = $this->serializer->unserialize(
                    $item->getData(StoreModel::COLUMN_IMAGE)
                );
                if (!is_array($jsonOptions)) {
                    $jsonOptions = [];
                }
            } catch (InvalidArgumentException $exception) {
                $jsonOptions = [];
            }

            $item->setImages($jsonOptions);
            $item->setEventIds($this->getBindings($item->getStoreId()));
        }
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
            if (!array_key_exists($storeId, $this->bindings)) {
                $this->bindings[$storeId] = [];
            }
            $this->bindings[$storeId][] = $eventId;
        }
    }
}
