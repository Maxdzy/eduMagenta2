<?php

namespace Edu\CmsSimpleBadge\Block\Product\View\Gallery;

use Magento\Catalog\Block\Product\View\Gallery as ParentGallery;

/**
 * Product gallery block
 *
 * @api
 */
class Gallery extends ParentGallery
{
    public function hi()
    {
        return "hi hi hi";

        $result = explode(',', $array);
        $text="";
        foreach ($result as $item) {
            $text.=$item . ' SKA ';
        }
        return $text;
    }
}
