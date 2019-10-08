<?php
/**
 * Edu_CmsSimpleBadge add new badge or edit badge.
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Controller\Adminhtml\Badges;

use Edu\CmsSimpleBadge\Controller\Adminhtml\Badges;
use Magento\Framework\View\Result\Page;

/**
 * Class Edit
 * @package Edu\CmsSimpleBadge\Controller\Adminhtml\Badges
 */
class Edit extends Badges
{
    /**
     * @return Page
     */
    public function execute()
    {
        //TODO change badges not work
        $badgeId = $this->getRequest()->getParam('badge_id');
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Edu_CmsSimpleBadge::badges');
        $resultPage->setActiveMenu('Edu_CmsSimpleBadge::badges')
            ->addBreadcrumb(__('Badges'), __('Badges'))
            ->addBreadcrumb(__('Manage Badges'), __('Manage Badges'));

        if ($badgeId) {
            $resultPage->addBreadcrumb(__('Edit Badges'), __('Edit Badges'));
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Badges'));
        }

        return $resultPage;
    }
}
