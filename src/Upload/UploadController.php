<?php

namespace Tommy001\Mymodule;

/**
 * Class for checking and processing uploaded gif, png and jpg images
 *
 */
class UploadController implements \Anax\DI\IInjectionAware {
    use \Anax\DI\TInjectable;
    
    public $new_height;
    public $new_width;
    protected $srcimg;
    protected $destimg;
    protected $upload;    
    protected $imgpath;
    protected $ext;
    protected $filename;
    protected $_destination;

    /**
     * Constructor
     *
     */
    public function __construct($destination=null) {
        $this->imgpath = $destination;
    }

    /**
     * Move file
     *
     * @param string $name
     * @return boolean
     */
    public function movefile($name) {
        if(empty($_FILES[$name]) || !is_uploaded_file($_FILES[$name]['tmp_name']))
        {
            return FALSE;
        }

        return move_uploaded_file($_FILES[$name]['tmp_name'], $this->imgpath);
    }
    
    public function checkMimeType(){
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        if (false === $this->ext = array_search(
            $finfo->file($_FILES['img']['tmp_name']),
            array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                ),true)){
            
                return false;
                } else {
                return true;
            }
    }
    
    
    /**
    * Initialize the controller.
    *
    * @return void
    */
    /**
 * @codeCoverageIgnore
 */
    public function initialize() {
    
        $this->upload = new \Tommy001\Mymodule\Upload();
        $this->upload->setDI($this->di);
    }  
    
/**
 * @codeCoverageIgnore
 */    
    public function entryAction() {

        $res = $this->checkUpload();
        if($res === true){    
            $this->imageSize();
            $res = $this->createImageSource();
            if($res === false){
                $this->errorMessage();
            }
            $res = $this->createImage();
            if($res === false){
                $this->errorMessage();
            }
            $res = $this->transparencyAndResample();
            if($res === false){
                $this->errorMessage();
            } 
            $res = $this->outputImage();
            if($res === false){
                $this->errorMessage();
            }            
            $filename = $this->sanitizeFilename();
            $this->saveToDatabase($filename);
            $this->viewUpload();
        } else {
           $this->views->add('mymodule/messages', [
           'message' => $res,
        ]);                    
        }
    }
/**
 * @codeCoverageIgnore
 */    
    private function errorMessage(){
        $this->views->add('mymodule/messages', [
            'message' => 'Ett fel uppstod vid bildbehandlingen.',
                    ]);
    }
