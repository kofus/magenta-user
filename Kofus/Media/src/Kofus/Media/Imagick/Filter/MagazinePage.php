<?php
namespace Kofus\Media\Imagick\Filter;
use Kofus\Media\Imagick\AbstractFilter;

class MagazinePage extends AbstractFilter
{
    public function filter($value)
    {
        if (! $value instanceof \Imagick)
            throw new \Exception('Filter value must be an instance of Imagick');
        
        
        $gradient = new \Imagick();
        
        switch ($this->getFold()) {
            case 'left':
                $gradient->newPseudoImage($value->getImageHeight(), $value->getImageWidth() / 2, "gradient:rgba(0, 0, 0, 0.5)-transparent");
                $gradient->rotateImage('transparent', 90);
                $value->compositeImage($gradient, \Imagick::COMPOSITE_OVER, $value->getImageWidth() / 2, 0);
                break;
                
            case 'right':
                $gradient->newPseudoImage($value->getImageHeight(), $value->getImageWidth() / 9, "gradient:rgba(0, 0, 0, 0.5)-transparent");
                $gradient->rotateImage('transparent', -90);
                $value->compositeImage($gradient, \Imagick::COMPOSITE_OVER, 0, 0);
                break;                
        }
            
        
        return $value;
    }
    
    public function setFold($value)
    {
        $this->options['fold'] = $value; return $this;
    }
    
    public function getFold()
    {
        if (! isset($this->options['fold']))
            throw new \Exception('Option "fold" is required');
        return $this->options['fold'];
    }
    
    
    

    
}