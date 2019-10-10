<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Edu\StoreFinderCompletion\Block\Adminhtml\System\Config\Distances;
use Edu\StoreFinderCompletion\Block\Adminhtml\System\Config\StoreTypes;
use Edu\StoreFinderCompletion\Helper\Map as MapHelper;
use Edu\StoreFinderCompletion\Model\Store as StoreModel;

class Data extends AbstractHelper
{
    const TABLE_STORES = 'scandiweb_store_finder_stores';
    const TABLE_EVENTS = 'scandiweb_store_finder_events';
    const TABLE_STORE_EVENT_BIND = 'scandiweb_store_finder_store_event_bind';
    const TABLE_EVENT_RSVP = 'scandiweb_store_finder_event_rsvp';

    const CONFIG_PATH_EVENTS_ENABLED = 'Edu_store_finder/general/events_enabled';
    const CONFIG_PATH_ALLOWED_COUNTRIES = 'Edu_store_finder/general/allowed_countries';
    const CONFIG_PATH_ALLOWED_STORE_TYPES = 'Edu_store_finder/general/allowed_store_types';
    const CONFIG_PATH_DISTANCES = 'Edu_store_finder/general/distances';
    const CONFIG_PATH_DEFAULT_LOCATION_NAME = 'Edu_store_finder/general/default_location_name';
    const CONFIG_PATH_DEFAULT_LATITUDE = 'scandiweb_store_finder/general/default_latitude';
    const CONFIG_PATH_DEFAULT_LONGITUDE = 'scandiweb_store_finder/general/default_longitude';
    const CONFIG_PATH_SEARCH_FROM_CUSTOMER = 'Edu_store_finder/general/search_from_customer';
    const CONFIG_PATH_DEFAULT_API_KEY = 'scandiweb_store_finder/general/google_maps_api_key';
    const CONFIG_PATH_EVENT_LIST_HEAD_IMAGE = 'Edu_store_finder/events/head_image';

    const CONFIG_PATH_SEO_STOREFINDER = 'Edu_store_finder/seo/storefinder';
    const CONFIG_PATH_SEO_STORELIST = 'Edu_store_finder/seo/storelist';
    const CONFIG_PATH_SEO_EVENTLIST = 'Edu_store_finder/seo/eventlist';
    const CONFIG_PATH_SEO_STOREDETAILS = 'Edu_store_finder/seo/storedetails';

    const URI_PARAM_LIMIT = 'limit';
    const URI_PARAM_PAGE = 'page';
    const URI_PARAM_STORE_TYPE = 'storetype';
    const URI_PARAM_STORE_COUNTRY = 'storecountry';
    const URI_PARAM_STORE_ID = 'store';
    const URI_PARAM_DISTANCE = 'distance';
    const URI_PARAM_LATITUDE = 'lat';
    const URI_PARAM_LONGITUDE = 'lon';
    const URI_PARAM_LAST_INDEX = 'lastindex';
    const URI_PARAM_BOUNDS = 'bounds';
    const URI_PARAM_DELIMITER = ',';

    const BOUNDS_PARAM_LAT_MAX = 'lat_max';
    const BOUNDS_PARAM_LNG_MAX = 'lng_max';
    const BOUNDS_PARAM_LAT_MIN = 'lat_min';
    const BOUNDS_PARAM_LNG_MIN = 'lng_min';

    const JSON_KEY_STATUS = 'status';
    const JSON_KEY_MESSAGE = 'message';
    const JSON_KEY_PARAMS = 'params';
    const JSON_KEY_TOTAL_SIZE = 'total_size';
    const JSON_KEY_STORES = 'stores';
    const JSON_KEY_EVENTS = 'events';

    const URL_LOAD_STORELIST = 'storefindercompletion/ajax/storelist';
    const URL_LOAD_EVENTLIST = 'storefindercompletion/ajax/eventlist';
    const URL_LOAD_MAPSTORELIST = 'storefindercompletion/ajax/mapstorelist';
    const URL_LOAD_STORESNEARYOU = 'storefindercompletion/ajax/storesnearyou';
    const URL_LOAD_EVENT_SUBSCRIBE = 'storefindercompletion/ajax/eventSubscribe';
    const URL_STORELIST = 'storefindercompletion/stores/index';
    const URL_EVENTLIST = 'storefindercompletion/events/index';
    const URL_STORE_FINDER = 'storefindercompletion/index/index';
    const URL_STORE_DETAIL = 'storefindercompletion/store/index';

