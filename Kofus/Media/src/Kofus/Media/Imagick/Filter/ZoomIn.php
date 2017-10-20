<?php
namespace Kofus\Media\Imagick\Filter;
use Kofus\Media\Imagick\AbstractFilter;

class ZoomIn extends AbstractFilter
{
    public function filter($value)
    {
        if (! $value instanceof \Imagick)
            throw new \Exception('Filter value must be an instance of Imagick');
        
        // Make sure that original image is larger than target image
        if ($this->getWidth() > $value->getImageWidth())
            $value->scaleImage($this->getWidth(), null);
        if ($this->getHeight() > $value->getImageHeight())
            $value->scaleImage(null, $this->getHeight());
        
            
        $value->cropImage(
            $this->getWidth(), 
            $this->getHeight(), 
            ($value->getImageWidth() - $this->getWidth()) / 2, 
            ($value->getImageHeight() - $this->getHeight()) / 2
        );
        
        return $value;
    }
    
    public function setWidth($value)
    {
        $this->options['width'] = $value; return $this;
    }
    
    public function getWidth()
    {
        if (! isset($this->options['width']))
            throw new \Exception('Option "width" is required');
        return $this->options['width'];
    }
    
    public function setHeight($value)
    {
        $this->options['height'] = $value; return $this;
    }
    
    public function getHeight()
    {
        if (! isset($this->options['height']))
            throw new \Exception('Option "height" is required');
            return $this->options['height'];
    }
    
   
    
    

    
}