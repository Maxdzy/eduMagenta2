<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Model\ResourceModel\Store;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\UrlInterface;

/**
 * Class FileInfo
 *
 * Provides information about requested file
 */
class FileInfo
{
    /**
     * Path in /pub/media directory
     */
    const ENTITY_MEDIA_PATH = '/store_finder/stores';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Mime
     */
    protected $mime;

    /**
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param Filesystem $filesystem
     * @param Mime $mime
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Filesystem $filesystem,
        Mime $mime,
        UrlInterface $urlBuilder
    ) {
        $this->filesystem = $filesystem;
        $this->mime = $mime;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get WriteInterface instance
     *
     * @return WriteInterface
     * @throws FileSystemException
     */
    protected function getMediaDirectory()
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }

        return $this->mediaDirectory;
    }

    /**
     * Retrieve MIME type of requested file
     *
     * @param string $fileName
     * @return string
     * @throws FileSystemException
     */
    public function getMimeType($fileName)
    {
        $filePath = $this->getFilePath($fileName);
        $absoluteFilePath = $this->getMediaDirectory()->getAbsolutePath($filePath);

        return $this->mime->getMimeType($absoluteFilePath);
    }

    /**
     * Get file statistics data
     *
     * @param string $fileName
     * @return array
     * @throws FileSystemException
     */
    public function getStat($fileName)
    {
        $filePath = $this->getFilePath($fileName);

        return $this->getMediaDirectory()->stat($filePath);
    }

    /**
     * @param string $fileName
     * @return int
     * @throws FileSystemException
     */
    public function getSize($fileName)
    {
        $stat = $this->getStat($fileName);

        return isset($stat['size']) ? $stat['size'] : 0;
    }

    /**
     * Check if the file exists
     *
     * @param string $fileName
     * @return bool
     * @throws FileSystemException
     */
    public function isExist($fileName)
    {
        $filePath = $this->getFilePath($fileName);

        return $this->getMediaDirectory()->isExist($filePath);
    }

    /**
     * @param $fileName
     * @return string
     */
    public function getFilePath($fileName)
    {
        return static::ENTITY_MEDIA_PATH . '/' . ltrim($fileName, '/');
    }

    /**
     * @param string $image
     * @return string
     */
    public function getUrl($image)
    {
        return rtrim($this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]), '/')
            . static::ENTITY_MEDIA_PATH
            . '/'
            . ltrim($image, '/');
    }
}
