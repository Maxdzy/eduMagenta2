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
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Template\Context;
use Scandiweb\StoreFinder\Helper\Data as DataHelper;

class Storefinder extends Template
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var array
     */
    protected $distanceOptions;

    /**
     * StoreList constructor.
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return array
     */
    public function getDistanceOptions()
    {
        if ($this->distanceOptions === null) {
            $this->distanceOptions = $this->dataHelper->getDistances();
        }

        return $this->distanceOptions;
    }

    /**
     * Retrieve HTML for search form select
     *
     * @return string
     */
    public function getDistanceSelect()
    {
        try {
            $options = $this->getDistanceOptions();

            /** @var Select $select */
            $select = $this->getLayout()->createBlock(Select::class);
            $select->setTitle(
                __('Distances (Store within X)')
            )->setClass(
                DataHelper::URI_PARAM_DISTANCE . ' chosen-active'
            )->setId(
                'storefinder-' . DataHelper::URI_PARAM_DISTANCE
            )->setOptions(
                $options
            )->setData(
                'name',
                DataHelper::URI_PARAM_DISTANCE
            )->setData(
                'extra_params',
                'data-bind="event: { change: submitPlaceSearch }, chosen: {}"'
            );

            return $select->getHtml();
        } catch (LocalizedException $exception) {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getMapMarkerUrl()
    {
         return $this->getViewFileUrl('Scandiweb_StoreFinder::images/storefinder-map-marker.png');
    }

    /**
     * @return string
     */
    public function getDefaultLocationName(): string
    {
        return $this->escapeJs($this->dataHelper->getDefaultLocationName());
    }

    /**
     * @return string
     */
    public function getDefaultLocationLatitude(): string
    {
        return $this->escapeJs($this->dataHelper->getDefaultLatitude());
    }

    /**
     * @return string
     */
    public function getDefaultLocationLongitude(): string
    {
        return $this->escapeJs($this->dataHelper->getDefaultLocationName());
    }

    /**
     * @return string
     */
    public function getSearchFromCustomer(): string
    {
        return $this->escapeJs($this->dataHelper->getSearchFromCustomer());
    }
}
