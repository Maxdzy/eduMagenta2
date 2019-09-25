<?php
/**
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Model\ResourceModel\Badges;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'badge_id';
    protected $_eventPrefix = 'edu_cmssimplebadgeid_badge_collection';
    protected $_eventObject = 'badges_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Edu\CmsSimpleBadge\Model\Badges', 'Edu\CmsSimpleBadge\Model\ResourceModel\Badges');
    }

}
