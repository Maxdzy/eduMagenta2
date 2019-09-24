<?php
/**
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */
namespace Edu\CmsSimpleBadge\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ObjectManager;

class Display extends Template
{
    protected $_storeManager;
    protected $_objectManager;

    public function __construct(
        Template\Context $context,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_objectManager = ObjectManager::getInstance();
        parent::__construct($context, $data);
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getStoreData()
    {
        $storeManagerDataList = $this->_storeManager->getStores();
        $options = [];

        foreach ($storeManagerDataList as $store) {
            $options[] = [
                "store_id" => $store->getId(),
                "store_base_url" => $store->getBaseUrl(),
                "store_code" => $store->getCode()
            ];
        }
        return $options;
    }

    public function getCurrentLocale()
    {
        $resolver = $this->_objectManager->get('Magento\Framework\Locale\Resolver');
        $result = [];
        $result['locale'] = $resolver->getLocale();
        $storeManager = $this->_storeManager->getStore();
        $result['baseUrl'] =  $storeManager->getBaseUrl();
        $resolverUrl = $this->_objectManager->get('Magento\Framework\UrlInterface');
        $result['pageUrl'] = $resolverUrl->getCurrentUrl();
        return $result;
    }
}
