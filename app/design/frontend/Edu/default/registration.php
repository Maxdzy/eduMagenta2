<?php

use Magento\Framework\Component\ComponentRegistrar;

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
ComponentRegistrar::register(
    ComponentRegistrar::THEME,
    'frontend/Skin/default',
    __DIR__
);
