<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Observer\Adminhtml;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Scandiweb\StoreFinder\Helper\UrlUpdate as UrlUpdateHelper;

class UrlUpdate implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var UrlUpdateHelper
     */
    protected $urlUpdateHelper;

    /**
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     * @param UrlUpdateHelper $urlUpdateHelper
     */
    public function __construct(
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        UrlUpdateHelper $urlUpdateHelper
    ) {
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->urlUpdateHelper = $urlUpdateHelper;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->urlUpdateHelper->recreateUrlRewritesFromConfig();
    }
}
