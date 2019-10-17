<?php
/**
 * @category Edu
 * @package Edu\StoreFinder
 * @author Maxim Dzyuba <maxim.d@tdo.kz>
 */

namespace Edu\StoreFinder\Block;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Tests\NamingConvention\true\string;
use Scandiweb\StoreFinder\Helper\Data as DataHelper;

/**
 * Class StoreList
 * @package Edu\StoreFinder\Block
 */
class StoreList extends Template
{
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

    /**
     * @return string
     */
    public function getRetailStoreUrl(): string
    {
        return $this->dataHelper->getStoreFinderUrl();
    }

    /**
     * @return string
     */
    public function getRetailStoreText(): string
    {
        return (string)__('Don\'t live near a store? Find our <strong>Retail Partners</strong>');
    }
}
