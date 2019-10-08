<?php
/**
 * @category Edu
 * @package Edu\CmsSimpleBadge
 * @author Maxim Dzyuba <maxim.d@tdo.kz>
 **/

declare(strict_types=1);

namespace Edu\CmsSimpleBadge\Block\Frontend\Badges;

use Edu\CmsSimpleBadge\Model\BadgesRepositoryFactory as BadgesRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\Manager;

/**
 * Class Render
 * @package Edu\CmsSimpleBadge\Block\Frontend\Badges
 */
class Render
{
    /**
     * Badges repository
     *
     * @var BadgesRepository
     */
    protected $badgesRepository;

    /**
     * @var Manager
     */
    protected $messageManager;

    /**
     * Block constructor.
     * @param BadgesRepository $badgesRepository
     * @param Manager $messageManager
     */
    public function __construct(
        BadgesRepository $badgesRepository,
        Manager $messageManager
    ) {
        $this->badgesRepository = $badgesRepository;
        $this->messageManager = $messageManager;
    }

    /**
     * @param null $badgeIdList
     * @return string
     * @throws NoSuchEntityException
     */
    public function renderBadges($badgeIdList = null): ?string
    {
        $result = null;
        if ($badgeIdList) {
            $badgesId = explode(',', $badgeIdList);
            $result.='<div class="relative">';
            foreach ($badgesId as $id) {
                $badge = $this->badgesRepository->create()->getBadgeId($id);
                try {
                    if ($badge->getStatus()) {
                        $result .= "<img src='{$badge->getImageUrl()}' 
                                    data_badgeId='{$id}'
                                    alt='{$badge->getName()}'
                                    class='product_badge' />";
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }
            $result.='</div>';
        }

        return $result;
    }
}
