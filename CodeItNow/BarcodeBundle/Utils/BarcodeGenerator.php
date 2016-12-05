<?php
/**
 * BarcodeGenerator
 * @author  Akhtar Khan <er.akhtarkhan@gmail.com>
 * @link http://www.codeitnow.in 
 */
namespace CodeItNow\BarcodeBundle\Utils;
use CodeItNow\BarcodeBundle\Utils\BarcodeType;
use CodeItNow\BarcodeBundle\Generator\CINColor;
use CodeItNow\BarcodeBundle\Generator\CINDrawing;
use CodeItNow\BarcodeBundle\Generator\CINFontFile;

class BarcodeGenerator extends BarcodeType {
    
    /**
     * Resolution
     */
    private $scale;
    
    /**
     * Thikness
     */
    private $thickness;
    
    /**
     * Text and barcode color
     */
    private $foregroundColor = '#000000';
    
    /**
     * Background color
     */
    private $backgroundColor = '#FFFFFF';
    
    /**
     * Font path for barcode
     */
    private $font;

    /**
     * Font size for text
     */
    private $fontSize;

    /**
     * Barcode type such as code128, code39 etc.
     */
    private $barcodeType;
    
    /**
     * Barcode format such as png, jpeg, gif or wbmp
     */
    private $format;
    
    /**
     * Text to generate barcode
     */
    private $text;
    
    /**
     * Filename to save barcode
     */
    private $filename = '';
    
    /**
     * Barcode types are allowed to generate
     */
    private $allowedFormats = array(
        'PNG', 'JPEG', 'GIF', 'WBMP'
    );

    /**
     * Clear labels flag
     */
    private $label = FALSE;

    /**
     * Set Resolution
     * @param int $scale
     *
     * @return BarcodeGenerator
     */
    public function setScale($scale){
        $this->scale = $scale;

        return $this;
    }
    
    /**
     * Set Thickness or Height
     * @param int $thickness
     *
     * @return BarcodeGenerator
     */
    public function setThickness($thickness){
        $this->thickness = $thickness;

        return $this;
    }
    
    /**
     * Set Text or barcode color
     * @param string $foregroundColor
     *
     * @return BarcodeGenerator
     */
    public function setForegroundColor($foregroundColor){
        $this->foregroundColor = $foregroundColor;

        return $this;
    }
    
    /**
     * Set background color
     * @param string $backgroundColor
     *
     * @return BarcodeGenerator
     */
    public function setBackgroundColor($backgroundColor){
        $this->backgroundColor = $backgroundColor;

        return $this;
    }
    
    /**
     * Set font path to use in barcode text
     * @param string $font
     *
     * @return BarcodeGenerator
     */
    public function setFont($font){
        $this->font = $font;

        return $this;
    }
    
    /**
     * Set Barcode type such as code128
     * @param string $type
     *
     * @return BarcodeGenerator
     */
    public function setType($type){
        $this->barcodeType = $type;

        return $this;
    }
    
    /**
     * Set barcode format such as png, gif, jpeg
     * @param string $format
     *
     * @return BarcodeGenerator
     */
    public function setFormat($format){
        $this->format = $format;

        return $this;
    }
    
    /**
     * Set text to generate barcode
     * @param string $text
     *
     * @return BarcodeGenerator
     */
    public function setText($text){
        $this->text = $text;

        return $this;
    }
    
    /**
     * Set filename with path to save barcode
     * @param string $filename
     *
     * @return BarcodeGenerator
     */
    public function setFilename($filename){
        $this->filename = $filename;

        return $this;
    }

    /**
     * Set font size
     * @param int $fontSize
     *
     * @return BarcodeGenerator
     */
    public function setFontSize($fontSize)
    {
        $this->fontSize = $fontSize;

        return $this;
    }

