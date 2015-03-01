<?php

namespace Tommy001\Mymodule;

class UploadControllerTest extends \PHPUnit_Framework_TestCase {
    
    protected $_objectjpg;  
    protected $_objectjpg250;      
    protected $_objectpng;  
    protected $_objectgif;
    protected $_objecttxt;    
    
    
    protected function setUp()
    {
       $this->_objectjpg = new \Tommy001\Mymodule\UploadController(__DIR__ . '/img/upload/testing.jpg');
       $this->_objectjpg250 = new \Tommy001\Mymodule\UploadController(__DIR__ . '/img/upload/testing_250.jpg');       
       $this->_objectgif = new \Tommy001\Mymodule\UploadController(__DIR__ . '/img/upload/testing.gif');
       $this->_objectpng = new \Tommy001\Mymodule\UploadController(__DIR__ . '/img/upload/testing.png'); 
       $this->_objecttxt = new \Tommy001\Mymodule\UploadController(__DIR__ . '/img/upload/testing.txt');            
                
        $_FILES = array(
            'img' => array(
                'name' => '\åäöÅÄÖ_test.jpg',
                'type' => 'image/jpeg',
                'size' => 198,
                'tmp_name' => __DIR__ . '/img/upload/source-test.jpg',
                'error' => 0
            )
        );

    } 

    
 
    public function testMoveFile()
    {
        $this->assertTrue($this->_objectjpg->movefile('img'));
    }  
    public function testCheckMimeType()
    {             
        $this->assertTrue($this->_objectjpg->checkMimeType());
    }     
    public function testFileSize1()
    {
        $this->_objectjpg->imageSize();
        $var = get_object_vars($this->_objectjpg);       
        $width = $var['new_width'];
        $height = $var['new_height'];
        $expHeight = 300;
        $expWidth = 204;
        $this->assertEquals($expHeight, $height, "The heights does not match.");
        $this->assertEquals($expWidth, $width, "The widths does not match."); 
    }
    public function testFileSize2()
    {   $_FILES = array(
            'img' => array(
                'name' => '\åäöÅÄÖ_test.jpg',
                'type' => 'image/jpeg',
                'size' => 198,
                'tmp_name' => __DIR__ . '/img/upload/source-test_250.jpg',
                'error' => 0
            )
        );     
        $this->_objectjpg250->imageSize();
        $var250 = get_object_vars($this->_objectjpg250);         
        $width250 = $var250['new_width'];
        $height250 = $var250['new_height'];
        $expHeight250 = 250;
        $expWidth250 = 170;
        $this->assertEquals($expHeight250, $height250, "The heights does not match.");
        $this->assertEquals($expWidth250, $width250, "The widths does not match.");           
    }
    public function testCreateImageSource()
    {
        $this->_objectjpg->imageSize();        
        $this->assertTrue($this->_objectjpg->createImageSource());
    }
    public function testCreateImage()
    {        
        $this->_objectjpg->checkMimeType();        
        $this->assertTrue($this->_objectjpg->createImage());
        $_FILES = array(
            'img' => array(
                'name' => '\åäöÅÄÖ_test.gif',
                'type' => 'image/gif',
                'size' => 198,
                'tmp_name' => __DIR__ . '/img/upload/source-test.gif',
                'error' => 0
            )
        );         
        $this->_objectgif->checkMimeType();        
        $this->assertTrue($this->_objectgif->createImage());
        $_FILES = array(
            'img' => array(
                'name' => '\åäöÅÄÖ_test.png',
                'type' => 'image/png',
                'size' => 198,
                'tmp_name' => __DIR__ . '/img/upload/source-test.png',
                'error' => 0
            )
        ); 
        $this->_objectpng->checkMimeType();        
        $this->assertTrue($this->_objectpng->createImage()); 
        $_FILES = array(
            'img' => array(
                'name' => '\åäöÅÄÖ_test.txt',
                'type' => 'text/plain',
                'size' => 198,
                'tmp_name' => __DIR__ . '/img/upload/testing.txt',
                'error' => 0
            )
        ); 
        $this->_objecttxt->checkMimeType();        
        $this->assertFalse($this->_objecttxt->createImage());         
    }    
    public function testTransparencyAndResample()
    {   
        $_FILES = array(
            'img' => array(
                'name' => '\åäöÅÄÖ_test.png',
                'type' => 'image/png',
                'size' => 198,
                'tmp_name' => __DIR__ . '/img/upload/source-test.png',
                'error' => 0
            )
        );         
        $this->_objectpng->checkMimeType();
        $this->_objectpng->imageSize();         
        $this->_objectpng->createImageSource();         
        $this->_objectpng->createImage(); 
        $this->assertTrue($this->_objectpng->transparencyAndResample());
    }  
  
    public function testOutputImagejpg()
    {      
        $this->_objectjpg->checkMimeType();
        $this->_objectjpg->imageSize();         
        $this->_objectjpg->createImageSource();         
        $this->_objectjpg->createImage(); 
        $this->_objectjpg->transparencyAndResample();
        $this->assertTrue($this->_objectjpg->outputImage());
    }  
    public function testOutputImagegif()
    {   
        $_FILES = array(
            'img' => array(
                'name' => '\åäöÅÄÖ_test.gif',
                'type' => 'image/gif',
                'size' => 198,
                'tmp_name' => __DIR__ . '/img/upload/source-test.gif',
                'error' => 0
            )
        );         
        $this->_objectgif->checkMimeType();
        $this->_objectgif->imageSize();         
        $this->_objectgif->createImageSource();         
        $this->_objectgif->createImage(); 
        $this->_objectgif->transparencyAndResample();
        $this->assertTrue($this->_objectgif->outputImage());
    }  
    public function testOutputImagenpg()
    {      
        $_FILES = array(
            'img' => array(
                'name' => '\åäöÅÄÖ_test.png',
                'type' => 'image/png',
                'size' => 198,
                'tmp_name' => __DIR__ . '/img/upload/source-test.png',
                'error' => 0
            )
        );         
        $this->_objectpng->checkMimeType();
        $this->_objectpng->imageSize();         
        $this->_objectpng->createImageSource();         
        $this->_objectpng->createImage(); 
        $this->_objectpng->transparencyAndResample();
        $this->assertTrue($this->_objectpng->outputImage());
    }      
    public function testSanitizeFilename()
    {
        $filename = 'aaoaao_test.jpg';
        $filename2 = $this->_objectjpg->sanitizeFilename();
        $this->assertEquals($filename, $filename2, "The name does not match.");
    }
}
