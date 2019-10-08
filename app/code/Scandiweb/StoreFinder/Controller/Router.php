<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 */

namespace Scandiweb\StoreFinder\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Url;
use Scandiweb\StoreFinder\Api\StoreRepositoryInterface;
use Scandiweb\StoreFinder\Helper\Data as DataHelper;
use Scandiweb\StoreFinder\Model\Store as StoreModel;

class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @param ActionFactory $actionFactory
     * @param StoreRepositoryInterface $storeRepository
     * @param DataHelper $dataHelper
     */
    public function __construct(
        ActionFactory $actionFactory,
        StoreRepositoryInterface $storeRepository,
        DataHelper $dataHelper
    ) {
        $this->actionFactory = $actionFactory;
        $this->storeRepository = $storeRepository;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Validate and Match Cms Page and modify request
     *
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $pathData = explode('/', trim($request->getPathInfo(), '/'));

        if (!isset($pathData[1]) || $pathData[0] !== $this->dataHelper->getStoreSeoUri()) {
            return null;
        }

        $identifier = $pathData[1];
        $store = $this->getStoreByIdentifier($pathData[1]);

        if (!($store instanceof StoreModel) || !$store->getId() || !$store->getHasStorePage()) {
            return null;
        }

        $request->setModuleName('storefinder')
            ->setControllerName('store')
            ->setActionName('index')
            ->setParam('store', $store->getId());

        $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

        return $this->actionFactory->create(Forward::class);
    }

    /**
     * @param string $identifier
     * @return null|StoreModel
     */
    protected function getStoreByIdentifier(string $identifier)
    {
        try {
            return $this->storeRepository->getByIdentifier($identifier);
        } catch (NotFoundException $exception) {
            return null;
        }
    }
}
