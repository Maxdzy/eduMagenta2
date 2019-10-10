<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Edu\StoreFinderCompletion\Model\ResourceModel\Store\Collection as StoreCollection;
use Edu\StoreFinderCompletion\Model\Store as StoreModel;

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
