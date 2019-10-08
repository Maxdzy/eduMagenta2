<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Model;

use Magento\Framework\Model\AbstractModel;
use Scandiweb\StoreFinder\Model\ResourceModel\Rsvp as RsvpResource;

/**
 * @method int getRsvpId()
 * @method string getFirstname()
 * @method Rsvp setFirstname($value)
 * @method string getLastname()
 * @method Rsvp setLastname($value)
 * @method string getEmail()
 * @method Rsvp setEmail(string $value)
 * @method int getTimeCreated()
 * @method int getTimeUpdated()
 * @method int getEventId()
 * @method Rsvp setEventId(int $value)
 * @method int getStoreId()
 * @method Rsvp setStoreId(int $value)
 */
class Rsvp extends AbstractModel
{
    const COLUMN_RSVP_ID = 'rsvp_id';
    const COLUMN_FIRSTNAME = 'firstname';
    const COLUMN_LASTNAME = 'lastname';
    const COLUMN_EMAIL = 'email';
    const COLUMN_TIME_CREATED = 'created_at';
    const COLUMN_TIME_UPDATED = 'updated_at';
    const COLUMN_EVENT_ID = 'event_id';
    const COLUMN_STORE_ID = 'store_id';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(RsvpResource::class);
    }
}
