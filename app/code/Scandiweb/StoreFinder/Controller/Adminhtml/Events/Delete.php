<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Controller\Adminhtml\Events;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Scandiweb\StoreFinder\Controller\Adminhtml\AbstractEvent;
use Scandiweb\StoreFinder\Model\Event as EventModel;

class Delete extends AbstractEvent
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $eventId = $this->getRequest()->getParam(EventModel::COLUMN_EVENT_ID);
        if ($eventId) {
            try {
                $this->eventRepository->delete($this->eventRepository->getById($eventId));
                $this->messageManager->addSuccessMessage(__('You deleted the event.'));
                $redirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $redirect->setPath('*/*/edit', [EventModel::COLUMN_EVENT_ID => $eventId]);
            }
        }

        return $redirect;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Scandiweb_StoreFinder::delete_event');
    }
}
