<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Scandiweb\StoreFinder\Helper\Data as DataHelper;

class RsvpPopup extends Template
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
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->escapeUrl($this->getUrl(DataHelper::URL_LOAD_EVENT_SUBSCRIBE));
    }
}
