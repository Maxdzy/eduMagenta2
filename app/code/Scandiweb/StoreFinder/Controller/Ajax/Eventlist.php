<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @author      Emils Brass <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Controller\Ajax;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Psr\Log\LoggerInterface;
use Scandiweb\StoreFinder\Model\ResourceModel\Event\BoundCollection as EventCollection;
use Scandiweb\StoreFinder\Model\ResourceModel\Event\BoundCollectionFactory as EventCollectionFactory;
use Scandiweb\StoreFinder\Helper\Data as DataHelper;
use Scandiweb\StoreFinder\Model\ResourceModel\Store\FileInfo;
use Scandiweb\StoreFinder\Model\Event as EventModel;
use Scandiweb\StoreFinder\Model\Store as StoreModel;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Eventlist extends Action
{
    /**
     * @var EventCollectionFactory
     */
    protected $eventCollectionFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var FileInfo
     */
    protected $fileInfo;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var EventCollection
     */
    protected $eventCollection;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var TimezoneInterface
     */
    protected $timezoneInterface;

    /**
     * Eventlist constructor.
     * @param Context $context
     * @param EventCollectionFactory $eventCollectionFactory
     * @param LoggerInterface $logger
     * @param FileInfo $fileInfo
     * @param DataHelper $dataHelper
     * @param TimezoneInterface $timezoneInterface
     */
    public function __construct(
        Context $context,
        EventCollectionFactory $eventCollectionFactory,
        LoggerInterface $logger,
        FileInfo $fileInfo,
        DataHelper $dataHelper,
        TimezoneInterface $timezoneInterface
    ) {
        parent::__construct($context);
        $this->eventCollectionFactory = $eventCollectionFactory;
        $this->logger = $logger;
        $this->fileInfo = $fileInfo;
        $this->dataHelper = $dataHelper;
        $this->timezoneInterface = $timezoneInterface;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var Json $json */
        $json = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $totalSize = $this->getEventCollection()->getSize();
            $limit = $this->getParams()[DataHelper::URI_PARAM_LIMIT];
            $page = $this->getParams()[DataHelper::URI_PARAM_PAGE] - 1;

            $eventData = [];

            // Because "it's a feature, not a bug"
            // https://github.com/magento/magento2/issues/9255
            if (($limit * $page) < $totalSize) {
                $events = $this->getEventCollection()->getItems();
                /** @var EventModel $event */
                foreach ($events as $event) {
                    $eventData[] = $this->getEventData($event);
                }
            }

            $data = [];
            $data[DataHelper::JSON_KEY_STATUS] = 'success';
            $data[DataHelper::JSON_KEY_EVENTS] = $eventData;
            $data[DataHelper::JSON_KEY_PARAMS] = $this->getParams();
            $data[DataHelper::JSON_KEY_TOTAL_SIZE] = $totalSize;
            $json->setData($data);
        } catch (Exception $exception) {
            $json->setData([
                DataHelper::JSON_KEY_STATUS => 'error',
                DataHelper::JSON_KEY_MESSAGE => __('Something went wrong')
            ]);
            $this->logger->error($exception->getMessage());
        }

        return $json;
    }

    /**
     * @param EventModel $event
     * @return array
     */
    protected function getEventData(EventModel $event)
    {
        $data = $event->getData();

        $startTime = strtotime($event->getData(EventModel::COLUMN_DATETIME_START));
        $data['date_start_month'] = $this->timezoneInterface->date($startTime)->format('M');
        $data['date_start_day'] = $this->timezoneInterface->date($startTime)->format('d');
        $data['date_start_time'] = $this->timezoneInterface->date($startTime)->format('g:iA');

        $endTime = strtotime($event->getData(EventModel::COLUMN_DATETIME_END));
        $data['date_end_month'] = $this->timezoneInterface->date($endTime)->format('M');
        $data['date_end_day'] = $this->timezoneInterface->date($endTime)->format('d');
        $data['date_end_time'] = $this->timezoneInterface->date($endTime)->format('g:iA');

        $timezoneInterface = $this->timezoneInterface;
        $data['stores'] = [];
        /** @var StoreModel $store */
        foreach ($event->getStores() as $store) {
            $storeData = [];
            $storeData['store_id'] = $store->getStoreId();
            $storeData['name'] = $store->getStoreName();
            $storeData['address'] = nl2br($store->getAddress());
            $storeData['directions_url'] = $store->getCustomDirectionsUrl();
            $data['stores'][] = $storeData;
        }

        $data[EventModel::COLUMN_ADDITIONAL_INFO] = $event->getAdditionalInfoHtml();

        $data[EventModel::KEY_RSVP_ENABLED] = $event->isRsvpEnabled();

        return $data;
    }

    /**
     * @return array
     */
    protected function getParams()
    {
        if (!isset($this->params)) {
            $this->params = $this->getRequest()->getParams();

            $this->params[DataHelper::URI_PARAM_LIMIT] = (int)$this->getRequest()
                ->getParam(DataHelper::URI_PARAM_LIMIT, 20);
            $this->params[DataHelper::URI_PARAM_PAGE] = (int)$this->getRequest()
                ->getParam(DataHelper::URI_PARAM_PAGE, 1);

            $this->params[DataHelper::URI_PARAM_STORE_ID] = (int)$this->getRequest()
                ->getParam(DataHelper::URI_PARAM_STORE_ID, -1);

            $storeCountries = $this->getRequest()->getParam(DataHelper::URI_PARAM_STORE_COUNTRY, null);
            if ($storeCountries !== null) {
                $storeCountries = explode(DataHelper::URI_PARAM_DELIMITER, $storeCountries);
            }
            $this->params[DataHelper::URI_PARAM_STORE_COUNTRY] = $this->validateStoreCountries($storeCountries);
        }

        return $this->params;
    }

    /**
     * @return EventCollection
     */
    protected function getEventCollection()
    {
        if ($this->eventCollection === null) {
            $limit = $this->getParams()[DataHelper::URI_PARAM_LIMIT];
            $page = $this->getParams()[DataHelper::URI_PARAM_PAGE];
            $storeCountries = $this->getParams()[DataHelper::URI_PARAM_STORE_COUNTRY];
            $storeId = $this->getParams()[DataHelper::URI_PARAM_STORE_ID];

            /** @var EventCollection $collection */
            $collection = $this->eventCollectionFactory->create();
            $collection->setPageSize($limit);
            $collection->setCurPage($page);

            if ($storeCountries !== null) {
                $collection->addFieldToFilter(StoreModel::COLUMN_COUNTRY, ['in' => $storeCountries]);
            }

            if ($storeId !== -1) {
                $collection->addFieldToFilter(StoreModel::COLUMN_STORE_ID, $storeId);
            }

            $collection->addFieldToFilter(
                EventModel::COLUMN_DATETIME_END,
                ['gt' => $this->dataHelper->getCurrentTime('Y-m-d')]
            );

            $collection->setOrder(EventModel::COLUMN_DATETIME_START, AbstractDb::SORT_ORDER_ASC);

            $this->eventCollection = $collection;
        }

        return $this->eventCollection;
    }

    /**
     * @param array|null $data
     * @param array $allowed
     * @return array|null
     */
    protected function validateArray($data, $allowed)
    {
        if (!is_array($data)) {
            return null;
        }

        foreach ($data as $key => $item) {
            if (!in_array($item, $allowed)) {
                unset($data[$key]);
            }
        }

        if (empty($data)) {
            $data = null;
        }

        return $data;
    }

    /**
     * @param array|null $data
     * @return array|null
     */
    protected function validateStoreCountries($data)
    {
        return $this->validateArray($data, array_keys($this->dataHelper->getAllowedCountries()));
    }
}
