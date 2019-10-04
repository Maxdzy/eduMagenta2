<?php


namespace Edu\CmsSimpleBadge\Block\Product\ProductList;


use Edu\CmsSimpleBadge\Model\BadgesFactory;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Checkout\Model\ResourceModel\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\Manager;

class Related extends \Magento\Catalog\Block\Product\ProductList\Related
{
    /**
     * Badges repository
     *
     * @var BadgesFactory
     */
    protected $badges;

    /**
     * @param Context $context
     * @param Cart $checkoutCart
     * @param Visibility $catalogProductVisibility
     * @param Session $checkoutSession
     * @param Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        BadgesFactory $badges,
        Context $context,
        Cart $checkoutCart,
        Visibility $catalogProductVisibility,
        Session $checkoutSession,
        Manager $moduleManager,
        array $data = []
    ) {
        $this->badges = $badges;
        parent::__construct(
            $context,
            $checkoutCart,
            $catalogProductVisibility,
            $checkoutSession,
            $moduleManager,
            $data
        );
    }


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
