<?php
/**
 * This file is a part of hexa.my project.
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
     */
    function __construct(WebSitePics $webPagePicsObj, $pagePath, $availableExtensions)
    {
        $this->availableExtensions = $availableExtensions;
        $this->pagePath = $pagePath;
        $this->siteAddress = $webPagePicsObj->getSiteDomain();
        $this->storagePath = $webPagePicsObj->getStoragePath();
        $this->localStorageObj = new LocalStorage($this->storagePath, $availableExtensions);
        $this->crawlPagePics();
    }


    /**
     * @author Mykola Danylov (n.danylov@gmail.com)
     * Returns list of files stored recently
     * @return array of locally stored pictures (including path)
     */
    /**@todo refine */
    public function getLocalPicsList()
    {
        return $this->localStorageObj->getPicturesFilesListByStalk($this->siteAddress, $this->pagePath);
    }

    /**
     * @author Mykola Danylov (n.danylov@gmail.com)
     * Get and store all detected pictures via links on the page
     * @throws \Exception
     */
    private function crawlPagePics()
    {
        $remotePicturesList = self::parsePageForPics($this->siteAddress . $this->pagePath, $this->availableExtensions);
//        var_dump($remotePicturesList); die;
        foreach ($remotePicturesList as $remoteUrl) {
            $webPicture = new WebPicture($remoteUrl);
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
     * @throws \Exception
     */
    public static function parsePageForPics($url, array $availableExtensions)
    {
        /**There we get html and parse for pictures */
        $regExp = '~\<img[^>]*\ssrc\s*=\s*(\'|")(.+?)\1~';
        $httpAddr = 'http://' . $url;
        $domain = parse_url($httpAddr, PHP_URL_HOST);
        try {
            $htmlPage = self::getContents('http://' . $url);
        } catch (\Exception $e) {
            /**@todo : $htmlPage might have not been defined! */
            echo "\n\n" . $e->getMessage() . "\n\n";
        }

        $picturesListFromPage = [];
        $parseResult = preg_match_all($regExp, $htmlPage, $picturesListFromPage);
        if ($parseResult === FALSE) {
            throw new \Exception('Page parsing error.');
        }
        $picturesListFromPage = $picturesListFromPage[2];
        $resultPictureList = [];
        foreach ($picturesListFromPage as $picturePath) {
            $picturePath = str_replace(['../', './'], '', $picturePath, $replacedCount);

            $extensionNotAllowed = TRUE;
            foreach ($availableExtensions as $extension) {
                $realFileExtension = substr($picturePath, -strlen($extension));
                if ($extension === $realFileExtension) {
                    $extensionNotAllowed = FALSE;
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


    //        $urlDomain = parse_url($url, PHP_URL_HOST);
    //        $urlPath = parse_url($url, PHP_URL_PATH);
    //        $dirName = pathinfo($urlPath, PATHINFO_DIRNAME);
    //        $fileBaseName = pathinfo($urlPath, PATHINFO_BASENAME);
    //        $fileExt = pathinfo($urlPath, PATHINFO_EXTENSION);

    /**
     * @author Mykola Danylov (n.danylov@gmail.com)
     * Helper method to get web page content (text)
     * @param $url - page url
     * @return string - page text
     * @throws \Exception
     */
    private static function getContents($url)
    {
        $opts = ['http' =>
            [
                'method' => 'GET',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
                            //. "Content-Length: ".strlen($postData)."\r\n"
                            . "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36"
                            . " (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36\r\n",
                //'content' => $postData,
                'timeout' => 5,
                'accept' => 'image/webp,image/*,*/*;q=0.8',

            ]
        ];
        $headToQuery = stream_context_create($opts);
        $serverString = @file_get_contents($url, NULL, $headToQuery);
        $headers = $http_response_header; //get_headers($mainServerScriptName);
        if ($headers AND $serverString !== FALSE) {
            $httpCode = substr($headers[0], 9, 3);
        } else {
            $httpCode = 0;
        }

        if (($httpCode <> 200)) {
            throw new \Exception('Can not get page for parsing.');
        }
        return $serverString;
    }
}

























