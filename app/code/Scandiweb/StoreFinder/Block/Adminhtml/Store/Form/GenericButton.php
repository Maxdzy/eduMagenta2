<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Block\Adminhtml\Store\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\UrlInterface;
use Scandiweb\StoreFinder\Api\StoreRepositoryInterface;
use Scandiweb\StoreFinder\Model\Store as StoreModel;

class GenericButton
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        RequestInterface $request,
        UrlInterface $urlBuilder,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->storeRepository = $storeRepository;
    }

    /**
     * Return Store ID
     *
     * @return int|null
     */
    public function getStoreId()
    {
        if ($this->storeId === null) {
            try {
                $this->storeId = $this->storeRepository->getById(
                    $this->request->getParam(StoreModel::COLUMN_STORE_ID)
                )->getId();
            } catch (NotFoundException $e) {
                $this->storeId = null;
            }
        }

        return $this->storeId;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
