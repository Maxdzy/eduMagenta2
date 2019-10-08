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
use Scandiweb\StoreFinder\Helper\Data as DataHelper;

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
