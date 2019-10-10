<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Block;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Edu\StoreFinderCompletion\Helper\Data as DataHelper;

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
    public function getRetailStoreUrl()
    {
        return $this->dataHelper->getStoreFinderUrl();
    }

    /**
     * @return string
     */
    public function getRetailStoreText()
    {
        return (string)__('Don\'t live near a store? Find our <strong>Retail Partners</strong>');
    }
}
