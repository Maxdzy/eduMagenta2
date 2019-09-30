<?php
/**
 * Edu_CmsSimpleBadge Status Options Model.
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;
use Edu\CmsSimpleBadge\Api\BadgesRepositoryInterface;
use Edu\CmsSimpleBadge\Api\Data\BadgesInterface;
use Edu\CmsSimpleBadge\Api\Data\BadgesInterfaceFactory;
use Edu\CmsSimpleBadge\Model\ResourceModel\Badges as ResourceBadges;
use Edu\CmsSimpleBadge\Model\ResourceModel\Badges\CollectionFactory as BadgesCollectionFactory;

class BadgesRepository implements BadgesRepositoryInterface
{
    /**
     * @var array
     */
    protected $instances = [];
    /**
     * @var ResourceBadges
     */
    protected $resource;

    /**
     * @var BadgesCollectionFactory
     */
    protected $badgesCollectionFactory;

    /**
     * @var BadgesInterfaceFactory
     */
    protected $badgesInterfaceFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    public function __construct(
        ResourceBadges $resource,
        BadgesCollectionFactory $badgesCollectionFactory,
        BadgesInterfaceFactory $badgesInterfaceFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource = $resource;
        $this->badgesCollectionFactory = $badgesCollectionFactory;
        $this->badgesInterfaceFactory = $badgesInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @param BadgesInterface $badges
     * @return BadgesInterface
     * @throws CouldNotSaveException
     */
    public function save(BadgesInterface $badges)
    {
        try {
            /** @var BadgesInterface|\Magento\Framework\Model\AbstractModel $badges */
            $this->resource->save($badges);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the badges: %1',
                $exception->getMessage()
            ));
        }
        return $badges;
    }

    /**
     * Get badges record
     *
     * @param $badgesId
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getBadgeId($badgesId)
    {
        if (!isset($this->instances[$badgesId])) {
            $badges = $this->badgesInterfaceFactory->create();
            $this->resource->load($badges, $badgesId);
            if (!$badges->getId()) {
                throw new NoSuchEntityException(__('Requested badges doesn\'t exist'));
            }
            $this->instances[$badgesId] = $badges;
        }
        return $this->instances[$badgesId];
    }

    /**
     * Get image record
     *
     * @param $badgesId
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($badgeId)
    {
        if (!isset($this->instances[$badgeId])) {
            $badges = $this->imageInterfaceFactory->create();
            $this->resource->load($badges, $badgeId);
            if (!$badges->getId()) {
                throw new NoSuchEntityException(__('Requested image doesn\'t exist'));
            }
            $this->instances[$badgeId] = $badges;
        }
        return $this->instances[$badgeId];
    }

    /**
     * @param BadgesInterface $badges
     * @return bool
     * @throws CouldNotSaveException
     * @throws StateException
     */
    public function delete(BadgesInterface $badges)
    {
        /** @var \Edu\CmsSimpleBadge\Api\Data\BadgesInterface|\Magento\Framework\Model\AbstractModel $badges */
        $id = $badges->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($badges);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove badges %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * @param $badgesId
     * @return bool
     */
    public function deleteById($badgesId)
    {
        $badges = $this->getBadgeId($badgesId);
        return $this->delete($badges);
    }
}
