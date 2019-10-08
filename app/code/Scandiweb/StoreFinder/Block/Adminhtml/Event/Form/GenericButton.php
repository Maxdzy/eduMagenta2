<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Block\Adminhtml\Event\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\UrlInterface;
use Scandiweb\StoreFinder\Api\EventRepositoryInterface;
use Scandiweb\StoreFinder\Model\Event as EventModel;

class GenericButton
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var EventRepositoryInterface
     */
    protected $eventRepository;

    /**
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     * @param EventRepositoryInterface $eventRepository
     */
    public function __construct(
        RequestInterface $request,
        UrlInterface $urlBuilder,
        EventRepositoryInterface $eventRepository
    ) {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->eventRepository = $eventRepository;
    }

    /**
     * Return Event ID
     *
     * @return int|null
     */
    public function getEventId()
    {
        if ($this->storeId === null) {
            try {
                $this->storeId = $this->eventRepository->getById(
                    $this->request->getParam(EventModel::COLUMN_EVENT_ID)
                )->getId();
            } catch (NotFoundException $e) {
                $this->storeId = null;
            }
        }

        return $this->storeId;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
