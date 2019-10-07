<?php

namespace Edu\CmsSimpleBadge\Block\Product\ProductList\Item;

use Edu\CmsSimpleBadge\Model\BadgesFactory;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Block
 * @package Edu\CmsSimpleBadge\Block\Product\ProductList\Item
 */
class Block extends \Magento\Catalog\Block\Product\ProductList\Item\Block
{
    /**
     * Badges repository
     *
     * @var BadgesFactory
     */
    protected $badges;

    /**
     * Block constructor.
     * @param BadgesFactory $badges
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        BadgesFactory $badges,
        Context $context,
        array $data = []
    ) {
        $this->badges = $badges;
        parent::__construct($context, $data);
    }

    /**
     * @param null $badgeIdList
     * @return string
     */
    public function renderBadge($badgeIdList = null)
    {
        $result = "";
        if (isset($badgeIdList)) {
            $badgesId = explode(',', $badgeIdList);
            foreach ($badgesId as $id) {
                $badge = $this->badges->create()->load($id);
                try {
                    if ($badge->getStatus() && $id != 0) {
                        $result .= "<img src='{$badge->getImageUrl()}' 
                                    data_badgeId='{$id}'
                                    alt='{$badge->getName()}'
                                    class='product_badge' />";
                    }
                } catch (LocalizedException $e) {
                    echo "error";
                } catch (\Exception $e) {
                    echo "error getImageUrl";
                }
            }
        }
        return $result;
    }
}
