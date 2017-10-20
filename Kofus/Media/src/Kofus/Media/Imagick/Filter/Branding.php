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
        
        $im = $value;
        
        $canvas = new \Imagick();
        $canvas->newImage($im->getImageWidth(), $im->getImageHeight(), new \ImagickPixel('white'));
        $canvas->setImageFormat('jpg');
        $canvas->compositeImage($im, \Imagick::COMPOSITE_DEFAULT, 0, 0);
        
        $imagick = $canvas;
        $branding = new \Imagick($this->options['filename']);
        $branding->scaleImage($imagick->getImageWidth() / 2, null);
        
        $imagick->compositeImage($branding, \Imagick::COMPOSITE_DEFAULT, $imagick->getImageWidth() / 2, ($imagick->getImageHeight() / 3 * 2), $imagick->getImageAlphaChannel());
        
        return $imagick;
            
    }
    
    
    
    
    

    
}