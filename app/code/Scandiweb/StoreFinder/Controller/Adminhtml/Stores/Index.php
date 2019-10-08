<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Controller\Adminhtml\Stores;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $this->_setPageData($resultPage);

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Scandiweb_StoreFinder::store_finder');
    }

    /**
     * @param Page $resultPage
     * @return Page
     */
    protected function _setPageData($resultPage)
    {
        $resultPage->setActiveMenu('Scandiweb_StoreFinder::store_list');
        $resultPage->getConfig()->getTitle()->prepend(__('Stores'));
        $resultPage->addBreadcrumb(__('Store Finder'), __('Store Finder'))
            ->addBreadcrumb(__('Store List'), __('Store List'));

        return $resultPage;
    }
}
