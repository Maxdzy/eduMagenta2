<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Controller\Ajax;

use Exception;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Edu\StoreFinderCompletion\Helper\Data as DataHelper;

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
