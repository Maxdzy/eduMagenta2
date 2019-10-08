<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Maris Mols <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 */

namespace Scandiweb\StoreFinder\Import;

use Symfony\Component\Console\Output\OutputInterface as ConsoleOutputInterface;
use Scandiweb\StoreFinder\Api\Import\OutputInterface as ImportOutputInterface;

class Output implements ImportOutputInterface
{
    /**
     * @var OutputInterface
     */
    protected $consoleOutput;

    /**
     * @param OutputInterface $consoleOuput
     * @return $this
     */
    public function __construct(ConsoleOutputInterface $consoleOutput = null)
    {
        $this->consoleOutput = $consoleOutput;
    }

    /**
     * @param $message
     * @param array $context
     */
    public function warn($message, array $context = [])
    {
        if ($this->consoleOutput instanceof ConsoleOutputInterface) {
            $this->consoleOutput->writeLn(sprintf('[%s]: %s', date('Y-m-d H:i:s'), $message));
        }
    }

    /**
     * @param $message
     * @param array $context
     * @return mixed|void
     */
    public function info($message, array $context = [])
    {
        if ($this->consoleOutput instanceof ConsoleOutputInterface) {
            $this->consoleOutput->writeLn(sprintf('<info>[%s]: %s</info>', date('Y-m-d H:i:s'), $message));
        }
    }
}
