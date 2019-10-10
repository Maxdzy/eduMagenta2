<?php
/**
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Block\Product\ProductList;

use Edu\CmsSimpleBadge\Block\Frontend\Badges\RenderFactory as BadgesRender;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Checkout\Model\ResourceModel\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\Manager;

/**
 * Class Related
 * @package Edu\CmsSimpleBadge\Block\Product\ProductList
 */
class Related extends \Magento\Catalog\Block\Product\ProductList\Related
{

    /**
     * @var BadgesRender
     */
    protected $badgesRender;

    /**
     * Related constructor.
     * @param BadgesRender $badgesRender
     * @param Context $context
     * @param Cart $checkoutCart
     * @param Visibility $catalogProductVisibility
     * @param Session $checkoutSession
     * @param Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        BadgesRender $badgesRender,
        Context $context,
        Cart $checkoutCart,
        Visibility $catalogProductVisibility,
        Session $checkoutSession,
        Manager $moduleManager,
        array $data = []
    ) {
        $this->badgesRender = $badgesRender;
        parent::__construct(
            $context,
            $checkoutCart,
            $catalogProductVisibility,
            $checkoutSession,
            $moduleManager,
            $data
        );
    }

    /**
     * @param null $badgeIdList
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getBadge($badgeIdList = null)
    {
        return $this->badgesRender->create()->renderBadges($badgeIdList);
    }
}
