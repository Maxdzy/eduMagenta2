<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Scandiweb\StoreFinder\Helper\Data as DataHelper;
use Scandiweb\StoreFinder\Model\Rsvp as RsvpModel;
use Scandiweb\StoreFinder\Model\ResourceModel\Rsvp\Collection as RsvpCollection;
use Scandiweb\StoreFinder\Model\Event as EventModel;
use Scandiweb\StoreFinder\Model\Store as StoreModel;

class Rsvp extends AbstractDb
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(DataHelper::TABLE_EVENT_RSVP, RsvpModel::COLUMN_RSVP_ID);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param AbstractModel $object
     * @return Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        $eventTableName = $this->getConnection()->getTableName(DataHelper::TABLE_EVENTS);
        $storeTableName = $this->getConnection()->getTableName(DataHelper::TABLE_STORES);
        $select->joinLeft(
            [RsvpCollection::COLUMN_ALIAS_EVENT => $eventTableName],
            sprintf(
                'main_table.%s = %s.%s',
                RsvpModel::COLUMN_EVENT_ID,
                RsvpCollection::COLUMN_ALIAS_EVENT,
                EventModel::COLUMN_EVENT_ID
            ),
            [EventModel::COLUMN_NAME]
        )->joinLeft(
            [RsvpCollection::COLUMN_ALIAS_STORE => $storeTableName],
            sprintf(
                'main_table.%s = %s.%s',
                RsvpModel::COLUMN_STORE_ID,
                RsvpCollection::COLUMN_ALIAS_STORE,
                StoreModel::COLUMN_STORE_ID
            ),
            [StoreModel::COLUMN_STORE_NAME, StoreModel::COLUMN_ADDRESS]
        );

        return $select;
    }
}
