<?php
/**
 * This file is a part of remotepics_gather project.
 * Author: Mykola Danylov (n.danylov@gmail.com)
 * Date: 21.10.2016
 * Time: 19:56
 */

namespace MykolaDanylov\LocalStorage;

/**
 * Interface FileToStoreType
 * @package RemotePics
 * The LocalStorage object can work with objects which have been implemented from this Interface.
 * You can use any source to get files for LocalStorage class.
 */
interface FileToStoreType
{
    /**
     * @return string - file name for LocalStorage
     */
    function getFileName();

    /**
     * @return mixed - raw contents for storing in file by LocalStorage
     */
    function getFileContents();
}