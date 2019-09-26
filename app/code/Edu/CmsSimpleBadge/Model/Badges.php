<?php
/**
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Model;

use Magento\Framework\Model\AbstractModel;
use Edu\CmsSimpleBadge\Api\Data\BadgesInterface;
use Zend\Db\Sql\Ddl\Column\Varchar;

class Badges extends AbstractModel implements BadgesInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'edu_cmssimplebadge_badges';

    /**
     * @var string
     */
    protected $_cacheTag = 'edu_cmssimplebadge_badges';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'edu_cmssimplebadge_badges';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Edu\CmsSimpleBadge\Model\ResourceModel\Badges');
    }

    /**
     * Get BadgeId.
     *
     * @return int
     */
    public function getBadgeId()
    {
        return $this->getData(self::BADGE_ID);
    }

    /**
     * Set BadgeId.
     */
    public function setBadgeId($badgeId)
    {
        return $this->setData(self::BADGE_ID, $badgeId);
    }

    /**
     * Get Name.
     *
     * @return varchar
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set Name.
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get getImage_url.
     *
     * @return varchar
     */
    public function getImageUrl()
    {
        return $this->getData(self::IMAGE_URL);
    }

    /**
     * Set Image_url.
     */
    public function setImageUrl($imageUrl)
    {
        return $this->setData(self::IMAGE_URL, $imageUrl);
    }

    /**
     * Get Status.
     *
     * @return varchar
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set Status.
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get CreatedAt.
     *
     * @return varchar
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set CreatedAt.
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get UpdateTime.
     *
     * @return varchar
     */
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * Set UpdateTime.
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

}
