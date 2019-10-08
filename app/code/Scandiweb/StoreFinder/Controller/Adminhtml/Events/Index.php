<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Controller\Adminhtml\Events;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;
use Scandiweb\StoreFinder\Helper\Data as DataHelper;

class Index extends Action
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * Index constructor.
     * @param Context $context
     * @param DataHelper $dataHelper
     */
    public function __construct(Context $context, DataHelper $dataHelper)
    {
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
    }

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
        if (!$this->dataHelper->isEventsEnabled()) {
            return false;
        }

        return $this->_authorization->isAllowed('Scandiweb_StoreFinder::events');
    }

    /**
     * @param Page $resultPage
     * @return Page
     */
    protected function _setPageData($resultPage)
    {
        $resultPage->setActiveMenu('Scandiweb_StoreFinder::event_list');
        $resultPage->getConfig()->getTitle()->prepend(__('Events'));
        $resultPage->addBreadcrumb(__('Store Finder'), __('Store Finder'))
            ->addBreadcrumb(__('Event List'), __('Event List'));

        return $resultPage;
    }
}
