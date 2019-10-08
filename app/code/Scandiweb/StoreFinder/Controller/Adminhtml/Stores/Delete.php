<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Controller\Adminhtml\Stores;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Scandiweb\StoreFinder\Controller\Adminhtml\AbstractStore;
use Scandiweb\StoreFinder\Model\Store as StoreModel;

class Delete extends AbstractStore
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $storeId = $this->getRequest()->getParam(StoreModel::COLUMN_STORE_ID);
        if ($storeId) {
            try {
                $this->storeRepository->delete($this->storeRepository->getById($storeId));
                $this->messageManager->addSuccessMessage(__('You deleted the store.'));
                $redirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $redirect->setPath('*/*/edit', [StoreModel::COLUMN_STORE_ID => $storeId]);
            }
        }

        return $redirect;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Scandiweb_StoreFinder::delete_store');
    }
}
