<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Scandiweb\StoreFinder\Api\StoreRepositoryInterface;
use Scandiweb\StoreFinder\Model\Store as StoreModel;

abstract class AbstractStore extends Action
{
    const REGISTRY_KEY_CURRENT_STORE = 'current_store';
    const SESSION_KEY_CURRENT_STORE = 'current_store';
    const PERSISTOR_KEY_CURRENT_STORE = 'current_store';

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * AbstractStore constructor.
     * @param Context $context
     * @param StoreRepositoryInterface $storeRepository
     * @param Registry $registry
     * @param Session $session
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        StoreRepositoryInterface $storeRepository,
        Registry $registry,
        Session $session,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);

        $this->storeRepository = $storeRepository;
        $this->registry = $registry;
        $this->session = $session;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @return StoreModel
     */
    protected function initStore()
    {
        $storeId = (int)$this->getRequest()->getParam(StoreModel::COLUMN_STORE_ID);

        try {
            $store = $this->storeRepository->getById($storeId);
        } catch (NotFoundException $exception) {
            $store = $this->storeRepository->create();
        }

        $this->registry->register(self::REGISTRY_KEY_CURRENT_STORE, $store);

        return $store;
    }

    /**
     * @param array $data
     * @throws LocalizedException
     */
    protected function validateData($data)
    {
        if (!is_array($data)) {
            throw new LocalizedException(__('Invalid data'));
        }

        if (!array_key_exists(StoreModel::COLUMN_STORE_NAME, $data)) {
            throw new LocalizedException(__('Missing required argument Store Name'));
        }

        if (!array_key_exists(StoreModel::COLUMN_STORE_IDENTIFIER, $data)) {
            throw new LocalizedException(__('Missing required argument Identifier'));
        }

        if (!preg_match('/^[a-z-]+$/', $data[StoreModel::COLUMN_STORE_IDENTIFIER])) {
            throw new LocalizedException(__('Identifier must contain only characters a-z and "-" (dash)'));
        }
    }
}
