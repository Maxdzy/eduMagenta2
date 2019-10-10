<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Edu\StoreFinderCompletion\Model\Event as EventModel;
use Edu\StoreFinderCompletion\Model\Template\Filter;
use Edu\StoreFinderCompletion\Model\ResourceModel\Store as StoreResource;

/**
 * @method int getStoreId()
 *
 * Store Table Functions
 * @method string getStoreName()
 * @method string getIdentifier()
 * @method array getImage()
 * @method string getAddress()
 * @method bool getHasStorePage()
 * @method string getLatitude()
 * @method string getCustomDirectionsUrl()
 * @method string getLongitude()
 * @method string getPhoneNumber()
 * @method Store setStoreName(string $storeName)
 * @method Store setIdentifier(string $storeIdentifier)
 * @method Store setImage(array $images)
 * @method Store setHasStorePage(bool $hasStorePage)
 * @method Store setCustomDirectionsUrl(string $customDirectionsUrl)
 * @method Store setLatitude(string $latitude)
 * @method Store setLongitude(string $longitude)
 * @method Store setPhoneNumber(string $phoneNumber)
 *
 * Page Table Functions
 * @method int getPageId()
 * @method string getDescription()
 * @method string getManagerName()
 * @method string getManagerPhone()
 * @method string getManagerEmail()
 * @method string getStoreEmail()
 * @method string getStoreHours()
 * @method string getAdditionalInfo()
 * @method string getStoreType()
 * @method string getCountry()
 * @method Store setDescription(string $description)
 * @method Store setManagerName(string $managerName)
 * @method Store setManagerPhone(string $managerPhone)
 * @method Store setManagerEmail(string $managerEmail)
 * @method Store setStoreEmail(string $storeEmail)
 * @method Store setStoreHours(string $storeHours)
 * @method Store setAdditionalInfo(string $additionalInfo)
 * @method Store setStoreType(string $storeType)
 * @method Store setCountry(string $country)
 *
 * Custom methods
 * @method array getEventIds()
 * @method Store setEventIds(array $values)
 */
class Store extends AbstractModel
{
    const COLUMN_STORE_ID = 'store_id';

    /**
     * Store Columns
     */
    const COLUMN_STORE_NAME = 'store_name';
    const COLUMN_STORE_IDENTIFIER = 'identifier';
    const COLUMN_IMAGE = 'image';
    const COLUMN_ADDRESS = 'address';
    const COLUMN_HAS_STORE_PAGE = 'has_store_page';
    const COLUMN_CUSTOM_DIRECTIONS_URL = 'custom_directions_url';
    const COLUMN_LATITUDE = 'latitude';
    const COLUMN_LONGITUDE = 'longitude';
    const COLUMN_PHONE_NUMBER = 'phone_number';
    const COLUMN_IS_ACTIVE = 'is_active';
    const COLUMN_POSITION = 'position';

    /**
     * Store Page Columns
     */
    const COLUMN_PAGE_ID = 'page_id';
    const COLUMN_DESCRIPTION = 'description';
    const COLUMN_MANAGER_NAME = 'manager_name';
    const COLUMN_MANAGER_PHONE = 'manager_phone';
    const COLUMN_MANAGER_EMAIL = 'manager_email';
    const COLUMN_STORE_EMAIL = 'store_email';
    const COLUMN_STORE_HOURS = 'store_hours';
    const COLUMN_ADDITIONAL_INFO = 'additional_info';
    const COLUMN_STORE_TYPE = 'store_type';
    const COLUMN_COUNTRY = 'country';

    const KEY_EVENTS = 'events';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var string
     */
    protected $storeHourHtml;

    /**
     * @var string
     */
    protected $additionalInfoHtml;

    /**
     * Store constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Filter $filter
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Filter $filter,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->filter = $filter;
    }

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init(StoreResource::class);
    }

    /**
     * Alias for getImage, since image holds an array of all images
     * @return array
     */
    public function getImages()
    {
        return $this->getImage();
    }

    /**
     * Alias for setImage, since image holds an array of all images
     * @param array $images
     * @return Store
     */
    public function setImages(array $images)
    {
        return $this->setImage($images);
    }

    /**
     * @return string|null
     */
    public function getBaseImage()
    {
        if (isset($this->getImages()[self::COLUMN_IMAGE])) {
            return $this->getImages()[self::COLUMN_IMAGE];
        }

        return null;
    }

    /**
     * @param string $imagePath
     * @return Store
     */
    public function setBaseImage(string $imagePath)
    {
        $images = $this->getImages();
        $images[self::COLUMN_IMAGE] = $imagePath;
        $this->setImages($images);

        return $this;
    }

    /**
     * @return string
     */
    public function getStoreHourHtml()
    {
        if ($this->storeHourHtml === null) {
            $this->storeHourHtml = $this->filter->filter($this->getStoreHours());
        }

        return $this->storeHourHtml;
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
     * @param bool $refresh
     * @return EventModel[]
     */
    public function getEvents($refresh = false)
    {
        if ($refresh || !$this->getData(self::KEY_EVENTS)) {
            $this->setData(self::KEY_EVENTS, []);
        }

        return $this->getData(self::KEY_EVENTS);
    }
}
