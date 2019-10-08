<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Controller\Adminhtml\Stores;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Scandiweb\StoreFinder\Api\StoreRepositoryInterface;
use Scandiweb\StoreFinder\Controller\Adminhtml\AbstractStore;
use Scandiweb\StoreFinder\Model\ResourceModel\Store\FileInfo;
use Scandiweb\StoreFinder\Model\Store as StoreModel;

class Save extends AbstractStore
{
    const IMAGE_FILENAME_CODE = 'file';
    const KEY_EXTRA_IMAGE_ARRAY = 'extra_image';

    /**
     * @var ImageUploader
     */
    protected $imageUploader;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var FileInfo
     */
    protected $fileInfo;

    /**
     * Save constructor.
     * @param Context $context
     * @param StoreRepositoryInterface $storeRepository
     * @param Registry $registry
     * @param Session $session
     * @param DataPersistorInterface $dataPersistor
     * @param ImageUploader $imageUploader
     * @param SerializerInterface $serializer
     * @param FileInfo $fileInfo
     */
    public function __construct(
        Context $context,
        StoreRepositoryInterface $storeRepository,
        Registry $registry,
        Session $session,
        DataPersistorInterface $dataPersistor,
        ImageUploader $imageUploader,
        SerializerInterface $serializer,
        FileInfo $fileInfo
    ) {
        parent::__construct($context, $storeRepository, $registry, $session, $dataPersistor);
        $this->imageUploader = $imageUploader;
        $this->serializer = $serializer;
        $this->fileInfo = $fileInfo;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var Http $request */
        $request = $this->getRequest();
        $data = $request->getPostValue();
        if ($data) {
            $storeId = $request->getParam(StoreModel::COLUMN_STORE_ID);

            if (empty($data[StoreModel::COLUMN_STORE_ID])) {
                $data[StoreModel::COLUMN_STORE_ID] = null;
            }

            try {
                $model = $this->storeRepository->getById($storeId);
            } catch (NotFoundException $exception) {
                $model = $this->storeRepository->create();
            }

            if (!$model->getId() && $storeId) {
                $this->messageManager->addErrorMessage(__('This store no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            try {
                $this->validateData($data);

                // Process Images
                $images = [];
                if (isset($data[StoreModel::COLUMN_IMAGE])) {
                    $images[StoreModel::COLUMN_IMAGE] = $this->processImage($data[StoreModel::COLUMN_IMAGE]);
                }
                if (isset($data[self::KEY_EXTRA_IMAGE_ARRAY])) {
                    foreach ($data[self::KEY_EXTRA_IMAGE_ARRAY] as $key => $extra) {
                        if ($key === StoreModel::COLUMN_IMAGE) {
                            continue;
                        }
                        $images[$key] = $this->processImage($extra);
                    }
                }
                $data[StoreModel::COLUMN_IMAGE] = $images;

                $model->setData($data);
                $this->storeRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the store.'));
                $this->dataPersistor->clear(self::PERSISTOR_KEY_CURRENT_STORE);

                if ($request->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', [StoreModel::COLUMN_STORE_ID => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the store.'));
            }

            $this->dataPersistor->set(self::PERSISTOR_KEY_CURRENT_STORE, $data);

            return $resultRedirect->setPath(
                '*/*/edit',
                [StoreModel::COLUMN_STORE_ID => $this->getRequest()->getParam(StoreModel::COLUMN_STORE_ID)]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param array|string $image
     * @return null|string
     * @throws Exception
     */
    protected function processImage($image)
    {
        if (is_array($image)) {
            if (isset($image[self::IMAGE_FILENAME_CODE])) {
                $image = $image[self::IMAGE_FILENAME_CODE];
            } elseif (isset($image[0][self::IMAGE_FILENAME_CODE])) {
                $image = $image[0][self::IMAGE_FILENAME_CODE];
            } else {
                return '';
            }
        }

        if (!is_string($image) || empty($image)) {
            return null;
        }

        try {
            $this->imageUploader->moveFileFromTmp($image);
        } catch (Exception $exception) {
            if (!$this->fileInfo->isExist($image)) {
                throw $exception;
            }
        }

        return $image;
    }
}