/**
 * @codeCoverageIgnore
 */    
    private function checkUpload(){

        try {
   
            if (
                !isset($_FILES['img']['error']) ||
                is_array($_FILES['img']['error'])
                ) {
            throw new \Anax\Exception('Invalid parameters.');
            }

            switch ($_FILES['img']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new \Anax\Exception('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new \Anax\Exception('Exceeded filesize limit defined in form.');
            default:
                throw new \Anax\Exception('Unknown errors.');
        }
            
            if(mb_strlen($_FILES['img']['name'],"UTF-8") > 100) {
            throw new \Anax\Exception('Filename is too long.');
        }

            if(!$this->checkMimeType())

        {
            throw new \Anax\Exception('Wrong file type.');
        } 
        
            if($_FILES['img']['size'] > 2000000) {
            throw new \Anax\Exception('Exceeded filesize limit defined in script.');
        }        

            $this->imgpath = sprintf('img/upload/%s.%s',
            sha1_file($_FILES['img']['tmp_name']), $this->ext);
            if(!$this->movefile('img'))
            
        {
        throw new \Anax\Exception('Failed to move uploaded file. Check to see that you have a folder named \'img/upload\' in your Anax webroot folder');
        }    
          
            
            return true; // passed all checks

        } catch (\Anax\Exception $e) {

            $message = $e->getMessage();
            return $message;

        }
    }

    public function imageSize() {

        $size = 300; // the default image height
        chmod ($this->imgpath, octdec('0666')); // read-write
        $sizes = getimagesize($this->imgpath);
        $aspect_ratio = $sizes[1]/$sizes[0]; 
        if ($sizes[1] <= $size) {
            $this->new_width = $sizes[0];
            $this->new_height = $sizes[1];
        } else {
            $this->new_height = $size; 
            $this->new_width = round($this->new_height/$aspect_ratio); 
        }
    }
        
    public function createImageSource(){    
        /* create an image source with specified dimensions */
        $this->destimg=imagecreatetruecolor($this->new_width,$this->new_height);
        if(!$this->destimg){
            return false;
        }
        return true;
    }

    public function createImage(){

        switch ($this->ext) {
            case 'gif':
                $this->srcimg = imagecreatefromgif($this->imgpath);
                if(!$this->srcimg){
                    return false;
                }
                return true;
            case 'jpg':
                $this->srcimg = imagecreatefromjpeg($this->imgpath);
                if(!$this->srcimg){
                    return false; 
                }
                return true;
            case 'png':
                $this->srcimg = imagecreatefrompng($this->imgpath);
                if(!$this->srcimg){
                    return false;
                }
                return true;
            default:
                $this->srcimg = null;
                return false;
        }
    }
    
    public function transparencyAndResample(){
        
        if($this->ext == 'png' || $this->ext == 'gif'){
            imagecolortransparent($this->destimg, imagecolorallocatealpha($this->destimg, 0, 0, 0, 0)); // numbers are red, green, blue and last alpha
            imagealphablending($this->destimg, false);
            imagesavealpha($this->destimg, true); //true makes surre that all alpha channel-info is kept
        }
        /* copy frame from $srcimg to the image in $destimg and resample image to reduce data size  */	
        $res = imagecopyresampled($this->destimg,$this->srcimg,0,0,0,0,$this->new_width,$this->new_height,imagesx($this->srcimg),imagesy($this->srcimg));
         if(!$res){
             return false;
         } 
         return true;
    }

    public function outputImage(){

        switch ($this->ext) {
        case 'gif':
            $res = imagegif($this->destimg,$this->imgpath);
            if(!$res){
                return false;
            }     
            return true;
        case 'jpg':
            $res = imagejpeg($this->destimg,$this->imgpath); 
            if(!$res){
                return false;
            }
            return true;
        case 'png':
            $res = imagepng($this->destimg,$this->imgpath);                
            if(!$res){
                return false;
            } 
            return true;    
        }
    }
    
    public function sanitizeFilename(){
        
        /* in this pattern you can add special characters used in you language and their replacements, otherwise they will just be stripped from the filename */ 
        $filename = preg_replace(array('/å/', '/ä/', '/ö/', '/Å/', '/Ä/', '/Ö/', '/[^a-zA-Z0-9\/_|+ .-]/', '/[ -]+/', '/^-|-$/'),
        array('a', 'a', 'o', 'a', 'a', 'o', '', '_', ''), mb_strtolower($_FILES['img']['name'], 'UTF-8'));
        return $filename;
    }
/**
 * @codeCoverageIgnore
 */        
    public function saveToDatabase($filename){   
        $this->initialize();
        $this->upload->add([
            '0' => $filename,
            '1' => $this->imgpath, 
        ]);
    }
   
        /* the code below is for test purpose, modify or replace it with needed
        structure to work in you application */  
/**
 * @codeCoverageIgnore
 */        
    public function viewUpload(){
        $lastimage = $this->upload->findLast();
        $url = $this->di->url->asset($this->imgpath);

        $this->views->add('mymodule/uploadform', [
        ]); 
        
        $img_cap = "<br><br><img src=$url alt='Uploaded image' /><br>
        <p>Sanitized original filename stored in database, can e.g. be used to represent this image in a download list:<strong> ".$lastimage[0]['san_filename']."</strong></p>
        <p>Relative image path on disk with secure filename, stored in database: <strong>".$lastimage[0]['path']."</strong></p>";
        
        $this->views->addString($img_cap, 'main');

    }
}
        
