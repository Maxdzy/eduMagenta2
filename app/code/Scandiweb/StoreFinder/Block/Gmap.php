<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Scandiweb\StoreFinder\Helper\Data;
use Magento\Framework\View\Asset\File\NotFoundException;
use Magento\Framework\View\Asset\Repository as AssetRepository;

class Gmap extends Template
{
    const TEMPLATE_PATH_INFO_WINDOW = 'Scandiweb_StoreFinder::template/storefinder/infowindow.html';

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var AssetRepository
     */
    protected $assetRepository;

    /**
     * Gmap constructor.
     * @param Context $context
     * @param Data $dataHelper
     * @param AssetRepository $assetRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $dataHelper,
        AssetRepository $assetRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
        $this->assetRepository = $assetRepository;
    }

    /**
     * @return string
     */
    public function getGoogleApiKey()
    {
        return $this->dataHelper->getGoogleMapsApiKey();
    }

    /**
     * @return string
     */
    public function getMapMarkerIcon()
    {
        return $this->getViewFileUrl('Scandiweb_StoreFinder::images/storefinder-map-marker.png');
    }

    /**
     * @param string $image
     * @return string
     */
    public function getMarkerClustererIcon(string $image)
    {
        return $this->getViewFileUrl('Scandiweb_StoreFinder::images/markerclusters/' . $image);
    }

    /**
     * @return string
     */
    public function getInfoWindowTemplate()
    {
        $template = $this->assetRepository->createAsset(static::TEMPLATE_PATH_INFO_WINDOW, ['area' => 'frontend']);
        try {
            return preg_replace('/<!--.*-->/s', '', $template->getContent());
        } catch (NotFoundException $exception) {
            return '';
        }
    }
}
