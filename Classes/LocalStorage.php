<?php
/**
 * This file is a part of hexa.my project.
 * Author: Mykola Danylov (n.danylov@gmail.com)
 * Date: 20.10.2016
 * Time: 20:08
 */

namespace RemotePics;


class LocalStorage {
    const DEFAULT_STORAGE_PATH = '/assets/storage/';
    private $storagePath = self::DEFAULT_STORAGE_PATH;
    private $storagePathLen;
    private $availableExtensions = ['.jpg', '.png', '.gif'];
    private $appRoot;

    function __construct($storagePath = NULL, $availableExtensions = NULL){
        $this->appRoot = $_SERVER["DOCUMENT_ROOT"];
        if(!is_null($storagePath)){
            $this->storagePath = $storagePath;
        }
        $this->storagePathLen = strlen($this->storagePath) + 1;
        if(!is_null($availableExtensions)){
            $this->availableExtensions = $availableExtensions;
        }
        if(is_file($this->storagePath)){
            throw new \Exception('Can not create directory for storage. File with same name already exists.');
        }
        if(!file_exists($this->storagePath)){
            mkdir ($this->storagePath, 077, TRUE);
        }
        if(!chmod($this->storagePath, 0777)){
            throw new \Exception('Can not make storage writable.');
        };
    }


    public function isPathExists($subPath){
        return file_exists($this->appRoot . $this->storagePath . $subPath);
    }


    public function getPicturesFilesListByStalk($stalkFolderName, $subPath = NULL){
        $dir = $this->appRoot . $this->storagePath . $stalkFolderName . $subPath;
        $storagePathLen = strlen($dir) + 1;
        $resultArr = [];

        if(is_null($subPath)) {
            $dirtyFilesList = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        } else {
            $dirtyFilesList = new \IteratorIterator(new \RecursiveDirectoryIterator($dir));
        }
        foreach($dirtyFilesList as $path => $object){
            if(is_file($path)){
                foreach($this->availableExtensions as $extension){
                    $realFileExtension = substr($path, -strlen($extension));
                    if($extension === $realFileExtension){
                        $subpath = substr($path, $storagePathLen);
                        $resultArr[$this->storagePath . $stalkFolderName . '/' . $subpath] = $subpath;
                        break;
                    }
                }
            }
        }

        return $resultArr;
    }


    public function store(FileToStoreType $fileObj, $subPath){
        $path = $this->appRoot . $this->storagePath . $subPath;
        $fullFilePath = $path . '/' . ($fileName = $fileObj->getFileName());
        if(!file_exists($path)) {
            mkdir($path, 0777, TRUE);
        }
        file_put_contents($fullFilePath,  $fileObj->getFileContents());
    }
}













