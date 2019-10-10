<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Api;

use Magento\Framework\Exception\NotFoundException;
use Edu\StoreFinderCompletion\Model\Rsvp;

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
