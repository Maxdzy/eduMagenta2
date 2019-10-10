<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>
 */

namespace Edu\StoreFinderCompletion\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Url;
use Edu\StoreFinderCompletion\Api\StoreRepositoryInterface;
use Edu\StoreFinderCompletion\Helper\Data as DataHelper;
use Edu\StoreFinderCompletion\Model\Store as StoreModel;

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
