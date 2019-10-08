<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @author      Emils Brass <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Scandiweb\StoreFinder\Helper\Data as DataHelper;
use Scandiweb\StoreFinder\Model\Rsvp as RsvpModel;
use Scandiweb\StoreFinder\Model\Store as StoreModel;
use Scandiweb\StoreFinder\Model\Event as EventModel;
use Zend_Db_Exception;

class UpgradeSchema implements UpgradeSchemaInterface
{
    const NULLABLE = 'nullable';
    const UNSIGNED = 'unsigned';
    const PRIMARY = 'primary';
    const DEFAULT = 'default';
    const IDENTITY = 'identity';

    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        $this->createEventTable($connection);
        $this->createBindTable($connection);
        $this->createRsvpTable($connection);

        if (version_compare($context->getVersion(), '0.6.0', '<')) {
            $this->addLocationIndexes($connection);
        }

        if (version_compare($context->getVersion(), '1.1.3', '<')) {
            $this->addStoreIsActive($connection);
        }

        if (version_compare($context->getVersion(), '1.1.8', '<')) {
            $this->addPosition($connection);
        }

        $this->addStoreIdentifier($connection);

        $installer->endSetup();
    }

    /**
     * @param AdapterInterface $connection
     * @throws Zend_Db_Exception
     */
    private function createEventTable($connection)
    {
        $eventTableName = $connection->getTableName(DataHelper::TABLE_EVENTS);
        if (!$connection->isTableExists($eventTableName)) {
            $eventTable = $connection->newTable($eventTableName)->addColumn(
                EventModel::COLUMN_EVENT_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    self::IDENTITY => true,
                    self::UNSIGNED => true,
                    self::NULLABLE => false,
                    self::PRIMARY => true
                ],
                'Event ID'
            )->addColumn(
                EventModel::COLUMN_NAME,
                Table::TYPE_TEXT,
                255,
                [
                    self::NULLABLE => false
                ],
                'Event Name'
            )->addColumn(
                EventModel::COLUMN_LOCATION,
                Table::TYPE_TEXT,
                255,
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'Event Location'
            )->addColumn(
                EventModel::COLUMN_DATETIME_START,
                Table::TYPE_DATETIME,
                null,
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'DateTime Event Start'
            )->addColumn(
                EventModel::COLUMN_DATETIME_END,
                Table::TYPE_DATETIME,
                null,
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'DateTime Event End'
            )->addColumn(
                EventModel::COLUMN_CUSTOM_TIME,
                Table::TYPE_TEXT,
                255,
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'Custom Time String'
            )->addColumn(
                EventModel::COLUMN_ADDITIONAL_INFO,
                Table::TYPE_TEXT,
                null,
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'Additional Info'
            )->addColumn(
                EventModel::COLUMN_RSVP_OPTIONS,
                Table::TYPE_TEXT,
                null,
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'RSVP Options'
            )->addIndex(
                $connection->getIndexName($eventTableName, [EventModel::COLUMN_DATETIME_START]),
                [EventModel::COLUMN_DATETIME_START]
            )->addIndex(
                $connection->getIndexName($eventTableName, [EventModel::COLUMN_DATETIME_END]),
                [EventModel::COLUMN_DATETIME_END]
            );

            $connection->createTable($eventTable);
        }
    }

    /**
     * @param AdapterInterface $connection
     * @throws Zend_Db_Exception
     */
    private function createBindTable($connection)
    {
        $bindTableName = $connection->getTableName(DataHelper::TABLE_STORE_EVENT_BIND);
        if (!$connection->isTableExists($bindTableName)) {
            $bindTable = $connection->newTable($bindTableName)->addColumn(
                EventModel::COLUMN_EVENT_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    self::UNSIGNED => true,
                    self::NULLABLE => false
                ],
                'Event ID'
            )->addColumn(
                StoreModel::COLUMN_STORE_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    self::UNSIGNED => true,
                    self::NULLABLE => false
                ],
                'Store ID'
            )->addIndex(
                $connection->getIndexName($bindTableName, [EventModel::COLUMN_EVENT_ID]),
                [EventModel::COLUMN_EVENT_ID]
            )->addIndex(
                $connection->getIndexName($bindTableName, [StoreModel::COLUMN_STORE_ID]),
                [StoreModel::COLUMN_STORE_ID]
            )->addIndex(
                $connection->getIndexName(
                    $bindTableName,
                    [
                        EventModel::COLUMN_EVENT_ID,
                        StoreModel::COLUMN_STORE_ID
                    ]
                ),
                [
                    EventModel::COLUMN_EVENT_ID,
                    StoreModel::COLUMN_STORE_ID
                ],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            );

            $eventTableName = $connection->getTableName(DataHelper::TABLE_EVENTS);
            $storeTableName = $connection->getTableName(DataHelper::TABLE_STORES);
            $bindTable->addForeignKey(
                $connection->getForeignKeyName(
                    $bindTableName,
                    EventModel::COLUMN_EVENT_ID,
                    $eventTableName,
                    EventModel::COLUMN_EVENT_ID
                ),
                EventModel::COLUMN_EVENT_ID,
                $eventTableName,
                EventModel::COLUMN_EVENT_ID,
                AdapterInterface::FK_ACTION_CASCADE
            )->addForeignKey(
                $connection->getForeignKeyName(
                    $bindTableName,
                    StoreModel::COLUMN_STORE_ID,
                    $storeTableName,
                    StoreModel::COLUMN_STORE_ID
                ),
                StoreModel::COLUMN_STORE_ID,
                $storeTableName,
                StoreModel::COLUMN_STORE_ID,
                AdapterInterface::FK_ACTION_CASCADE
            );

            $connection->createTable($bindTable);
        }
    }

    /**
     * @param AdapterInterface $connection
     * @throws Zend_Db_Exception
     */
    private function createRsvpTable($connection)
    {
        $rsvpTableName = $connection->getTableName(DataHelper::TABLE_EVENT_RSVP);
        if (!$connection->isTableExists($rsvpTableName)) {
            $rsvpTable = $connection->newTable($rsvpTableName)->addColumn(
                RsvpModel::COLUMN_RSVP_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    self::IDENTITY => true,
                    self::UNSIGNED => true,
                    self::NULLABLE => false,
                    self::PRIMARY => true
                ],
                'Rsvp ID'
            )->addColumn(
                RsvpModel::COLUMN_EVENT_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    self::UNSIGNED => true,
                    self::NULLABLE => false
                ],
                'Event ID'
            )->addColumn(
                RsvpModel::COLUMN_STORE_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    self::UNSIGNED => true,
                    self::NULLABLE => false
                ],
                'Store ID'
            )->addColumn(
                RsvpModel::COLUMN_EMAIL,
                Table::TYPE_TEXT,
                255,
                [
                    self::NULLABLE => false
                ],
                'Subscriber Email'
            )->addColumn(
                RsvpModel::COLUMN_FIRSTNAME,
                Table::TYPE_TEXT,
                255,
                [
                    self::NULLABLE => true
                ],
                'Subscriber Firstname'
            )->addColumn(
                RsvpModel::COLUMN_LASTNAME,
                Table::TYPE_TEXT,
                255,
                [
                    self::NULLABLE => true
                ],
                'Subscriber Lastname'
            )->addColumn(
                RsvpModel::COLUMN_TIME_CREATED,
                Table::TYPE_TIMESTAMP,
                null,
                [
                    self::NULLABLE => false,
                    self::DEFAULT => Table::TIMESTAMP_INIT
                ],
                'Created At'
            )->addColumn(
                RsvpModel::COLUMN_TIME_UPDATED,
                Table::TYPE_TIMESTAMP,
                null,
                [
                    self::NULLABLE => false,
                    self::DEFAULT => Table::TIMESTAMP_INIT_UPDATE
                ],
                'Updated At'
            )->addIndex(
                $connection->getIndexName($rsvpTableName, [RsvpModel::COLUMN_EVENT_ID]),
                [RsvpModel::COLUMN_EVENT_ID]
            )->addIndex(
                $connection->getIndexName($rsvpTableName, [RsvpModel::COLUMN_STORE_ID]),
                [RsvpModel::COLUMN_STORE_ID]
            )->addIndex(
                $connection->getIndexName($rsvpTableName, [RsvpModel::COLUMN_EMAIL]),
                [RsvpModel::COLUMN_EMAIL]
            );

            $eventTableName = $connection->getTableName(DataHelper::TABLE_EVENTS);
            $storeTableName = $connection->getTableName(DataHelper::TABLE_STORES);
            $rsvpTable->addForeignKey(
                $connection->getForeignKeyName(
                    $rsvpTableName,
                    RsvpModel::COLUMN_EVENT_ID,
                    $eventTableName,
                    EventModel::COLUMN_EVENT_ID
                ),
                RsvpModel::COLUMN_EVENT_ID,
                $eventTableName,
                EventModel::COLUMN_EVENT_ID,
                AdapterInterface::FK_ACTION_CASCADE
            )->addForeignKey(
                $connection->getForeignKeyName(
                    $rsvpTableName,
                    RsvpModel::COLUMN_STORE_ID,
                    $storeTableName,
                    StoreModel::COLUMN_STORE_ID
                ),
                RsvpModel::COLUMN_STORE_ID,
                $storeTableName,
                StoreModel::COLUMN_STORE_ID,
                AdapterInterface::FK_ACTION_CASCADE
            );

            $connection->createTable($rsvpTable);
        }
    }

    /**
     * @param AdapterInterface $connection
     */
    private function addLocationIndexes($connection)
    {
        $storeTableName = $connection->getTableName(DataHelper::TABLE_STORES);
        $connection->addIndex(
            $storeTableName,
            $connection->getIndexName($storeTableName, [StoreModel::COLUMN_LATITUDE]),
            [StoreModel::COLUMN_LATITUDE]
        );
        $connection->addIndex(
            $storeTableName,
            $connection->getIndexName($storeTableName, [StoreModel::COLUMN_LONGITUDE]),
            [StoreModel::COLUMN_LONGITUDE]
        );
    }

    /**
     * @param AdapterInterface $connection
     */
    private function addStoreIdentifier($connection)
    {
        $tableName = $connection->getTableName(DataHelper::TABLE_STORES);
        if ($connection->isTableExists($tableName)
            && !$connection->tableColumnExists($tableName, StoreModel::COLUMN_STORE_IDENTIFIER)) {
            $connection->addColumn(
                $tableName,
                StoreModel::COLUMN_STORE_IDENTIFIER,
                [
                    'type' => Table::TYPE_TEXT,
                    self::NULLABLE => false,
                    'length' => 255,
                    'comment' => 'Store Identifier',
                    'after' => StoreModel::COLUMN_STORE_ID
                ]
            );

            $connection->addIndex(
                $tableName,
                $connection->getIndexName(
                    $tableName,
                    [StoreModel::COLUMN_STORE_IDENTIFIER],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [StoreModel::COLUMN_STORE_IDENTIFIER],
                AdapterInterface::INDEX_TYPE_UNIQUE
            );
        }
    }

    /**
     * @param AdapterInterface $connection
     */
    private function addStoreIsActive($connection)
    {
        $tableName = $connection->getTableName(DataHelper::TABLE_STORES);
        if ($connection->isTableExists($tableName)
            && !$connection->tableColumnExists($tableName, StoreModel::COLUMN_IS_ACTIVE)) {
            $connection->addColumn(
                $tableName,
                StoreModel::COLUMN_IS_ACTIVE,
                [
                    'type' => Table::TYPE_BOOLEAN,
                    self::NULLABLE => false,
                    self::DEFAULT => true,
                    'comment' => 'Is Store Active'
                ]
            );
        }
    }

    /**
     * @param AdapterInterface $connection
     */
    private function addPosition($connection)
    {
        $tableName = $connection->getTableName(DataHelper::TABLE_STORES);
        if ($connection->isTableExists($tableName)
            && !$connection->tableColumnExists($tableName, StoreModel::COLUMN_POSITION)) {
            $connection->addColumn(
                $tableName,
                StoreModel::COLUMN_POSITION,
                [
                    'type' => Table::TYPE_INTEGER,
                    self::NULLABLE => false,
                    self::DEFAULT => 0,
                    'comment' => 'Store Position'
                ]
            );
        }
    }
}