    const SRC_EVENT_IMAGES = 'storefindercompletion/events';

    const REGISTRY_KEY_CURRENT_STORE = 'storefindercompletion_current_store';

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var array
     */
    protected $allowedCountries;

    /**
     * @var array
     */
    protected $allowedStoreTypes;

    /**
     * @var array
     */
    protected $distances;

    /**
     * @var ListsInterface
     */
    protected $translatedLists;

    /**
     * @var AssetRepository
     */
    protected $assetRepository;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var Map
     */
    protected $mapHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Data constructor.
     * @param Context $context
     * @param SerializerInterface $serializer
     * @param ListsInterface $translatedLists
     * @param AssetRepository $assetRepository
     * @param UrlInterface $urlBuilder
     * @param DateTime $date
     * @param Map $mapHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        SerializerInterface $serializer,
        ListsInterface $translatedLists,
        AssetRepository $assetRepository,
        UrlInterface $urlBuilder,
        DateTime $date,
        MapHelper $mapHelper,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->serializer = $serializer;
        $this->translatedLists = $translatedLists;
        $this->assetRepository = $assetRepository;
        $this->urlBuilder = $urlBuilder;
        $this->date = $date;
        $this->mapHelper = $mapHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @return array
     */
    public function getAllowedStoreTypes()
    {
        if (empty($this->allowedStoreTypes)) {
            try {
                $data = $this->serializer->unserialize(
                    $this->scopeConfig->getValue(self::CONFIG_PATH_ALLOWED_STORE_TYPES)
                );
            } catch (\Exception $exception) {
                return [];
            }

            $this->allowedStoreTypes = [];
            foreach ($data as $storeType) {
                if (!array_key_exists(StoreTypes::OPTION_CODE_STORE_TYPE, $storeType)) {
                    continue;
                }
                $storeType = $storeType[StoreTypes::OPTION_CODE_STORE_TYPE];
                $this->allowedStoreTypes[$storeType] = __($storeType);
            }
        }

        return $this->allowedStoreTypes;
    }

    /**
     * @return array
     */
    public function getAllowedCountries()
    {
        if (empty($this->allowedCountries)) {
            $data = $this->scopeConfig->getValue(self::CONFIG_PATH_ALLOWED_COUNTRIES);

            $this->allowedCountries = [];
            foreach (explode(',', $data) as $countryCode) {
                $this->allowedCountries[$countryCode] = $this->translatedLists->getCountryTranslation($countryCode);
            }
        }

        return $this->allowedCountries;
    }

    /**
     * @return array
     */
    public function getDistances()
    {
        if (empty($this->distances)) {
            try {
                $data = $this->serializer->unserialize(
                    $this->scopeConfig->getValue(self::CONFIG_PATH_DISTANCES)
                );
            } catch (\Exception $exception) {
                return [];
            }

            $this->distances = [];
            foreach ($data as $distance) {
                if (!isset($distance[Distances::OPTION_CODE_DISTANCE], $distance[Distances::OPTION_CODE_LABEL])) {
                    continue;
                }
                $distanceValue = $distance[Distances::OPTION_CODE_DISTANCE];
                if (empty($distance)) {
                    continue;
                }
                $label = $distance[Distances::OPTION_CODE_LABEL];
                if (empty($label)) {
                    $label = $distanceValue;
                }
                $this->distances[$distanceValue] = __($label);
            }
        }

        return $this->distances;
    }

    /**
     * @return bool
     */
    public function isEventsEnabled()
    {
        return (bool)$this->scopeConfig->getValue(self::CONFIG_PATH_EVENTS_ENABLED);
    }

    /**
     * @return string
     */
    public function getDefaultLocationName()
    {
        return (string)$this->scopeConfig->getValue(self::CONFIG_PATH_DEFAULT_LOCATION_NAME);
    }

