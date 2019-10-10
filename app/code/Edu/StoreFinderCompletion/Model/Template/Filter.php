<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */
namespace Edu\StoreFinderCompletion\Model\Template;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;

class Filter extends \Magento\Email\Model\Template\Filter
{
    /**
     * Whether to allow SID in store directive: AUTO
     *
     * @var bool
     */
    protected $_useSessionInUrl;

    /**
     * Setter whether SID is allowed in store directive
     *
     * @param bool $flag
     * @return $this
     */
    public function setUseSessionInUrl($flag)
    {
        $this->_useSessionInUrl = (bool)$flag;

        return $this;
    }

    /**
     * Retrieve media file URL directive
     *
     * @param string[] $construction
     * @return string
     */
    public function mediaDirective($construction)
    {
        $params = $this->getParameters($construction[2]);
        /** @var Store $store */
        $store = $this->_storeManager->getStore();

        return $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $params['url'];
    }
}
