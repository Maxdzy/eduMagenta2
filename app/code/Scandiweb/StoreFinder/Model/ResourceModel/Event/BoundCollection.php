<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Model\ResourceModel\Event;

use Scandiweb\StoreFinder\Model\Event as EventModel;
use Scandiweb\StoreFinder\Helper\Data as DataHelper;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class BoundCollection extends Collection
{
    /**
     * Joins bind table so that only events with stores are returned
     * @return AbstractCollection
     */
    protected function _beforeLoad()
    {
        $from = $this->_select->getPart('from');
        if (!isset($from[self::COLUMN_ALIAS_BINDS])) {
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
        }

        return parent::_beforeLoad();
    }
}
