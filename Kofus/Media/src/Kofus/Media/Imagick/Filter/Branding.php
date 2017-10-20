<?php
namespace Kofus\Media\Imagick\Filter;
use Kofus\Media\Imagick\AbstractFilter;

class Branding extends AbstractFilter
{
    protected $options = array(
        'filename' => null
    );
    
    public function filter($value)
    {
        if (! $value instanceof \Imagick)
            throw new \Exception('Filter value must be an instance of Imagick');
        
        $this->imagick = $value;
        
        $canvas = new \Imagick();
        $canvas->newImage($this->imagick->getImageWidth(), $this->imagick->getImageHeight(), new \ImagickPixel('white'));
        $canvas->setImageFormat('jpg');
        $canvas->compositeImage($this->imagick, \Imagick::COMPOSITE_DEFAULT, 0, 0);
        
        
        $rectangle = new \ImagickDraw();
        $rectangle->setFillColor(new \ImagickPixel('rgba(1, 29, 79, 0.6)'));
        $rectangle->rectangle(0, 670, 800, 800);
        $canvas->drawImage($rectangle);
        
        
        $imagick = $canvas;
        $branding = new \Imagick($this->options['filename']);
        $branding->setCompression(100);
        $branding->setCompressionQuality(100);
        $branding->scaleImage(null, 100);
        
        $imagick->compositeImage($branding, \Imagick::COMPOSITE_DEFAULT, 550 , 686, \Imagick::ALPHACHANNEL_TRANSPARENT);
        
        return $imagick;
            
    }
    
    
    
    
    

    
}