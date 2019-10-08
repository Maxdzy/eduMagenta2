<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @author      Emils Brass <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Scandiweb\StoreFinder\Model\Template\Filter;
use Scandiweb\StoreFinder\Model\ResourceModel\Event as EventResource;
use Scandiweb\StoreFinder\Model\ResourceModel\Store\Collection as StoreCollection;
use Scandiweb\StoreFinder\Model\ResourceModel\Store\CollectionFactory as StoreCollectionFactory;
use Scandiweb\StoreFinder\Model\Store as StoreModel;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * @method int getStoreId()
 *
 * Db Table Functions
 * @method string getEventId()
 * @method string getEventName()
 * @method string getEventLocation()
 * @method string getCustomTime()
 * @method string getAdditionalInfo()
 * @method array getRsvpOptions()
 * @method Event setEventName(string $value)
 * @method Event setEventLocation(string $value)
 * @method Event setDatetimeStart(string $value)
 * @method Event setDatetimeEnd(string $value)
 * @method Event setCustomTime(string $value)
 * @method Event setAdditionalInfo(string $value)
 * @method Event setRsvpOptions(array $values)
 *
 * Custom methods
 * @method array getStoreIds()
 * @method Event setStoreIds(array $values)
 */
class Event extends AbstractModel
{
    const COLUMN_EVENT_ID = 'event_id';
    const COLUMN_NAME = 'event_name';
    const COLUMN_LOCATION = 'event_location';
    const COLUMN_DATETIME_START = 'datetime_start';
    const COLUMN_DATETIME_END = 'datetime_end';
    const COLUMN_CUSTOM_TIME = 'custom_time';
    const COLUMN_ADDITIONAL_INFO = 'additional_info';
    const COLUMN_RSVP_OPTIONS = 'rsvp_options';

    const KEY_RSVP_ENABLED = 'rsvp_enabled';
    const KEY_STORES = 'stores';

    const DATETIME_FORMAT = 'Y-m-d H:i';
    const DEFAULT_DATE_FORMAT = 'Y-m-d';
    const DEFAULT_TIME_FORMAT = 'H:i';
    const DEFAULT_TIME_DIVIDER = ' - ';

    /**
     * @var StoreCollectionFactory
     */
    protected $storeCollectionFactory;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var string
     */
    protected $additionalInfoHtml;

    /**
     * @var TimezoneInterface
     */
    protected $timezoneInterface;

    /**
     * Event constructor.
     * @param Context $context
     * @param Registry $registry
     * @param StoreCollectionFactory $storeCollectionFactory
     * @param Filter $filter
     * @param TimezoneInterface $timezoneInterface
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StoreCollectionFactory $storeCollectionFactory,
        Filter $filter,
        TimezoneInterface $timezoneInterface,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->storeCollectionFactory = $storeCollectionFactory;
        $this->filter = $filter;
        $this->timezoneInterface = $timezoneInterface;
    }

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init(EventResource::class);
    }

    /**
     * @param string $format
     * @return string
     */
    public function getDatetimeStart($format = self::DATETIME_FORMAT)
    {
        return $this->timezoneInterface->date(strtotime($this->getData(self::COLUMN_DATETIME_START)))
            ->format($format);
    }

    /**
     * @param string $format
     * @return string
     */
    public function getDatetimeEnd($format = self::DATETIME_FORMAT)
    {
        return $this->timezoneInterface->date(strtotime($this->getData(self::COLUMN_DATETIME_END)))
            ->format($format);
    }

    /**
     * @param string $format
     * @return false|string
     */
    public function getDateStart($format = self::DEFAULT_DATE_FORMAT)
    {
        return $this->getDatetimeStart($format);
    }

    /**
     * @param string $format
     * @return false|string
     */
    public function getDateEnd($format = self::DEFAULT_DATE_FORMAT)
    {
        return $this->getDatetimeEnd($format);
    }

    /**
     * @param string $format
     * @return false|string
     */
    public function getTimeStart($format = self::DEFAULT_TIME_FORMAT)
    {
        return $this->getDatetimeStart($format);
    }

    /**
     * @param string $format
     * @return false|string
     */
    public function getTimeEnd($format = self::DEFAULT_TIME_FORMAT)
    {
        return $this->getDatetimeEnd($format);
    }

    /**
     * @param string $format
     * @param string $divider
     * @param bool $useCustomTimeIfSet
     *
     * Returns either custom_time string if set, or start and end time in format
     * {$format(start_time)}{$divider}{$format(end_time)}
     * E.g. to get "10:00AM - 5:00PM"
     *     $format = "g:iA"
     *     $divider = " - "
     * More info on formatting http://php.net/manual/en/function.date.php
     * @return string
     */
    public function getTimeString(
        $format = self::DEFAULT_TIME_FORMAT,
        $divider = self::DEFAULT_TIME_DIVIDER,
        $useCustomTimeIfSet = true
    ) {
        $customTime = $this->getCustomTime();
        if ($useCustomTimeIfSet && $customTime !== null) {
            return $customTime;
        }

        return sprintf(
            '%s%s%s',
            $this->getTimeStart($format),
            $divider,
            $this->getTimeEnd($format)
        );
    }

    /**
     * @param bool $refresh
     * @return StoreModel[]
     */
    public function getStores($refresh = false)
    {
        if ($refresh || !$this->getData(self::KEY_STORES)) {
            if (empty($this->getStoreIds())) {
                $this->setData(self::KEY_STORES, []);
            } else {
                /** @var StoreCollection $storeCollection */
                $storeCollection = $this->storeCollectionFactory->create();
                $storeCollection->addFieldToFilter(StoreModel::COLUMN_STORE_ID, ['IN' => $this->getStoreIds()]);

                $this->setData(self::KEY_STORES, $storeCollection->getItems());
            }
        }

        return $this->getData(self::KEY_STORES);
    }

    /**
     * @return string
     */
    public function getAdditionalInfoHtml()
    {
        if ($this->additionalInfoHtml === null) {
            $this->additionalInfoHtml = $this->filter->filter($this->getAdditionalInfo());
        }

        return $this->additionalInfoHtml;
    }

    /**
     * @return bool
     */
    public function isRsvpEnabled()
    {
        return (isset($this->getRsvpOptions()[self::KEY_RSVP_ENABLED])
            && (bool)$this->getRsvpOptions()[self::KEY_RSVP_ENABLED]);
    }
}
