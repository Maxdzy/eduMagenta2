<?php
/**
 * @category    Edu
 * @package     Edu\CmsSimpleBadge
 * @author      Maxim Dzyuba
 */

namespace Edu\CmsSimpleBadge\Block\Product\View;

use Edu\CmsSimpleBadge\Model\BadgesFactory;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View\Gallery as ParentGallery;
use Magento\Catalog\Model\Product\Gallery\ImagesConfigFactoryInterface;
use Magento\Catalog\Model\Product\Image\UrlBuilder;
use Magento\Framework\Exception\LocalizedException;
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
     * Badges repository
     *
     * @var BadgesFactory
     */
    protected $badges;

    public function __construct(
        BadgesFactory $badges,
        Context $context,
        ArrayUtils $arrayUtils,
        EncoderInterface $jsonEncoder,
        array $data = [],
        ImagesConfigFactoryInterface $imagesConfigFactory = null,
        array $galleryImagesConfig = [],
        UrlBuilder $urlBuilder = null
    ) {
        $this->badges = $badges;
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
