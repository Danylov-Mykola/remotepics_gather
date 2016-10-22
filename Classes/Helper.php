<?php
/**
 * This file is a part of remotepics_gather project.
 * Author: Mykola Danylov (n.danylov@gmail.com)
 * Date: 22.10.2016
 * Time: 16:16
 */

namespace MykolaDanylov\RemotePics;


class Helper
{
    /**
     * @author Mykola Danylov (n.danylov@gmail.com)
     * Helper method to get web page content (text)
     * @param $url - page url
     * @return string - page text
     * @throws RemotePicException
     */
    public static function getContents($url)
    {
        $opts = ['http' =>
            [
                'method' => 'GET',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
                            . "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36"
                            . " (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36\r\n",
                'timeout' => 5,
                'accept' => 'image/webp,image/*,*/*;q=0.8',

            ]
        ];
        $headToQuery = stream_context_create($opts);
        $serverString = @file_get_contents($url, null, $headToQuery);
        $headers = $http_response_header; //get_headers(using a php magic variable);
        if ($headers AND $serverString !== false) {
            $httpCode = substr($headers[0], 9, 3);
        } else {
            $httpCode = 0;
        }

        if (($httpCode <> 200)) {
            throw new RemotePicException(
                '>>WARNING:: Can not get page for parsing ("' . $url . '").',
                [':method' => __FUNCTION__, ':class' => __CLASS__, ':fileSystem' => $url],
                RemotePicException::E_JUST_FOR_USER
            );
        }
        return $serverString;
    }
}