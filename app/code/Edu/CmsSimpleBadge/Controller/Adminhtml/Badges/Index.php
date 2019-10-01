<?php
/**
 *
 *  Edu Badges Controller
 *
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Controller\Adminhtml\Badges;

use Edu\CmsSimpleBadge\Controller\Adminhtml\Badges;
use Magento\Backend\Model\View\Result\Page;

class Index extends Badges
{
    /**
     * @return Page
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Edu_CmsSimpleBadge::badges');
        $resultPage->getConfig()->getTitle()->prepend(__('Badges List'));
        $resultPage->addBreadcrumb(__('badges'), __('badges'));
        return $resultPage;
    }

}
