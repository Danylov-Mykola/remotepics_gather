<?php
/**
 * This file is a part of hexa.my project.
 * Author: Mykola Danylov (n.danylov@gmail.com)
 * Date: 20.10.2016
 * Time: 18:05
 */

namespace MykolaDanylov\RemotePics;

/**
 * Class WebSitePics
 * @package RemotePics
 * Main class of packet functionality. It provides an interface for usage outside packet.
 */
class WebSitePics
{
    /** @var string */
    private $siteDomain;
    /** @var array of Picture -s */
    private $pagesArr = [];
    /** @var string */
    private $storagePath = '/storage/';
    /** @var LocalStorage */
    private $storageObj;
    /** @var array */
    private $extensionsList;

    /**
     * @param string $siteAddress full site address
     * @param array $extensionsList (on example: ['.jpg', '.gif'])
     * @param null|string $storagePath path where files will be stored. Relative to site root.
     */
    function __construct($siteAddress, array $extensionsList, $storagePath = NULL)
    {
        $this->extensionsList = $extensionsList;
        if (!is_null($storagePath)) {
            $this->storagePath = $storagePath;
        }
        $this->siteDomain = parse_url($siteAddress, PHP_URL_HOST);
        $this->storageObj = new LocalStorage($this->storagePath, $this->extensionsList);
    }

    /**
     * @author Mykola Danylov (n.danylov@gmail.com)
     * Prepare and store web pictures to local storage
     * @param string $urlPagePath path to web-page relative to domain. On example: "/sitemap"
     */
    public function preparePagePictures($urlPagePath)
    {
        $this->pagesArr[$urlPagePath] = new OneHtmlPage($this, $urlPagePath, $this->extensionsList);
    }

    /**
     * @author Mykola Danylov (n.danylov@gmail.com)
     * Returns path to local storage
     * @return string $storagePath path where files will be stored. Relative to site root.
     */
    public function getStoragePath()
    {
        return $this->storagePath;
    }

    /**
     * @author Mykola Danylov (n.danylov@gmail.com)
     * Returns web site domain name
     * @return string site domain name which pictures we are storing
     */
    public function getSiteDomain()
    {
        return $this->siteDomain;
    }

    /**
     * @author Mykola Danylov (n.danylov@gmail.com)
     * Returns list of local files
     * @return array of strings - locally stored files
     */
    public function getAllLocalPicsFilesList()
    {
        $filesList = $this->storageObj->getPicturesFilesListByStalk($this->siteDomain);
        return $filesList;
    }
}