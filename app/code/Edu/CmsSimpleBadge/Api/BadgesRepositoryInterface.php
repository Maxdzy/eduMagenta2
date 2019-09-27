<?php
/**
 * Edu Admin Cagegory Map Record Save Controller.
 * @category  Edu
 * @package   Edu_CmsSimpleBadge
 * @author    Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Api;

use Edu\CmsSimpleBadge\Api\Data\BadgesInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @api
 */
interface BadgesRepositoryInterface
{
    /**
     * Save page.
     *
     * @param BadgesInterface $badges
     * @return BadgesInterface
     * @throws LocalizedException
     */
    public function save(BadgesInterface $badges);

    /**
     * Retrieve Image.
     *
     * @param int $badgeId
     * @return BadgesInterface
     * @throws LocalizedException
     */
    public function getBadgeId($badgeId);

    /**
     * Delete image.
     *
     * @param BadgesInterface $badges
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(BadgesInterface $badges);

    /**
     * Delete badge by ID.
     *
     * @param int $badgeId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($badgeId);
}
