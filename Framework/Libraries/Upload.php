<?php

class Upload
{

    public $location;
    public $filename;
    public $filetype;
    private $allowedTypes = array(
        'mp3' => 'audio/mpeg,audio/x-mpeg,audio/mp3,audio/x-mp3,audio/mpeg3,audio/x-mpeg3,audio/mpg,audio/x-mpg,audio/mpg,audio/x-mpegaudio',
        'mpe,mpeg,mpg,mpa' => 'video/mpeg',
        'mpga,mp2,mp3,mp4,mpa' => 'audio/mpeg',
        'avi' => 'application/x-troff-msvideo,video/avi,video/msvideo,video/x-msvideo',
        'asx' => 'application/x-mplayer2,video/x-ms-asf,video/x-ms-asf-plugin',
        'png' => 'image/png',
        'jpe,jpeg,jpg,jfif,jfif-tbnl' => 'image/jpeg',
        'jpg,jpeg,jpe,jfif' => 'image/pjpeg',
        'gif' => 'image/gif',
        'wav' => 'audio/x-wav',
        'bmp' => 'image/bmp,image/x-windows-bmp',
        'tif,tiff' => 'image/tiff',
        'txt,asc' => 'text/plain',
        'pdf' => 'application/pdf',
        'swf' => 'application/x-shockwave-flash',
        'svg' => 'image/svg+xml',
        'exe' => 'application/octet-stream',
        'gz' => 'application/x-gzip,application/x-compressed,application/x-compressed-tar',
        'gzip' => 'multipart/x-gzip',
        'tar' => 'application/x-tar',
        'rar' => 'application/x-rar-compressed',
        'zip' => 'application/x-zip-compressed',
        'bin' => 'application/mac-binary,application/macbinary,application/octet-stream,application/x-binary,application/x-macbinary');
    
    function __construct($allowedtypes = array())
    {
        if (isset($_FILES['fileupload'])) {
            $this->filename = $_FILES['fileupload']['name'];
            $this->filetype = $_FILES['fileupload']['type'];
            $this->location = $_FILES['fileupload']['tmp_name'];
        }
    }
    
    function isAllowed($filename, $filetype)
    {
        $retval = false;
        foreach ($this->allowedTypes as $key => $value) {
            $exts = explode(',', $key);
            $types = explode(',', $value);
            foreach ($exts as $ext) {
                if (preg_match("/\.$ext$/i", $filename) && in_array($filetype, $types)) $retval = true;
            }
        }
        return $retval;
    }
    
    function isImage()
    {
        return getimagesize($this->location) ? true : false;
    }
    
    function isUpload()
    {
        return !empty($_FILES) ? true : false;
    }
    
    function moveTo($filepath)
    {
        return move_uploaded_file($this->location, $filepath);
    }

}

?>