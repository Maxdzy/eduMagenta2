<?php

namespace Edu\StickyHeader\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Display extends Template
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function sayHello()
    {
        return 'Hello World';
    }
}
