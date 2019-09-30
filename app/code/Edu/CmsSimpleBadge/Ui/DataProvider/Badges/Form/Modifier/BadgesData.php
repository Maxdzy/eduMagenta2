<?php
/**
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */
namespace Edu\CmsSimpleBadge\Ui\DataProvider\Badges\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Edu\CmsSimpleBadge\Model\ResourceModel\Badges\CollectionFactory;
use Edu\CmsSimpleBadge\Model\ResourceModel\Badges\Collection;

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
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function modifyData(array $data)
    {
        $items = $this->collection->getItems();
        /** @var $badges \Edu\CmsSimpleBadge\Model\Badges */
        foreach ($items as $badges) {
            $_data = $badges->getData();
            if (isset($_data['badges'])) {
                $badgesArr = [];
                $badgesArr[0]['name'] = 'Image';
                $badgesArr[0]['url'] = $badges->getImageUrl();
                $_data['badges'] = $badgesArr;
            }
            $badges->setData($_data);
            $data[$badges->getId()] = $_data;
        }
        return $data;
    }
}
