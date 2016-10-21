<?php
/**
 * This file is a part of hexa.my project.
 * Author: Mykola Danylov (n.danylov@gmail.com)
 * Date: 20.10.2016
 * Time: 18:05
 */

namespace RemotePics;

class WebSitePics
{
    /** @var string  */
    private $siteDomain;
    /** @var array of Picture -s */
    private $pagesArr = [];
    /** @var string  */
    private $storagePath = '/storage/';
    /** @var LocalStorage  */
    private $storageObj;
    /** @var array  */
    private $extensionsList;

    function __construct ($siteAddress, array $extensionsList, $storagePath = NULL){
        $this->extensionsList = $extensionsList;
        if(!is_null($storagePath)){
            $this->storagePath = $storagePath;
        }
        $this->siteDomain = parse_url($siteAddress, PHP_URL_HOST);
        $this->storageObj = new LocalStorage($this->storagePath, $this->extensionsList);
    }


    public function preparePagePictures($urlPagePath){
        $this->pagesArr[$urlPagePath] = new OneHtmlPage($this, $urlPagePath, $this->extensionsList);
    }

    public function getStoragePath(){
        return $this->storagePath;
    }

    public function getSiteDomain(){
        return $this->siteDomain;
    }

    public function getPreparedLocalPicsFilesList(){
        $filesList = [];
        /** @var OneHtmlPage $pageObj */
        foreach($this->pagesArr as $pageObj){
            $filesList = array_merge($filesList, $pageObj->getLocalPicsList());
        }
        return $filesList;
    }


    public function getAllLocalPicsFilesList(){
        $filesList = $this->storageObj->getPicturesFilesListByStalk($this->siteDomain);
        return $filesList;
    }
}