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
