<?php
/**
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Model\Config\Source;

use Edu\CmsSimpleBadge\Model\BadgesFactory;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class BadgesSelect
 * @package Edu\CmsSimpleBadge\Model\Config\Source
 */
class BadgesSelect extends AbstractSource
{
    /**
     * Badges repository
     *
     * @var BadgesFactory
     */
    protected $badges;

    /**
     * @var array
     */
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
    }

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions(): array
    {
        return $this->toOptionArray();
    }

    /**
     * @return array
     */
    final public function toOptionArray(): array
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
