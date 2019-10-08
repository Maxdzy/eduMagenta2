<?php
/**
 * @category    Scandiweb
 * @package     Scandiweb/StoreFinder
 * @author      Maris Mols <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Inc (https://scandiweb.com)
 */

namespace Scandiweb\StoreFinder\Api\Import;

use Symfony\Component\Console\Output\OutputInterface as ConsoleOutputInterface;

interface OutputInterface
{
    /**
     * OuputInterface constructor.
     * @param ConsoleOutputInterface|null $consoleOuput
     */
    public function __construct(ConsoleOutputInterface $consoleOuput = null);

    /**
     * @param $message
     * @param array $context
     * @return mixed
     */
    public function warn($message, array $context = []);

    /**
     * @param $message
     * @param array $context
     * @return mixed
     */
    public function info($message, array $context = []);
}