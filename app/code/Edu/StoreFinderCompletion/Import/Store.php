<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>
 */

namespace Edu\StoreFinderCompletion\Import;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Edu\StoreFinderCompletion\Model\StoreFactory;
use Edu\StoreFinderCompletion\Api\StoreRepositoryInterface;
use Edu\StoreFinderCompletion\Model\StoreRepository;
use Edu\StoreFinderCompletion\Api\Import\SourceInterface;
use Edu\StoreFinderCompletion\Api\Import\Store\ValidationInterface;
use Edu\StoreFinderCompletion\Api\Import\StoreInterface;

class Store implements StoreInterface
{
    /**
     * @var Source
     */
    protected $source;

    /**
     * @var Validation
     */
    protected $validation;

    /**
     * @var StoreFactory
     */
    protected $storeFactory;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var Output
     */
    protected $output;

    /**
     * Store constructor.
     * @param Source $source
     */
    public function __construct(
        SourceInterface $source,
        ValidationInterface $validation,
        StoreFactory $storeFactory,
        StoreRepositoryInterface $storeRepository,
        Output $output
    ) {
        $this->source = $source;
        $this->validation = $validation;
        $this->storeFactory = $storeFactory;
        $this->storeRepository = $storeRepository;
        $this->output = $output;
    }

    /**
     * @return void
     */
    public function process()
    {
        try {
            foreach ($this->source->retreiveData() as $store) {
                $this->validation->performCheck($store);
                $this->saveStore($store);
            }

            $this->output->info('Import finished succesfully');
        } catch (LocalizedException $exception) {
            $this->output->warn($exception->getMessage(), ['data' => $store]);
        }
    }

    /**
     * @param array $store
     * @return void
     */
    protected function saveStore(array $store)
    {
        try {
            if (empty($store['identifier'])) {
                throw new NotFoundException(__('Id was not found in given data'));
            }

            $storeModel = $this->storeRepository->getByIdentifier($store['identifier']);
            $storeModel->addData($store);
        } catch (NotFoundException $exception) {
            $storeModel = $this->storeFactory->create(['data' => $store]);
            $storeModel->setHasDataChanges(true);
        }

        try {
            $this->storeRepository->save($storeModel);
        } catch (CouldNotSaveException $exception) {
            $this->output->warn($exception->getMessage(), ['store' => $store]);
        }
    }
}
