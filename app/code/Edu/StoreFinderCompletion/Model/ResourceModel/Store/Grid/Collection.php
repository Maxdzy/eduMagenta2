<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Model\ResourceModel\Store\Grid;

use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Edu\StoreFinderCompletion\Model\ResourceModel\Store\Collection as StoreCollection;
use Magento\Framework\Api\Search\SearchResultInterface;
use Edu\StoreFinderCompletion\Model\ResourceModel\Store as StoreResource;

class Collection extends StoreCollection implements SearchResultInterface
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
        $this->_init(static::CLASS_DATA_PROVIDER_DOCUMENT, StoreResource::class);
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
