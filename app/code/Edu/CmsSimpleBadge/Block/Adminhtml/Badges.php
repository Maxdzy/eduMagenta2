<?php
/**
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Badges extends Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_badges';
        $this->_blockGroup = 'Edu_CmsSimpleBadge';
        $this->_headerText = __('Badges');
        $this->_addButtonLabel = __('Create New Badges');
        parent::_construct();
    }
}
