<?php
/**
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Ui\DataProvider\Badges\Form\Modifier;

use Edu\CmsSimpleBadge\Model\Badges;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Edu\CmsSimpleBadge\Model\ResourceModel\Badges\CollectionFactory;
use Edu\CmsSimpleBadge\Model\ResourceModel\Badges\Collection;

/**
 * Class BadgesData
 * @package Edu\CmsSimpleBadge\Ui\DataProvider\Badges\Form\Modifier
 */
class BadgesData implements ModifierInterface
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @param CollectionFactory $badgesCollectionFactory
     */
    public function __construct(
        CollectionFactory $badgesCollectionFactory
    ) {
        $this->collection = $badgesCollectionFactory->create();
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * @param array $data
     * @return array|mixed
     * @throws LocalizedException
     */
    public function modifyData(array $data)
    {
        $badges = $this->collection->getItems();
        /** @var $badge Badges */
        foreach ($badges as $badge) {
            $_data = $badge->getData();
            if (isset($_data['badge_id'])) {
                $badgeArr = [];
                $badgeArr[0]['name'] = $_data['name'];
                $badgeArr[0]['image_url'] = $badge->getImageUrl();
                $_data['image_url'] = $badge->getImageUrl(); //$badgeArr;
            }
            $badge->setData($_data);
            $data[$badge->getId()] = $_data;
        }
        return $data;
    }
}
