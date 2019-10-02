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
    )
    {
        $this->badges = $badges;
    }

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->options === null) {
            $badges = $this->badges->create()->getCollection();
            $this->options = [];
            foreach ($badges as $item) {
                if ($item->getData()['status'] == 1) {
                    $this->options[] = [
                        'value' => $item->getData()['badge_id'],
                        'label' => __($item->getData()['name'])
                    ];
                }
            }
        }

        return $this->options;
    }

    final public function toOptionArray()
    {
        $badges = $this->badges->create()->getCollection();
        $this->options = [];
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
