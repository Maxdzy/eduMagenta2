<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Edu\StoreFinderCompletion\Helper\Data as DataHelper;

class StoreType extends AbstractSource
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * StoreCountries constructor.
     * @param DataHelper $dataHelper
     */
    public function __construct(DataHelper $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getAllOptions()
    {
        $options = [];
        foreach ($this->dataHelper->getAllowedStoreTypes() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => __($label)
            ];
        }

        return $options;
    }
}
