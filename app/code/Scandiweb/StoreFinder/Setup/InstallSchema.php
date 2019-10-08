<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Scandiweb\StoreFinder\Helper\Data as DataHelper;
use Scandiweb\StoreFinder\Model\Store as StoreModel;

class InstallSchema implements InstallSchemaInterface
{
    const NULLABLE = 'nullable';
    const UNSIGNED = 'unsigned';
    const PRIMARY = 'primary';
    const DEFAULT = 'default';
    const IDENTITY = 'identity';

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        $storeTableName = $installer->getTable(DataHelper::TABLE_STORES);
        if (!$connection->isTableExists($storeTableName)) {
            $storeTable = $connection->newTable($storeTableName)->addColumn(
                StoreModel::COLUMN_STORE_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    self::IDENTITY => true,
                    self::UNSIGNED => true,
                    self::NULLABLE => false,
                    self::PRIMARY => true
                ],
                'Store ID'
            )->addColumn(
                StoreModel::COLUMN_STORE_NAME,
                Table::TYPE_TEXT,
                255,
                [
                    self::NULLABLE => false
                ],
                'Store Name'
            )->addColumn(
                StoreModel::COLUMN_IMAGE,
                Table::TYPE_TEXT,
                null,
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'Image'
            )->addColumn(
                StoreModel::COLUMN_ADDRESS,
                Table::TYPE_TEXT,
                null,
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'Address'
            )->addColumn(
                StoreModel::COLUMN_HAS_STORE_PAGE,
                Table::TYPE_BOOLEAN,
                null,
                [
                    self::NULLABLE => false,
                    self::DEFAULT => 0
                ],
                'Has Store Page'
            )->addColumn(
                StoreModel::COLUMN_CUSTOM_DIRECTIONS_URL,
                Table::TYPE_TEXT,
                null,
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'Custom Directions Url'
            )->addColumn(
                StoreModel::COLUMN_LATITUDE,
                Table::TYPE_DECIMAL,
                [13,10],
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'Latitude'
            )->addColumn(
                StoreModel::COLUMN_LONGITUDE,
                Table::TYPE_DECIMAL,
                [13,10],
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'Longitude'
            )->addColumn(
                StoreModel::COLUMN_PHONE_NUMBER,
                Table::TYPE_TEXT,
                255,
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'Phone Number'
            )->addColumn(
                StoreModel::COLUMN_DESCRIPTION,
                Table::TYPE_TEXT,
                null,
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'Description'
            )->addColumn(
                StoreModel::COLUMN_MANAGER_NAME,
                Table::TYPE_TEXT,
                255,
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'Manager Name'
            )->addColumn(
                StoreModel::COLUMN_MANAGER_PHONE,
                Table::TYPE_TEXT,
                255,
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'Manager Phone'
            )->addColumn(
                StoreModel::COLUMN_MANAGER_EMAIL,
                Table::TYPE_TEXT,
                255,
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'Manager Email'
            )->addColumn(
                StoreModel::COLUMN_STORE_EMAIL,
                Table::TYPE_TEXT,
                255,
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'Store Email'
            )->addColumn(
                StoreModel::COLUMN_STORE_HOURS,
                Table::TYPE_TEXT,
                '64k',
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'Store Hours'
            )->addColumn(
                StoreModel::COLUMN_ADDITIONAL_INFO,
                Table::TYPE_TEXT,
                '64k',
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'Additional Information'
            )->addColumn(
                StoreModel::COLUMN_STORE_TYPE,
                Table::TYPE_TEXT,
                255,
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'Store Type'
            )->addColumn(
                StoreModel::COLUMN_COUNTRY,
                Table::TYPE_TEXT,
                255,
                [
                    self::NULLABLE => true,
                    self::DEFAULT => null
                ],
                'Country'
            )->addIndex(
                $connection->getIndexName($storeTableName, [StoreModel::COLUMN_HAS_STORE_PAGE]),
                [StoreModel::COLUMN_HAS_STORE_PAGE]
            )->addIndex(
                $connection->getIndexName($storeTableName, [StoreModel::COLUMN_STORE_TYPE]),
                [StoreModel::COLUMN_STORE_TYPE]
            )->addIndex(
                $connection->getIndexName($storeTableName, [StoreModel::COLUMN_COUNTRY]),
                [StoreModel::COLUMN_COUNTRY]
            );

            $connection->createTable($storeTable);
        }

        $installer->endSetup();
    }
}
