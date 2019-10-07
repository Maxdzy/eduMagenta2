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
    const BADGE_ID = 'badge_id';
    const NAME = 'name';
    const IMAGE_URL = 'image_url';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATE_TIME = 'update_time';

    /**
     * Get BadgeId.
     *
     * @param $badgeId
     * @return int
     */
    public function getBadgeId($badgeId);

    /**
     * Set BadgeId.
     * @param $badgeId
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
     * @param $name
     */
    public function setName($name);

    /**
     * Get getImage_url.
     *
     * @return varchar
     */
    public function getImageUrl();

    /**
     * Set ImageUrl.
     * @param $imageUrl
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
     * @param $status
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
     * @param $createdAt
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
     * @param $updateTime
     */
    public function setUpdateTime($updateTime);
}
