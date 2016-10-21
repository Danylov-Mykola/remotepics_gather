<?php
/**
 * This file is a part of remotepics_gather project.
 * Author: Mykola Danylov (n.danylov@gmail.com)
 * Date: 21.10.2016
 * Time: 19:56
 */

namespace RemotePics;


interface FileToStoreType {
    function getFileName();
    function getFileContents();
}