<?php
/**
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class Badges extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'edu_cmssimplebadge_badges';

    protected $_cacheTag = 'edu_cmssimplebadge_badges';

    protected $_eventPrefix = 'edu_cmssimplebadge_badges';

    protected function _construct()
    {
        $this->_init('Edu\CmsSimpleBadge\Model\ResourceModel\Badges');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
