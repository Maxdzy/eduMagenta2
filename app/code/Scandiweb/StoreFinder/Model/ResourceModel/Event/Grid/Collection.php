<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Andris Bremanis <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

namespace Scandiweb\StoreFinder\Model\ResourceModel\Event\Grid;

use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Scandiweb\StoreFinder\Model\ResourceModel\Event as EventResource;
use Magento\Framework\Api\Search\SearchResultInterface;
use Scandiweb\StoreFinder\Model\ResourceModel\Event\Collection as EventCollection;

class Collection extends EventCollection implements SearchResultInterface
{
    const CLASS_DATA_PROVIDER_DOCUMENT = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document';

    /**
     * @var AggregationInterface
     */
    protected $aggregations;

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(static::CLASS_DATA_PROVIDER_DOCUMENT, EventResource::class);
    }

    /**
     * Set items list.
     *
     * @param DocumentInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null)
    {
        $this->getItems();

        return $this;
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;

        return $this;
    }

    /**
     * Get search criteria.
     *
     * @return SearchCriteriaInterface
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria)
    {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }
}
