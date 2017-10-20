<?php
namespace Kofus\Media\Imagick\Filter;
use Kofus\Media\Imagick\AbstractFilter;

class Resize extends AbstractFilter
{
    
    protected $options = array(
        'max-width' => null,
        'max-height' => null,
        'width' => null,
        'height' => null
    );
    
    public function filter($value)
    {
        if (! $value instanceof \Imagick)
            throw new \Exception('Filter value must be an instance of Imagick');
        
        $imagick = $value;
        
        // Fixed width and height => Thumbnail on canvas
        if (isset($this->options['width']) && isset($this->options['height'])) {
            $width = $this->options['width'];
            $height = $this->options['height'];
            if ($imagick->getImageWidth() > $width)
                $imagick->scaleImage($width, null);
            if ($imagick->getImageHeight() > $height)
                $imagick->scaleImage(null, $height);
                    
            // Calc offsets
            $x = 0; $y = 0;
            if ($width > $imagick->getImageWidth())
                $x = ($width - $imagick->getImageWidth()) / 2;
            if ($height > $imagick->getImageHeight())
                $y = ($height - $imagick->getImageHeight()) / 2;
                            
            $canvas = new \Imagick();
            if (in_array($imagick->getImageFormat(), array('gif', 'png'))) {
                $canvas->newImage($width, $height, new \ImagickPixel('transparent'));
            } else {
                $canvas->newImage($width, $height, new \ImagickPixel('white'));
            }
            $canvas->setImageColorSpace($imagick->getImageColorSpace());
            $canvas->compositeImage($imagick, \Imagick::COMPOSITE_DEFAULT, $x, $y, \Imagick::CHANNEL_ALPHA);
            $canvas->setImageFormat($imagick->getImageFormat());
            $imagick = $canvas;
                                
        // Max width or height => flexible width/height
        } elseif (isset($this->options['max-width']) || isset($this->options['max-height'])) {
            if (isset($this->options['max-width'])) {
                $maxWidth = $this->options['max-width'];
                if ($imagick->getImageWidth() > $maxWidth)
                    $imagick->scaleImage($maxWidth, null);
            }
            if (isset($this->options['max-height'])) {
                $maxHeight = $this->options['max-height'];
                if ($imagick->getImageHeight() > $maxHeight)
                    $imagick->scaleImage(null, $maxHeight);
            }
        } elseif (isset($this->options['width']) && ! isset($this->options['height'])) {
            $imagick->scaleImage($this->options['width'], null);
        } elseif (isset($this->options['height']) && ! isset($this->options['width'])) {
            $imagick->scaleImage(null, $this->options['height']);
        }
            
        return $imagick;
    }
    


    
    

    
}