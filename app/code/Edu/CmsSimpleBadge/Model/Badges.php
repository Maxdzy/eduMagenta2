<?php
/**
 * Edu_CmsSimpleBadge Model.
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Model;

use Edu\CmsSimpleBadge\Api\Data\BadgesInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
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
     * @var UploaderPool
     */
    protected $uploaderPool;

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'edu_cmssimplebadge_badges';

    /**
     * Sliders constructor.
     * @param Context $context
     * @param Registry $registry
     * @param UploaderPool $uploaderPool
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        UploaderPool $uploaderPool,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->uploaderPool    = $uploaderPool;
    }

    /**
     * Initialise resource model
     * @codingStandardsIgnoreStart
     */
    protected function _construct()
    {
        // @codingStandardsIgnoreEnd
        $this->_init('Edu\CmsSimpleBadge\Model\ResourceModel\Badges');
    }

    /**
     * Get cache identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->getData(BadgesInterface::BADGES);
    }

    /**
     * Set image
     *
     * @param $image
     * @return $this
     */
    public function setImage($badges)
    {
        return $this->setData(BadgesInterface::BADGES, $badges);
    }

    /**
     * Get image URL
     *
     * @return bool|string
     * @throws LocalizedException
     * @throws \Exception
     */
    public function getImageUrl()
    {
        $url = false;
        $badges = $this->getImage();
        if ($badges) {
            if (is_string($badges)) {
                $uploader = $this->uploaderPool->getUploader('badges');
                $url = $uploader->getBaseUrl() . $uploader->getBasePath() . $badges;
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }

   /**
     * Get BadgeId.
     *
     * @return int
     */
    public function getBadgeId($badgeId=null)
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
    public function getImageUrl2()
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
