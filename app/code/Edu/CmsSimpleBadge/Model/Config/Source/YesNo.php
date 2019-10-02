<?php

namespace Edu\CmsSimpleBadge\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class YesNo extends AbstractSource
{
    protected $_options;

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => '0', 'label' => __('No')],
                ['value' => '1', 'label' => __('Yes')],
                ['value' => '2', 'label' => __('Yes2')],
            ];
        }
        return $this->_options;
    }
    final public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('No')],
            ['value' => '1', 'label' => __('Yes')],
            ['value' => '2', 'label' => __('Yes2')],
        ];
    }
}
