<?php
/**
 * This file is a part of remotepix_gather.package project.
 * Author: Mykola Danylov (n.danylov@gmail.com)
 * Date: 20.10.2016
 * Time: 20:08
 */

namespace MykolaDanylov\RemotePics;

use MykolaDanylov\LocalStorage\LocalStorage as LocalStorage;
use MykolaDanylov\LocalStorage\WebPicture as WebPicture;

/**
 * Class OneHtmlPage
 * @package RemotePics
 * Manipulations with separated web-page
 */
class OneHtmlPage
{
    /** @var string site url address */
    private $siteAddress;
    /** @var string local storage path */
    private $storagePath;
    /** @var string  site page path relative to site domain */
    private $pagePath;
    /** @var array of strings ".jpg" ... */
    private $availableExtensions = [];
    /** @var  LocalStorage object */
    private $localStorageObj;

    /**
     * @param WebSitePics $webPagePicsObj - object with web site data
     * @param $pagePath - site page path relative to site domain
     * @param $availableExtensions array of strings ".jpg" ...
     * @throws RemotePicException
     * @throws \Exception
     */
    function __construct(WebSitePics $webPagePicsObj, $pagePath, $availableExtensions)
    {
        $this->availableExtensions = $availableExtensions;
        $this->pagePath = $pagePath;
        $this->siteAddress = $webPagePicsObj->getSiteDomain();
        $this->storagePath = $webPagePicsObj->getStoragePath();
        try {
            $this->localStorageObj = new LocalStorage($this->storagePath, $availableExtensions);
        } catch (RemotePicException $e) {
            throw $e;
        }
        try {
            $this->crawlPagePics();
        } catch (RemotePicException $e) {
            throw $e;
        }
    }


    /**
     * @author Mykola Danylov (n.danylov@gmail.com)
     * Get and store all detected pictures via links on the page
     * @throws \Exception
     */
    private function crawlPagePics()
    {
        try {
            $remotePicturesList = self::parsePageForPics($this->siteAddress . $this->pagePath, $this->availableExtensions);
        } catch (RemotePicException $e) {
            throw $e;
        }
        foreach ($remotePicturesList as $remoteUrl) {
            try {
                $webPicture = new WebPicture($remoteUrl);
            } catch  (RemotePicException $e) {
                throw $e;
            }
            $this->localStorageObj->store(
                $webPicture,
                $this->siteAddress
                . pathinfo(parse_url($remoteUrl, PHP_URL_PATH), PATHINFO_DIRNAME)
            );
        }

    }

    /**
     * @author Mykola Danylov (n.danylov@gmail.com)
     * Parsing web page for picture lincs
     * @param $url - web page url
     * @param array $availableExtensions - array of strings ".jpg" ...
     * @return array of strings - list of web links to pictures
     * @throws RemotePicException
     * @throws \Exception
     */
    public static function parsePageForPics($url, array $availableExtensions)
    {
        /**There we get html and parse for pictures */
        $regExp = '~\<img[^>]*\ssrc\s*=\s*(\'|")(.+?)\1~';
        $httpAddr = 'http://' . $url;
        $domain = parse_url($httpAddr, PHP_URL_HOST);
        try {
            /**@todo : $htmlPage might have not been defined! */
            $htmlPage = Helper::getContents('http://' . $url);
        } catch (RemotePicException $e) {
            throw $e;
        }

        $picturesListFromPage = [];
        $parseResult = preg_match_all($regExp, $htmlPage, $picturesListFromPage);
        if ($parseResult === false) {
            throw new RemotePicException(
                '>>ERROR:: Page parsing error.',
                [':method' => __FUNCTION__, ':class' => __CLASS__],
                RemotePicException::E_SERIOUS
            );
        }
        $picturesListFromPage = $picturesListFromPage[2];
        $resultPictureList = [];
        foreach ($picturesListFromPage as $picturePath) {
            $picturePath = str_replace(['../', './'], '', $picturePath, $replacedCount);

            $extensionNotAllowed = true;
            foreach ($availableExtensions as $extension) {
                $realFileExtension = substr($picturePath, -strlen($extension));
                if ($extension === $realFileExtension) {
                    $extensionNotAllowed = false;
                    break;
                }
            }
            if (
                substr($picturePath, 0, 4) === 'http'
                AND substr($picturePath, 7, strlen($url)) !== $url
                OR
                substr($picturePath, 0, 2) === '//'
                AND substr($picturePath, 2, strlen($url)) !== $url
                OR
                $extensionNotAllowed
            ) {
                continue;
            }
            $resultPictureList[] = 'http://' . $domain . '/' . $picturePath;
        }
        return $resultPictureList;
    }
}

