    /**
     * @return double
     */
    public function getDefaultLatitude()
    {
        return (double)$this->scopeConfig->getValue(self::CONFIG_PATH_DEFAULT_LATITUDE);
    }

    /**
     * @return double
     */
    public function getDefaultLongitude()
    {
        return (double)$this->scopeConfig->getValue(self::CONFIG_PATH_DEFAULT_LONGITUDE);
    }

    /**
     * @return double
     */
    public function getSearchFromCustomer()
    {
        return (int)$this->scopeConfig->getValue(self::CONFIG_PATH_SEARCH_FROM_CUSTOMER);
    }

    /**
     * @return string
     */
    public function getGoogleMapsApiKey()
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_DEFAULT_API_KEY);
    }

    /**
     * @return null|string
     * @throws NoSuchEntityException
     */
    public function getEventListHeadImageUrl()
    {
        $file = $this->scopeConfig->getValue(self::CONFIG_PATH_EVENT_LIST_HEAD_IMAGE);
        if ($file === null) {
            return null;
        }

        /** @var Store $store */
        $store = $this->storeManager->getStore();
        $mediaBaseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        return sprintf('%s/%s/%s', rtrim($mediaBaseUrl, '/'), self::SRC_EVENT_IMAGES, ltrim($file, '/'));
    }

    /**
     * @return string|null
     */
    public function getStoreSeoUri()
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_SEO_STOREDETAILS) ?: null;
    }

    /**
     * @return string|null
     */
    public function getEventListSeoUri()
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_SEO_EVENTLIST) ?: null;
    }

    /**
     * @return string|null
     */
    public function getStoreListSeoUri()
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_SEO_STORELIST) ?: null;
    }

    /**
     * Retrieve url of a view file
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    public function getViewFileUrl($fileId, array $params = [])
    {
        return $this->assetRepository->getUrlWithParams($fileId, $params);
    }

    /**
     * Returns timestamp if $format is null, or formatted datetime if format is string
     * @param null|string $format
     * @return int|string
     */
    public function getCurrentTime($format = null)
    {
        if ($format === null) {
            return $this->date->timestamp();
        } else {
            return $this->date->date($format);
        }
    }

    /**
     * @param StoreModel $store
     * @return string
     */
    public function getStorePageUrl($store)
    {
        $seoUri = $this->getStoreSeoUri();

        if ($seoUri) {
            return $this->urlBuilder->getDirectUrl(
                sprintf(
                    '%s/%s',
                    $seoUri,
                    $store->getIdentifier()
                )
            );
        } else {
            return $this->urlBuilder->getUrl(
                static::URL_STORE_DETAIL,
                [
                    static::URI_PARAM_STORE_ID => $store->getId(),
                    '_use_rewrite' => false
                ]
            );
        }
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @return MapHelper
     */
    public function getMapHelper()
    {
        return $this->mapHelper;
    }

    /**
     * @return string
     */
    public function getStoreFinderUrl()
    {
        if (null !== $seoUri = $this->getStoreFinderSeoUri()) {
            return $this->_urlBuilder->getDirectUrl($seoUri);
        } else {
            return $this->urlBuilder->getUrl(static::URL_STORE_FINDER);
        }
    }

    /**
     * @return string
     */
    public function getEventListUrl()
    {
        if (null !== $seoUri = $this->getEventListSeoUri()) {
            return $this->_urlBuilder->getDirectUrl($seoUri);
        } else {
            return $this->urlBuilder->getUrl(static::URL_EVENTLIST);
        }
    }

    /**
     * @return string
     */
    public function getStoreListUrl()
    {
        if (null !== $seoUri = $this->getStoreListSeoUri()) {
            return $this->_urlBuilder->getDirectUrl($seoUri);
        } else {
            return $this->urlBuilder->getUrl(static::URL_STORELIST);
        }
    }

    /**
     * @return string|null
     */
    public function getStoreFinderSeoUri()
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_SEO_STOREFINDER) ?: null;
    }

    /**
     * @return array
     */
    public function getAllStoreStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled'),
        ];
    }
}
