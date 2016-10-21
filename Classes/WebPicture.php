<?php
/**
 * This file is a part of remotepics_gather project.
 * Author: Mykola Danylov (n.danylov@gmail.com)
 * Date: 21.10.2016
 * Time: 20:02
 */

namespace RemotePics;


class WebPicture implements FileToStoreType {
    private $pictureName;
    private $pictureContents;

    function __construct($url){
        $this->pictureName = pathinfo($url, PATHINFO_BASENAME);
        $this->pictureContents = @file_get_contents($url);
    }


    public function getFileName(){
        return $this->pictureName;
    }

    public function getFileContents(){
        return $this->pictureContents;
    }
}