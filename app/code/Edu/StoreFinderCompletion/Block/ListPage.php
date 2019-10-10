<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Edu\StoreFinderCompletion\Helper\Data as DataHelper;

class ListPage extends Template
{
    const KEY_LIST = 'list';
    const LIST_STORES = 'stores';
    const LIST_EVENTS = 'events';

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * ListPage constructor.
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param array $data
     */
    public function __construct(Context $context, DataHelper $dataHelper, array $data = [])
    {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isStoreList()
    {
        return $this->getData(self::KEY_LIST) === self::LIST_STORES;
    }

    /**
     * @return bool
     */
    public function isEventList()
    {
        return $this->getData(self::KEY_LIST) === self::LIST_EVENTS;
    }

    /**
     * @return string
     */
    public function getEventPageUrl()
    {
        return $this->escapeUrl($this->dataHelper->getEventListUrl());
    }

    /**
     * @return string
     */
    public function getStorePageUrl()
    {
        return $this->escapeUrl($this->dataHelper->getStoreListUrl());
    }
}