    /**
     * Set label, leave empty for automatic label
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Generate barcode
     * @param string $text      Barcode text to generate
     * @param string $type      Barcode type such as code128
     * @param string $format    Barcode format such as png, jpeg
     * @param string $fontPath  Font path to use in barcode text
     * @return string           Base64Encoded string will return
     */
    public function generate($text=null, $type=null, $format=null, $fontPath=null){
        if(isset($text)){
            $this->text = $text;
        }
        if(isset($type)){
            $this->barcodeType = $type;
        }
        if(isset($format)){
            $this->format = $format;
        }
        
        if(isset($fontPath)){
            $this->font = $fontPath;
        }
        
        return $this->_render();
    }
    
    /**
     * Get barcode object to create image
     * @return object   Barcode object
     */
    private function _getCode(){
        $code = null;
        //try{
            $text = $this->text;
            $textColor = new CINColor($this->foregroundColor);
            $backgroudColor = new CINColor($this->backgroundColor);
            $fontPath = isset($this->font) ? $this->font : $this->_getDefaultFont();

            $font = new CINFontFile($fontPath, (is_int($this->fontSize)) ? $this->fontSize : 18);

            $codeClass = "\\CodeItNow\\BarcodeBundle\\Generator\\".$this->barcodeType;
            $code = new $codeClass();
            if($this->scale){
                $code->setScale($this->scale); // Resolution
            }
            if($this->thickness){
                $code->setThickness($this->thickness); // Thickness
            }
            if($this->label !== FALSE){
                $code->setLabel($this->label);
            }
            $code->setForegroundColor($textColor); // Color of bars
            $code->setBackgroundColor($backgroudColor); // Color of spaces
            $code->setFont($font); // Font (or 0)
            $code->parse($text); // Text
        //} catch (\Exception $ex) {
            
        //}
        return $code;
    }
    
    /**
     * Render barcode as base64 encoded
     * @return string   Base64Encoded image
     */
    private function _render(){
        $textColor = new CINColor($this->foregroundColor);
        $backgroudColor = new CINColor($this->backgroundColor);
        
        /* Here is the list of the arguments
        1 - Filename (empty : display on screen)
        2 - Background color */
        $drawing = new CINDrawing($this->filename, $backgroudColor);
        
        $drawException = null;
        
        if(isset($this->format) and !in_array(strtoupper($this->format), $this->allowedFormats)){
            $drawException = $this->format .' format is not allowed.';
        }
        
        if(!isset($this->barcodeType)){
            $drawException = 'Unable to generate barcode for unknown type';
        }else{
            try{
                if(!($code = $this->_getCode())){
                    $drawException = 'Unable to generate barcode';
                }
            } catch(\Exception $e){
                $drawException = $e->getMessage();
            }
        }

        ob_start();
        if($drawException) {
            $exception = new \Exception($drawException);
            $drawing->drawException($exception);
        } else {
            $drawing->setBarcode($code);
            $drawing->draw();
        }
        $drawing->finish($this->_getFormat());
        $barcodeImg = ob_get_clean();
        $barcodeImg = base64_encode($barcodeImg);
        return $barcodeImg;
    }
    
    /**
     * Barcode image format
     * @return string
     */
    private function _getFormat(){
        $format = '';
        switch(strtoupper($this->format)){
            case 'PNG':
                $format = CINDrawing::IMG_FORMAT_PNG;
                break;
            case 'JPEG':
                $format = CINDrawing::IMG_FORMAT_JPEG;
                break;
            case 'GIF':
                $format = CINDrawing::IMG_FORMAT_GIF;
                break;
            case 'WBMP':
                $format = CINDrawing::IMG_FORMAT_WBMP;
                break;
            default:
                $format = CINDrawing::IMG_FORMAT_PNG;
                break;
        }
        return $format;
    }
    
    /**
     * Get default font for barcode if not provided by user
     * @global object $kernel
     * @return string
     */
    private function _getDefaultFont(){
        $fontPath = dirname(__DIR__)."/Resources/font/Arial.ttf";
        return $fontPath;
    }
    
}
