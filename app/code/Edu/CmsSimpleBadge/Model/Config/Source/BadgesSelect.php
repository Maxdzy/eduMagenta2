<?php

namespace Edu\CmsSimpleBadge\Model\Config\Source;

use Edu\CmsSimpleBadge\Model\BadgesFactory;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class BadgesSelect extends AbstractSource
{
    /**
     * Badges repository
     *
     * @var BadgesFactory
     */
    protected $badges;

    protected $options;

    /**
     * Sliders constructor.
     *
     * @param BadgesFactory $badges
     */
    public function __construct(
        BadgesFactory $badges
    ) {
        $this->badges = $badges;
        $this->options = [];
    }

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

    final public function toOptionArray()
    {
        $badges = $this->badges->create()->getCollection();
        $this->options[] = [
            'value' => 0,
            'label' => __('---- Empty ----')
        ];
        foreach ($badges as $item) {
            if ($item->getData()['status'] == 1) {
                $this->options[] = [
                    'value' => $item->getData()['badge_id'],
                    'label' => __($item->getData()['name'])
                ];
            }
        }

        return $this->options;
    }
}
