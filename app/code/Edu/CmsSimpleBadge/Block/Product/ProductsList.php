<?php
/**
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Block\Product;

use Edu\CmsSimpleBadge\Block\Frontend\Badges\RenderFactory as BadgesRender;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogWidget\Model\Rule;
use Magento\Framework\Exception\NoSuchEntityException;
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
     * @var BadgesRender
     */
    protected $badgesRender;

    public function __construct(
        BadgesRender $badgesRender,
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
        $this->badgesRender = $badgesRender;
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
