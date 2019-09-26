<?php
/**
 * Edu_CmsSimpleBadge add new badge.
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Controller\Adminhtml\Badges;

use Edu\CmsSimpleBadge\Model\BadgesFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;

class AddRow extends Action
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var BadgesFactory
     */
    private $badgesFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry,
     * @param badgesFactory $badgesFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        BadgesFactory $badgesFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->badgesFactory = $badgesFactory;
    }

    /**
     * Mapped Badges List page.
     * @return Page
     */
    public function execute()
    {
        $rowName="";
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->badgesFactory->create();

        /** @var Page $resultPage */
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            $rowName = $rowData->getName();

            if (!$rowData->getBadgeId()) {
                $this->messageManager->addError(__('row data no longer exist.'));
                $this->_redirect('badges/badges/index'); //grid/grid/rowdata
                return;
            }
        }

        $this->coreRegistry->register('row_data', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $name = $rowId ? __('Edit Row Data ') . $rowName : __('Add Row Data');
        $resultPage->getConfig()->getTitle()->prepend($name);
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Edu_Badges::add_row');
    }
}
