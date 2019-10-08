<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Controller\Adminhtml\Events;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Scandiweb\StoreFinder\Controller\Adminhtml\AbstractEvent;
use Scandiweb\StoreFinder\Model\Event as EventModel;

class Save extends AbstractEvent
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var Http $request */
        $request = $this->getRequest();
        $data = $request->getPostValue();
        if ($data) {
            $eventId = $request->getParam(EventModel::COLUMN_EVENT_ID);

            if (empty($data[EventModel::COLUMN_EVENT_ID])) {
                $data[EventModel::COLUMN_EVENT_ID] = null;
            }

            try {
                $model = $this->eventRepository->getById($eventId);
            } catch (NotFoundException $exception) {
                $model = $this->eventRepository->create();
            }

            if (!$model->getId() && $eventId) {
                $this->messageManager->addErrorMessage(__('This event no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            try {
                $this->validateData($data);

                $model->setData($data);
                $this->eventRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the event.'));
                $this->dataPersistor->clear(self::PERSISTOR_KEY_CURRENT_EVENT);

                if ($request->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', [EventModel::COLUMN_EVENT_ID => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the store.'));
            }

            $this->dataPersistor->set(self::PERSISTOR_KEY_CURRENT_EVENT, $data);

            return $resultRedirect->setPath(
                '*/*/edit',
                [EventModel::COLUMN_EVENT_ID => $this->getRequest()->getParam(EventModel::COLUMN_EVENT_ID)]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
