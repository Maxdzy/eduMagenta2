<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Controller\Adminhtml\Stores;

use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultFactory;
use Scandiweb\StoreFinder\Controller\Adminhtml\AbstractStore;

class NewAction extends AbstractStore
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var Forward $forward */
        $forward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);

        return $forward->forward('edit');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Scandiweb_StoreFinder::view_store');
    }
}
