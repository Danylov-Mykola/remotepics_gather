<?php
/**
 * This file is a part of hexa.my project.
 * Author: Mykola Danylov (n.danylov@gmail.com)
 * Date: 20.10.2016
 * Time: 20:08
 */

namespace MykolaDanylov\LocalStorage;

/**
 * Class LocalStorage
 * @package RemotePics
 * Represents functionality to store raw data information into local files
 */
class LocalStorage
{
    /** Where the root directory of local storage */
    const DEFAULT_STORAGE_PATH   = '/assets/storage/';
    private $storagePath         = self::DEFAULT_STORAGE_PATH;
    /** @var int length of storage path in characters number */
    private $storagePathLen;
    /** @var array|null store files with the certain extensions only */
    private $availableExtensions = ['.jpg', '.png', '.gif'];
    /** @var string system path to site root */
    private $appRoot;

    /**
     * @param string|null $storagePath Where the root directory of local storage
     * @param null|array $availableExtensions - store files with the certain extensions only
     * @throws LocalStorageException
     */
    function __construct($storagePath = null, $availableExtensions = null)
    {
        $this->appRoot = $_SERVER["DOCUMENT_ROOT"];
        if (!is_null($storagePath)) {
            $this->storagePath = $storagePath;
        }
        $this->storagePathLen = strlen($this->storagePath) + 1;
        if (!is_null($availableExtensions)) {
            $this->availableExtensions = $availableExtensions;
        }
        if (is_file($this->storagePath)) {
            throw new LocalStorageException(
                '>>ERROR:: Can not create directory for storage. File with same name "'
                . $this->storagePath . '" already exists.',
                [':method' => __FUNCTION__, ':class' => __CLASS__, ':fileSystem'=> $this->storagePath],
                LocalStorageException::E_SERIOUS
            );
        }
        if (!file_exists($this->storagePath)) {
            mkdir($this->storagePath, 0777, true);
        }
        if (!chmod($this->storagePath, 0777)) {
            throw new LocalStorageException(
                '>>ERROR:: Can not make storage writable.',
                [':method' => __FUNCTION__, ':class' => __CLASS__, ':fileSystem' => $this->storagePath],
                LocalStorageException::E_SERIOUS
            );
        };
    }

    /**
     * @author Mykola Danylov (n.danylov@gmail.com)
     * Returns file existent flag relative to local storage root
     * @param $subPath
     * @return bool
     */
    public function isPathExists($subPath)
    {
        return file_exists($this->appRoot . $this->storagePath . $subPath);
    }


    /**
     * @author Mykola Danylov (n.danylov@gmail.com)
     * Returns files files list of the first after the root level of a local storage.
     *      All of files recursively.
     * @param string $stalkFolderName dir name in the root dir of local storage
     * @param null|string $subPath it can be used for get files from deeper folder only, non-recursive
     * @return array of strings - files list
     * @throws LocalStorageException
     */
    public function getPicturesFilesListByStalk($stalkFolderName, $subPath = null)
    {
        $dir = $this->appRoot . $this->storagePath . $stalkFolderName . $subPath;
        $storagePathLen = strlen($dir) + 1;
        $resultArr = [];

        try {
            if (is_null($subPath)) {
                $dirtyFilesList = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
            } else {
                $dirtyFilesList = new \IteratorIterator(new \RecursiveDirectoryIterator($dir));
            }
        } catch (\Exception $e) {
            throw new LocalStorageException(
                '>>ERROR:: ' . $e->getMessage(),
                [':method' => __FUNCTION__, ':class' => __CLASS__, ':fileSystem' => $dir],
                LocalStorageException::E_SERIOUS
            );
        }
        foreach ($dirtyFilesList as $path => $object) {
            if (is_file($path)) {
                foreach ($this->availableExtensions as $extension) {
                    $realFileExtension = substr($path, -strlen($extension));
                    if ($extension === $realFileExtension) {
                        $subpath = substr($path, $storagePathLen);
                        $resultArr[$this->storagePath . $stalkFolderName . '/' . $subpath] = $subpath;
                        break;
                    }
                }
            }
        }

        return $resultArr;
    }

    /**
     * @author Mykola Danylov (n.danylov@gmail.com)
     * @param FileToStoreType $fileObj - object with file data and file name
     * @param string $subPath - where to store, path to local storage relatively
     * @throws LocalStorageException
     */
    public function store(FileToStoreType $fileObj, $subPath)
    {
        $path = $this->appRoot . $this->storagePath . $subPath;
        $fullFilePath = $path . '/' . ($fileName = $fileObj->getFileName());
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        if (file_put_contents($fullFilePath, $fileObj->getFileContents()) === false){
            throw new LocalStorageException(
                '>>ERROR:: ' . 'Can not save file "' . $fullFilePath . '" to the local storage.',
                [':method' => __FUNCTION__, ':class' => __CLASS__, ':fileSystem' => $fullFilePath],
                LocalStorageException::E_SERIOUS
            );
        };
    }
}













