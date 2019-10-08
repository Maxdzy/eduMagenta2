<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Maris Mols <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 */

namespace Scandiweb\StoreFinder\Console\Command;

use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Scandiweb\StoreFinder\Api\Import\SourceInterface;
use Scandiweb\StoreFinder\Api\Import\StoreInterfaceFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Scandiweb\StoreFinder\Import\Source\File\CsvFactory;
use Scandiweb\StoreFinder\Api\Import\OutputInterfaceFactory as ImportOutputInterfaceFactory;
use Scandiweb\StoreFinder\Api\Import\OutputInterface as ImportOutputInterface;

class StoreImportCommand extends Command
{
    /**
     * @var CsvFactory
     */
    protected $csvFactory;

    /**
     * @var ImportOutputInterfaceFactory
     */
    protected $outputInterfaceFactory;

    /**
     * @var StoreInterface
     */
    protected $storeImportFactory;

    /**
     * StoreImportCommand constructor.
     * @param CsvFactory $csvFactory
     * @param ImportOutputInterfaceFactory $outputInterfaceFactory
     */
    public function __construct(
        CsvFactory $csvFactory,
        ImportOutputInterfaceFactory $outputInterfaceFactory,
        StoreInterfaceFactory $storeImportFactory,
        State $state
    ) {
        parent::__construct();
        $this->csvFactory = $csvFactory;
        $this->outputInterfaceFactory = $outputInterfaceFactory;
        $this->storeImportFactory = $storeImportFactory;
        $this->state = $state;
    }

    /**
     * @return void
     */
    public function configure()
    {
        $this->setName('scandiweb:storefinder:store:import')
            ->setDescription('Import storefinder store from give source file')
            ->addOption(
                'source-file',
                null,
                InputArgument::OPTIONAL,
                'Provide source file path from magento ROOT (For now only CSV supported)'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->getAreaCode();
        } catch (LocalizedException $exception) {
            $this->state->setAreaCode('adminhtml');
        }

        /** @var SourceInterface $csvSource */
        $csvSource = $this->csvFactory->create(['filePath' => $input->getOption('source-file')]);
        /** @var ImportOutputInterface $importOutput */
        $importOutput = $this->outputInterfaceFactory->create(['consoleOutput' => $output]);

        $this->storeImportFactory->create([
            'source' => $csvSource,
            'output' => $importOutput
        ])->process();
    }
}
