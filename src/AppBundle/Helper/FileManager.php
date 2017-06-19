<?php
/*
 * This file manager accepts images encoded in base64
 * */
namespace AppBundle\Helper;

class FileManager
{
    private $allowedMimeTypes = array(
        'image/bmp' => '.bmp',
        'image/gif' => '.gif',
        'image/png' => '.png',
        'image/jpeg' => '.jpg',
        'image/svg+xml' => '.svg',
    );
    private $mime = null;
    private $path = null;
    private $raw = null;

    public function __construct( $mediaPath, $file){
        $this->path = $mediaPath;
        if(!$this->resolveFileType( $file )){
            return false;
        }
        //die($this->mime);
    }
    public function save($namePart = null ){
        $dirContent = scandir($this->path);
        $ext = $this->allowedMimeTypes[$this->mime];
        if($namePart == null){
            $namePart = getDate()[0];
        }
        $name = $namePart;
        $ind = 0;
        while(in_array($name . '_' . $ind . $ext, $dirContent)){
            $ind++;
        }
        $name .= '_' . $ind . $ext;
        if(in_array($name, $dirContent)){
            $name += getDate()[0];
        }
        $file = fopen( $this->path . $name, "w" );
        fwrite($file, $this->raw);
        fclose($file);
        return $name;
    }
    private function resolveFileType( $file ){
        $this->raw = base64_decode($file);
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $this->raw, FILEINFO_MIME_TYPE);

        if(!array_key_exists($mime_type, $this->allowedMimeTypes)){
            return false;
        }
        $this->mime = $mime_type;
        return true;
    }
}