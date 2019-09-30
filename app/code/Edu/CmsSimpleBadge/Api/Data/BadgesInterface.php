<?php
/**
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Api\Data;

use Zend\Db\Sql\Ddl\Column\Varchar;

interface BadgesInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const BADGES = 'badges';
    const BADGE_ID = 'badge_id';
    const NAME = 'name';
    const IMAGE_URL = 'image_url';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATE_TIME = 'update_time';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get BadgeId.
     *
     * @return int
     */
    public function getBadgeId($badgeId);

    /**
     * Set BadgeId.
     */
    public function setBadgeId($badgeId);

    /**
     * Get Name.
     *
     * @return varchar
     */
    public function getName();

    /**
     * Set Name.
     */
    public function setName($name);

    /**
     * Get getImage_url.
     *
     * @return varchar
     */
    public function getImageUrl();

    /**
     * Set Image_url.
     */
    public function setImageUrl($imageUrl);

    /**
     * Get Status.
     *
     * @return varchar
     */
    public function getStatus();

    /**
     * Set Status.
     */
    public function setStatus($status);

    /**
     * Get CreatedAt.
     *
     * @return varchar
     */
    public function getCreatedAt();

    /**
     * Set CreatedAt.
     */
    public function setCreatedAt($createdAt);

    /**
     * Get UpdateTime.
     *
     * @return varchar
     */
    public function getUpdateTime();

    /**
     * Set UpdateTime.
     */
    public function setUpdateTime($updateTime);
}
