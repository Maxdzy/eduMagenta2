<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Controller\Ajax;

use Exception;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Scandiweb\StoreFinder\Helper\Data as DataHelper;

class Storesnearyou extends Mapstorelist
{
    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $json */
        $json = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $data = [];
            $data[DataHelper::JSON_KEY_STATUS] = 'success';
            $data[DataHelper::JSON_KEY_STORES] = count($this->getDistances());
            $data[DataHelper::JSON_KEY_PARAMS] = $this->getParams();
            $data[DataHelper::JSON_KEY_TOTAL_SIZE] = count($this->getDistances());
            $json->setData($data);
        } catch (Exception $exception) {
            $json->setData([
                DataHelper::JSON_KEY_STATUS => 'error',
                DataHelper::JSON_KEY_MESSAGE => __('Something went wrong')
            ]);
            $this->logger->error($exception->getMessage());
        }

        return $json;
    }
}
