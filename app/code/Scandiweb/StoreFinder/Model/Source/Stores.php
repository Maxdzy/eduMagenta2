<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Scandiweb\StoreFinder\Model\ResourceModel\Store\Collection as StoreCollection;
use Scandiweb\StoreFinder\Model\Store as StoreModel;

class Stores extends AbstractSource
{
    /**
     * @var StoreCollection
     */
    protected $storeCollection;

    /**
     * Stores constructor.
     * @param StoreCollection $storeCollection
     */
    public function __construct(StoreCollection $storeCollection)
    {
        $this->storeCollection = $storeCollection;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getAllOptions()
    {
        $options = [];
        /** @var StoreModel $store */
        foreach ($this->storeCollection->getItems() as $store) {
            $options[] = [
                'value' => $store->getId(),
                'label' => __($store->getStoreName())
            ];
        }

        return $options;
    }
}
