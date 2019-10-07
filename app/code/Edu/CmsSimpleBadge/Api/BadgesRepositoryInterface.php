<?php
/**
 * Edu Admin Badges Map Record Save Controller.
 * @category  Edu
 * @package   Edu\CmsSimpleBadge
 * @author    Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Api;

use Edu\CmsSimpleBadge\Api\Data\BadgesInterface;

/**
 * Interface BadgesRepositoryInterface
 * @package Edu\CmsSimpleBadge\Api
 */
interface BadgesRepositoryInterface
{

    /**
     * @param BadgesInterface $badges
     * @return mixed
     */
    public function save(BadgesInterface $badges);


    /**
     * @param $badgeId
     * @return mixed
     */
    public function getBadgeId($badgeId);


    /**
     * @param BadgesInterface $badges
     * @return mixed
     */
    public function delete(BadgesInterface $badges);


    /**
     * @param $badgeId
     * @return mixed
     */
    public function deleteById($badgeId);
}
