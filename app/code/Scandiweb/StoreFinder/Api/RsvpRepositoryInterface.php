<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Api;

use Magento\Framework\Exception\NotFoundException;
use Scandiweb\StoreFinder\Model\Rsvp;

interface RsvpRepositoryInterface
{
    /**
     * @param int $rsvpId
     * @return Rsvp
     */
    public function getById($rsvpId);

    /**
     * @param Rsvp $rsvp
     * @return Rsvp
     */
    public function save(Rsvp $rsvp);

    /**
     * @param Rsvp $rsvp
     * @throws NotFoundException
     */
    public function delete(Rsvp $rsvp);

    /**
     * @return Rsvp
     */
    public function create();
}
