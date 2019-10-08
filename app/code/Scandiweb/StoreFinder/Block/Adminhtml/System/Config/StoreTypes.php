<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * @method AbstractElement getElement()
 */
class StoreTypes extends AbstractFieldArray
{
    const OPTION_CODE_STORE_TYPE = 'store_type';

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param Context $context
     * @param SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        Context $context,
        SerializerInterface $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            self::OPTION_CODE_STORE_TYPE,
            [
                'label' => __('Store Type'),
                'renderer' => true,
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Store Type');
    }

    /**
     * @return array
     */
    public function getArrayRows()
    {
        $value = $this->getElement()->getData('value');
        if (is_string($value)) {
            $value = $this->serializer->unserialize($value);
            if (is_array($value)) {
                $this->getElement()->setData('value', $value);
            }
        }

        return parent::getArrayRows();
    }
}
