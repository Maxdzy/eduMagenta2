<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Block;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\App\Response\Redirect;
use Edu\StoreFinderCompletion\Helper\Data as DataHelper;
use Edu\StoreFinderCompletion\Model\Event;
use Edu\StoreFinderCompletion\Model\Store;
use Edu\StoreFinderCompletion\Model\ResourceModel\Store\FileInfo;

class StoreDetailsPage extends Template
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Redirect
     */
    protected $redirect;

    /**
     * @var FileInfo
     */
    protected $fileInfo;

    /**
     * StoreDetailsPage constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Redirect $redirect
     * @param FileInfo $fileInfo
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Redirect $redirect,
        FileInfo $fileInfo,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->redirect = $redirect;
        $this->fileInfo = $fileInfo;
    }

    /**
     * @return Store
     */
    public function getStore()
    {
        return $this->registry->registry(DataHelper::REGISTRY_KEY_CURRENT_STORE);
    }

    /**
     * @return string
     */
    public function getReturnUrl()
    {
        $return = $this->redirect->getRefererUrl();
        if (!$return) {
            $return = $this->getUrl(DataHelper::URL_STORE_FINDER);
        }

        return $this->escapeUrl($return);
    }

    /**
     * @return int
     */
    public function getStoreId(): int
    {
        return $this->getStore()->getStoreId();
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->escapeHtml($this->getStore()->getStoreName());
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->escapeHtml($this->getStore()->getDescription());
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->escapeHtml($this->getStore()->getAddress());
    }

    /**
     * @return string
     */
    public function getDirectionsUrl()
    {
        return $this->escapeUrl($this->getStore()->getCustomDirectionsUrl());
    }

    /**
     * @return string[]
     */
    public function getImageUrls()
    {
        $images = [];
        foreach ($this->getStore()->getImages() as $image) {
            $images[] = $this->escapeUrl($this->fileInfo->getUrl($image));
        }

        return $images;
    }

    /**
     * @return string
     */
    public function getStorePhone()
    {
        return $this->escapeHtml($this->getStore()->getPhoneNumber());
    }

    /**
     * @return string
     */
    public function getStoreEmail()
    {
        return $this->escapeHtml($this->getStore()->getStoreEmail());
    }

    /**
     * @return string
     */
    public function getManagerName()
    {
        return $this->escapeHtml($this->getStore()->getManagerName());
    }

    /**
     * @return string
     */
    public function getManagerPhone()
    {
        return $this->escapeHtml($this->getStore()->getManagerPhone());
    }

    /**
     * @return string
     */
    public function getManagerEmail()
    {
        return $this->escapeHtml($this->getStore()->getManagerEmail());
    }

    /**
     * @return string
     */
    public function getStoreHours()
    {
        return $this->getStore()->getStoreHourHtml();
    }

    /**
     * @return string
     */
    public function getAdditionalInfo()
    {
        return $this->getStore()->getAdditionalInfoHtml();
    }

    /**
     * @return bool
     */
    public function isShowAdditionalInfo()
    {
        return (bool)$this->getStore()->getAdditionalInfo();
    }



    /**
     * @param Event $event
     * @return string
     */
    public function getEventMonth($event)
    {
        if ($event->getDateStart('Y-m') === $event->getDateEnd('Y-m')) {
            return $event->getDateStart('M');
        }

        return sprintf('%s - %s', $event->getDateStart('M'), $event->getDateEnd('M'));
    }

    /**
     * @param Event $event
     * @return string
     */
    public function getEventDate($event)
    {
        if ($event->getDateStart() === $event->getDateEnd()) {
            return $event->getDateStart('d');
        }

        return sprintf('%s - %s', $event->getDateStart('d'), $event->getDateEnd('d'));
    }
}
