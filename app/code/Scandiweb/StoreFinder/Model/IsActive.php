<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Emils Brass <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 */

namespace Scandiweb\StoreFinder\Model;

use Magento\Framework\Data\OptionSourceInterface;
use Scandiweb\StoreFinder\Helper\Data;

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
