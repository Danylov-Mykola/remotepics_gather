<?php
/**
 * This file is a part of hexa.my project.
 * Author: Mykola Danylov (n.danylov@gmail.com)
 * Date: 20.10.2016
 * Time: 20:08
 */

namespace RemotePics;


class OneHtmlPage {
    private $siteAddress;
    private $storagePath;
    private $pagePath;
    private $availableExtensions = [];
    /** @var  LocalStorage object */
    private $localStorageObj;
    /** @var  array of FileToStoreType */
//    private $newPicturesArr = [];

    function __construct(WebSitePics $webPagePicsObj, $pagePath, $availableExtensions){
        $this->availableExtensions = $availableExtensions;
        $this->pagePath = $pagePath;
        $this->siteAddress = $webPagePicsObj->getSiteDomain();
        $this->storagePath = $webPagePicsObj->getStoragePath();
        $this->localStorageObj = new LocalStorage($this->storagePath, $availableExtensions);
        $this->crawlPagePics();
    }


    private function isLocalPageExists(){
        return $this->localStorageObj->isPathExists($this->siteAddress . $this->pagePath);
    }


    public function getLocalPicsList(){
        return $this->localStorageObj->getPicturesFilesListByStalk($this->siteAddress, $this->pagePath);
    }


    private function crawlPagePics(){
        $remotePicturesList = self::parsePageForPics($this->siteAddress . $this->pagePath, $this->availableExtensions);
            foreach($remotePicturesList as $remoteUrl){
                 $webPicture = new WebPicture($remoteUrl);
                 $this->localStorageObj->store($webPicture, $this->siteAddress . $this->pagePath);
            }

    }

    public static function parsePageForPics($url, array $availableExtensions)
    {
        /**There we get html and parse for pictures */
        $regExp = '~\<img[^>]*\ssrc\s*=\s*(\'|")(.+?)\1~';
        try {
            $htmlPage = self::getContents('http://' . $url);
        } catch (\Exception $e) {
            echo "\n\n" .$e->getMessage() . "\n\n";
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
            foreach($availableExtensions as $extension){
                $realFileExtension = substr($picturePath, -strlen($extension));
                if($extension === $realFileExtension){
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
            $resultPictureList[] = 'http://' . $url . '/' . $picturePath;
        }
        return $resultPictureList;
    }


//        $urlDomain = parse_url($url, PHP_URL_HOST);
//        $urlPath = parse_url($url, PHP_URL_PATH);
//        $dirName = pathinfo($urlPath, PATHINFO_DIRNAME);
//        $fileBaseName = pathinfo($urlPath, PATHINFO_BASENAME);
//        $fileExt = pathinfo($urlPath, PATHINFO_EXTENSION);

    private static function getContents($url){
        $opts = ['http' =>
            [
                'method'  => 'GET',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
//                    "Content-Length: ".strlen($postData)."\r\n".
                    "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36\r\n",
//                'content' => $postData,
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

























