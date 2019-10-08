<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Model\ResourceModel\Event;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Scandiweb\StoreFinder\Controller\Adminhtml\AbstractEvent;
use Scandiweb\StoreFinder\Model\Event as EventModel;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var string
     */
    protected $mediaUrl;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $eventCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $eventCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $eventCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (!isset($this->loadedData)) {
            $this->loadedData = [];
            /** @var EventModel $item */
            foreach ($this->collection->getItems() as $item) {
                $this->loadedData[$item->getId()] = array_merge(
                    $item->getData(),
                    $this->convertRsvpOptions($item->getRsvpOptions())
                );
            }
        }

        $data = $this->dataPersistor->get(AbstractEvent::PERSISTOR_KEY_CURRENT_EVENT);
        if (!empty($data)) {
            /** @var EventModel $event */
            $event = $this->collection->getNewEmptyItem();
            $event->setData($data);
            $this->loadedData[$event->getId()] = array_merge(
                $event->getData(),
                $this->convertRsvpOptions($event->getRsvpOptions())
            );
            $this->dataPersistor->clear(AbstractEvent::PERSISTOR_KEY_CURRENT_EVENT);
        }

        return $this->loadedData;
    }

    /**
     * @param array $options
     * @return array
     */
    protected function convertRsvpOptions($options)
    {
        $results = [];
        foreach ($options as $key => $option) {
            $results[sprintf('%s[%s]', EventModel::COLUMN_RSVP_OPTIONS, $key)] = $option;
        }

        return $results;
    }
}
