<?php
/**
 * Edu_CmsSimpleBadge delete badge.
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Controller\Adminhtml\Badges;

use Edu\CmsSimpleBadge\Controller\Adminhtml\Badges;
use Magento\Framework\Controller\Result\Redirect;

/**
 * Class Delete
 * @package Edu\CmsSimpleBadge\Controller\Adminhtml\Badges
 */
class Delete extends Badges
{
    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $badgeId = $this->getRequest()->getParam('badge_id');
        if ($badgeId) {
            try {
                $this->badgesRepositoryInterface->deleteById($badgeId);
                $this->messageManager->addSuccessMessage(__('The badges has been deleted.'));
                $resultRedirect->setPath('badges/badges/index');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem deleting the badges'));
                return $resultRedirect->setPath('badges/badges/edit', ['badge_id' => $badgeId]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find the badges to delete.'));
        $resultRedirect->setPath('badges/badges/index');

        return $resultRedirect;
    }
}
