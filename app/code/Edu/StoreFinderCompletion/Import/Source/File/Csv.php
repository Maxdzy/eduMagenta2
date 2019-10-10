<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>
 */

namespace Edu\StoreFinderCompletion\Import\Source\File;

use Edu\StoreFinderCompletion\Api\Import\SourceInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Csv implements SourceInterface
{
    /**
     * @var Validation
     */
    protected $validation;

    /**
     * @var Filesystem\Directory\ReadInterface
     */
    protected $filesystem;

    /**
     * @var Filesystem\File\ReadInterface
     */
    protected $file;

    /**
     * @var int
     */
    protected $currentRow = 0;

    /**
     * Csv constructor.
     * @param Filesystem $filesystem
     * @param null $filePath
     */
    public function __construct(Filesystem $filesystem, $filePath = null)
    {
        $this->filesystem = $filesystem->getDirectoryRead(DirectoryList::ROOT);
        $this->file = $this->filesystem->openFile($filePath);
    }

    /**
     * @return array
     */
    public function retreiveData()
    {
        $data = [];
        $header = $this->readHeader();
        while ($row = $this->readRow()) {
            $data[] = array_combine($header, $row);
        }

        return $data;
    }

    /**
     * @return mixed
     */
    protected function readRow()
    {
        $line = $this->file->readCsv();

        if ($line) {
            $this->currentRow++;
        }

        return $line;
    }

    /**
     * Reads the header and applies header map
     */
    protected function readHeader()
    {
        return $this->readRow();
    }

}
