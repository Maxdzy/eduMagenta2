<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Maris Mols <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 */

namespace Scandiweb\StoreFinder\Import\Store;

use Magento\Framework\Exception\LocalizedException;
use Scandiweb\StoreFinder\Api\Import\Store\ValidationInterface;

class Validation implements ValidationInterface
{
    /**
     * @param array $store
     * @return mixed|void
     * @throws LocalizedException
     */
    public function performCheck(array $store)
    {
        $fieldToCheckForExistence = ['identifier', 'store_name', 'address', 'has_store_page', 'custom_directions_url',
            'latitude', 'longitude', 'phone_number', 'description', 'manager_name', 'manager_phone', 'manager_email',
            'store_email', 'store_hours', 'additional_info', 'store_type', 'country'];

        foreach ($fieldToCheckForExistence as $field) {
            if (!isset($store[$field])) {
                throw new LocalizedException(__(sprintf('%s field is not set', $field)));
            }
        }
    }
}
