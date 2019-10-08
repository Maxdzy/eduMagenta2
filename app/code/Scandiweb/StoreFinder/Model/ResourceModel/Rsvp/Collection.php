<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Model\ResourceModel\Rsvp;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Scandiweb\StoreFinder\Model\ResourceModel\Rsvp as RsvpResource;
use Scandiweb\StoreFinder\Model\Rsvp as RsvpModel;
use Scandiweb\StoreFinder\Model\Event as EventModel;
use Scandiweb\StoreFinder\Model\Store as StoreModel;
use Scandiweb\StoreFinder\Helper\Data as DataHelper;

class Collection extends AbstractCollection
{
    const COLUMN_ALIAS_EVENT = 'event';
    const COLUMN_ALIAS_STORE = 'store';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(RsvpModel::class, RsvpResource::class);
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $eventTableName = $this->getConnection()->getTableName(DataHelper::TABLE_EVENTS);
        $storeTableName = $this->getConnection()->getTableName(DataHelper::TABLE_STORES);
        $this->getSelect()->joinLeft(
            [self::COLUMN_ALIAS_EVENT => $eventTableName],
            sprintf(
                'main_table.%s = %s.%s',
                RsvpModel::COLUMN_EVENT_ID,
                self::COLUMN_ALIAS_EVENT,
                EventModel::COLUMN_EVENT_ID
            ),
            [EventModel::COLUMN_NAME]
        )->joinLeft(
            [self::COLUMN_ALIAS_STORE => $storeTableName],
            sprintf(
                'main_table.%s = %s.%s',
                RsvpModel::COLUMN_STORE_ID,
                self::COLUMN_ALIAS_STORE,
                StoreModel::COLUMN_STORE_ID
            ),
            [StoreModel::COLUMN_STORE_NAME, StoreModel::COLUMN_ADDRESS]
        );

        return $this;
    }
}
