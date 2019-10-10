<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>
 */

namespace Edu\StoreFinderCompletion\Model;

use Magento\Framework\Data\OptionSourceInterface;
use Edu\StoreFinderCompletion\Helper\Data;

class IsActive implements OptionSourceInterface
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * IsActive constructor.
     * @param Data $dataHelper
     */
    public function __construct(Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $availableOptions = $this->dataHelper->getAllStoreStatuses();

        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }

        return $options;
    }
}
