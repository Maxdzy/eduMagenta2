<?php
/**
 * Edu Admin Cagegory Map Record Save Controller.
 * @category  Edu
 * @package   Edu_CmsSimpleBadge
 * @author    Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Controller\Adminhtml\Badges;

use Edu\CmsSimpleBadge\Model\BadgesFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Save extends Action
{
    /**
     * @var BadgesFactory
     */
    var $badgesFactory;

    /**
     * @param Context $context
     * @param BadgesFactory $badgesFactory
     */
    public function __construct(
        Context $context,
        BadgesFactory $badgesFactory
    ) {
        parent::__construct($context);
        $this->badgesFactory = $badgesFactory;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('badges/badges/addrow');
            return;
        }
        try {
            $rowData = $this->badgesFactory->create();
            $rowData->setData($data);
            if (isset($data['id'])) {
                $rowData->setBadgeId($data['id']);
            }
            $rowData->save();
            $this->messageManager->addSuccess(__('Row data has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('badges/badges/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Edu_badges::save');
    }
}
