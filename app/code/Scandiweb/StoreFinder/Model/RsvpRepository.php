<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NotFoundException;
use Scandiweb\StoreFinder\Api\RsvpRepositoryInterface;
use Scandiweb\StoreFinder\Model\ResourceModel\Rsvp as RsvpResource;
use Scandiweb\StoreFinder\Model\Rsvp as RsvpModel;
use Scandiweb\StoreFinder\Model\RsvpFactory as RsvpModelFactory;
use Throwable;

class RsvpRepository implements RsvpRepositoryInterface
{
    /**
     * @var RsvpResource
     */
    protected $rsvpResource;

    /**
     * @var RsvpFactory
     */
    protected $rsvpFactory;

    /**
     * RsvpRepository constructor.
     * @param RsvpResource $rsvpResource
     * @param RsvpFactory $rsvpFactory
     */
    public function __construct(
        RsvpResource $rsvpResource,
        RsvpModelFactory $rsvpFactory
    ) {
        $this->rsvpResource = $rsvpResource;
        $this->rsvpFactory = $rsvpFactory;
    }

    /**
     * @param int $rsvpId
     * @return RsvpModel
     * @throws NotFoundException
     */
    public function getById($rsvpId)
    {
        $rsvp = $this->create();
        $this->rsvpResource->load($rsvp, $rsvpId);

        if (!$rsvp->getId()) {
            throw new NotFoundException(__('Rsvp with ID %1 does not exist.', $rsvpId));
        }

        return $rsvp;
    }

    /**
     * @param RsvpModel $rsvp
     * @return RsvpModel
     * @throws CouldNotSaveException
     */
    public function save(RsvpModel $rsvp)
    {
        try {
            $this->rsvpResource->save($rsvp);
        } catch (Throwable $exception) {
            throw new CouldNotSaveException(__('Unable to save rsvp with ID: %s', $rsvp->getId()));
        }

        return $rsvp;
    }

    /**
     * @param RsvpModel $rsvp
     * @throws CouldNotDeleteException
     */
    public function delete(RsvpModel $rsvp)
    {
        try {
            $this->rsvpResource->delete($rsvp);
        } catch (Throwable $exception) {
            throw new CouldNotDeleteException(__('Unable to delete rsvp ID: %s', $rsvp->getId()));
        }
    }

    /**
     * @return RsvpModel
     */
    public function create()
    {
        return $this->rsvpFactory->create();
    }
}
