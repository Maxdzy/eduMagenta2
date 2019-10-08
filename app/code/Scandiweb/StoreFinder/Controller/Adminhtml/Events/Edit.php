<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Controller\Adminhtml\Events;

use Magento\Framework\Controller\ResultFactory;
use Scandiweb\StoreFinder\Controller\Adminhtml\AbstractEvent;
use Magento\Backend\Model\View\Result\Page;

class Edit extends AbstractEvent
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $event = $this->initEvent();

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Scandiweb_StoreFinder::event_list');
        $resultPage->getConfig()->getTitle()->set(__('Event'));
        $title = $event->getId() ? $event->getEventName() : __('New Event');
        $resultPage->getConfig()->getTitle()->prepend($title);

        $data = $this->session->getData(self::SESSION_KEY_CURRENT_EVENT);
        if (!empty($data)) {
            $event->setData($data);
        }

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Scandiweb_StoreFinder::view_event');
    }
}
