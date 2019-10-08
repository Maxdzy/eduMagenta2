<?php
/**
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Block\Product\View;

use Edu\CmsSimpleBadge\Block\Frontend\Badges\RenderFactory as BadgesRender;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View\Gallery as ParentGallery;
use Magento\Catalog\Model\Product\Gallery\ImagesConfigFactoryInterface;
use Magento\Catalog\Model\Product\Image\UrlBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Stdlib\ArrayUtils;

/**
 * Product gallery block
 *
 * @api
 */
class Gallery extends ParentGallery
{
    /**
     * @var BadgesRender
     */
    protected $badgesRender;

    /**
     * Gallery constructor.
     * @param BadgesRender $badgesRender
     * @param Context $context
     * @param ArrayUtils $arrayUtils
     * @param EncoderInterface $jsonEncoder
     * @param array $data
     * @param ImagesConfigFactoryInterface|null $imagesConfigFactory
     * @param array $galleryImagesConfig
     * @param UrlBuilder|null $urlBuilder
     */
    public function __construct(
        BadgesRender $badgesRender,
        Context $context,
        ArrayUtils $arrayUtils,
        EncoderInterface $jsonEncoder,
        array $data = [],
        ImagesConfigFactoryInterface $imagesConfigFactory = null,
        array $galleryImagesConfig = [],
        UrlBuilder $urlBuilder = null
    ) {
        $this->badgesRender = $badgesRender;
        parent::__construct(
            $context,
            $arrayUtils,
            $jsonEncoder,
            $data,
            $imagesConfigFactory,
            $galleryImagesConfig,
            $urlBuilder
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
