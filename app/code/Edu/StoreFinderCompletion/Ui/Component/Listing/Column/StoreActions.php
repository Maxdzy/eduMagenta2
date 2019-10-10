<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Edu\StoreFinderCompletion\Model\Store as StoreModel;

class StoreActions extends Column
{
    const URL_PATH_EDIT = 'storefindercompletion/stores/edit';
    const URL_PATH_DELETE = 'storefindercompletion/stores/delete';

    /**
     * @var UrlInterface $urlBuilder
     */
    protected $urlBuilder;

    /**
     * @param UrlInterface $urlBuilder
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[StoreModel::COLUMN_STORE_ID])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    StoreModel::COLUMN_STORE_ID => $item[StoreModel::COLUMN_STORE_ID]
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    StoreModel::COLUMN_STORE_ID => $item[StoreModel::COLUMN_STORE_ID]
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete "${ $.$data.store_name }"'),
                                'message' => __('Are you sure you want to delete store "${ $.$data.store_name }" ?')
                            ]
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
