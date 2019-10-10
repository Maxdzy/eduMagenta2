<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Edu\StoreFinderCompletion\Helper\Data;
use Magento\Framework\View\Asset\File\NotFoundException;
use Magento\Framework\View\Asset\Repository as AssetRepository;

class Gmap extends Template
{
    const TEMPLATE_PATH_INFO_WINDOW = 'Edu_StoreFinderCompletion::template/storefindercompletion/infowindow.html';

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
        return $this->getViewFileUrl('Edu_StoreFinderCompletion::images/storefinder-map-marker.png');
    }

    /**
     * @param string $image
     * @return string
     */
    public function getMarkerClustererIcon(string $image)
    {
        return $this->getViewFileUrl('Edu_StoreFinderCompletion::images/markerclusters/' . $image);
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
