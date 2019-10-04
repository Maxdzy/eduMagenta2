<?php
/**
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Block\Product;

//  Magento\CatalogWidget\Block\Product;

use Edu\CmsSimpleBadge\Model\BadgesFactory;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogWidget\Model\Rule;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Rule\Model\Condition\Sql\Builder;
use Magento\Widget\Helper\Conditions;

/**
 * Catalog Products List widget block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class ProductsList extends \Magento\CatalogWidget\Block\Product\ProductsList
{
    /**
     * Badges repository
     *
     * @var BadgesFactory
     */
    protected $badges;
    /**
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param Builder $sqlBuilder
     * @param Rule $rule
     * @param Conditions $conditionsHelper
     * @param array $data
     * @param Json|null $json
     * @param LayoutFactory|null $layoutFactory
     * @param \Magento\Framework\Url\EncoderInterface|null $urlEncoder
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        BadgesFactory $badges,
        Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        Builder $sqlBuilder,
        Rule $rule,
        Conditions $conditionsHelper,
        array $data = [],
        Json $json = null,
        LayoutFactory $layoutFactory = null,
        EncoderInterface $urlEncoder = null
    ) {
        $this->badges = $badges;
        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $data,
            $json,
            $layoutFactory,
            $urlEncoder
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
