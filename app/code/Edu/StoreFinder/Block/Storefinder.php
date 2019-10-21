<?php
/**
 * @category Edu
 * @package Edu\StoreFinder
 * @author Maxim Dzyuba <maxim.d@tdo.kz>
 */

namespace Edu\StoreFinder\Block;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Template\Context;
use Scandiweb\StoreFinder\Block\Storefinder as ScandiwebStorefinder;
use Scandiweb\StoreFinder\Helper\Data as DataHelper;

/**
 * Class Storefinder
 * @package Edu\StoreFinder\Block
 */
class Storefinder extends ScandiwebStorefinder
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
        parent::__construct($context, $dataHelper, $data);
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return array
     */
    public function getCountryFilterOptions(): array
    {
        return array_merge(['' => __('All')], $this->dataHelper->getAllowedCountries());
    }

    /**
     * Retrieve HTML for search form select
     *
     * @return string
     */
    public function getCountrySelect(): string
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
                'store-' . DataHelper::URI_PARAM_STORE_COUNTRY
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
}
