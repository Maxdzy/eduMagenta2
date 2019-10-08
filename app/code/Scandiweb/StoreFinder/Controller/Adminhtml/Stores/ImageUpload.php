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
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;

class ImageUpload extends Action
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'Scandiweb_StoreFinder::edit_store';

    /**
     * @var ImageUploader
     */
    protected $imageUploader;

    /**
     * @param Context $context
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        Context $context,
        ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var Json $jsonResult */
        $jsonResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $result = $this->imageUploader->saveFileToTmpDir($this->getFileId());

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $jsonResult->setData($result);
    }

    /**
     * @return string
     */
    protected function getFileId()
    {
        return $this->getRequest()->getParam('param_name');
    }
}
