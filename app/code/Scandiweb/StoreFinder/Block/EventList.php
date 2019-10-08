<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Block;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Scandiweb\StoreFinder\Helper\Data as DataHelper;

class EventList extends Template
{
    const DATA_KEY_HEAD_IMAGE = 'head_image';

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * StoreList constructor.
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param array $data
     */
    public function __construct(Context $context, DataHelper $dataHelper, array $data = [])
    {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return array
     */
    public function getCountryFilterOptions()
    {
        return array_merge(['' => __('All')], $this->dataHelper->getAllowedCountries());
    }

    /**
     * Retrieve HTML for search form select
     *
     * @return string
     */
    public function getCountrySelect()
    {
        $options = $this->getCountryFilterOptions();

        try {
            /** @var Select $select */
            $select = $this->getLayout()->createBlock(Select::class);
            $select->setTitle(
                __('Store Country Filter')
            )->setClass(
                DataHelper::URI_PARAM_STORE_COUNTRY . ' chosen-active'
            )->setId(
                'event-' . DataHelper::URI_PARAM_STORE_COUNTRY
            )->setOptions(
                $options
            )->setData(
                'name',
                DataHelper::URI_PARAM_STORE_COUNTRY
            )->setData(
                'extra_params',
                'data-bind="event: {change: filterCountry}, chosen: {}"'
            );
        } catch (LocalizedException $exception) {
            return '';
        }

        return $select->getHtml();
    }

    /**
     * @return string
     */
    public function getHeadImageUrl()
    {
        if ($this->getData(self::DATA_KEY_HEAD_IMAGE) === null) {
            $url = $this->dataHelper->getEventListHeadImageUrl();
            if ($url !== null) {
                $this->setData(self::DATA_KEY_HEAD_IMAGE, $this->escapeUrl($url));
            }
        }

        return $this->getData(self::DATA_KEY_HEAD_IMAGE);
    }
}
