<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Scandiweb\StoreFinder\Api\EventRepositoryInterface;
use Scandiweb\StoreFinder\Model\Event as EventModel;
use Scandiweb\StoreFinder\Helper\Data as DataHelper;

abstract class AbstractEvent extends Action
{
    const REGISTRY_KEY_CURRENT_EVENT = 'current_event';
    const SESSION_KEY_CURRENT_EVENT = 'current_event';
    const PERSISTOR_KEY_CURRENT_EVENT = 'current_event';

    /**
     * @var EventRepositoryInterface
     */
    protected $eventRepository;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * AbstractStore constructor.
     * @param Context $context
     * @param EventRepositoryInterface $eventRepository
     * @param Registry $registry
     * @param Session $session
     * @param DataPersistorInterface $dataPersistor
     * @param DataHelper $dataHelper
     */
    public function __construct(
        Context $context,
        EventRepositoryInterface $eventRepository,
        Registry $registry,
        Session $session,
        DataPersistorInterface $dataPersistor,
        DataHelper $dataHelper
    ) {
        parent::__construct($context);

        $this->eventRepository = $eventRepository;
        $this->registry = $registry;
        $this->session = $session;
        $this->dataPersistor = $dataPersistor;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return EventModel
     */
    protected function initEvent()
    {
        $eventId = (int)$this->getRequest()->getParam(EventModel::COLUMN_EVENT_ID);

        try {
            $event = $this->eventRepository->getById($eventId);
        } catch (NotFoundException $exception) {
            $event = $this->eventRepository->create();
        }

        $this->registry->register(self::REGISTRY_KEY_CURRENT_EVENT, $event);

        return $event;
    }

    /**
     * @param array $data
     * @throws LocalizedException
     */
    protected function validateData($data)
    {
        if (!is_array($data)) {
            throw new LocalizedException(__('Invalid data'));
        }

        if (!array_key_exists(EventModel::COLUMN_NAME, $data)) {
            throw new LocalizedException(__('Missing required argument Event Name'));
        }
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        if (!$this->dataHelper->isEventsEnabled()) {
            return false;
        }

        return parent::_isAllowed();
    }
}
