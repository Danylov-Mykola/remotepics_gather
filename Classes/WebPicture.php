<?php
/**
 * This file is a part of remotepics_gather project.
 * Author: Mykola Danylov (n.danylov@gmail.com)
 * Date: 21.10.2016
 * Time: 20:02
 */

namespace RemotePics;

/**
 * Class WebPicture
 * @package RemotePics
 * This is a wrapper for file. It has used for LocalStorage object.
 * See FileToStoreType Interface as well.
 */
class WebPicture implements
    FileToStoreType
{
    /** @var string */
    private $pictureName;
    /** @var  mixed raw data */
    private $pictureContents;

    /**
     * @param $url - fully qualified url address
     */
    function __construct($url)
    {
        $this->pictureName = pathinfo($url, PATHINFO_BASENAME);
        $this->pictureContents = @file_get_contents($url);
        //        echo $url ."\n";
    }

    /**
     * @author Mykola Danylov (n.danylov@gmail.com)
     * @return string - file name
     */
    public function getFileName()
    {
        return $this->pictureName;
    }

    /**
     * @author Mykola Danylov (n.danylov@gmail.com)
     * @return mixed raw data
     */
    public function getFileContents()
    {
        return $this->pictureContents;
    }
}