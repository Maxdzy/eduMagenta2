<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>
 */

namespace Edu\StoreFinderCompletion\Import;

use Symfony\Component\Console\Output\OutputInterface as ConsoleOutputInterface;
use Edu\StoreFinderCompletion\Api\Import\OutputInterface as ImportOutputInterface;

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
