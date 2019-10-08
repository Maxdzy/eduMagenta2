<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Maris Mols <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 */

namespace Scandiweb\StoreFinder\Api\Import\Store;

use Magento\Framework\Exception\LocalizedException;

interface ValidationInterface
{
    /**
     * @param array $store
     * @return mixed
     * @throws LocalizedException
     */
    public function performCheck(array $store);
}