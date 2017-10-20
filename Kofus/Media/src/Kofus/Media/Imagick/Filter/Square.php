<?php
namespace Kofus\Media\Imagick\Filter;
use Kofus\Media\Imagick\AbstractFilter;

class Square extends AbstractFilter
{
    protected $options = array(
        'orientation' => 'center'
    );
    
    
    public function filter($value)
    {
        if (! $value instanceof \Imagick)
            throw new \Exception('Filter value must be an instance of Imagick');
        
        $edge = min($value->getImageWidth(), $value->getImageHeight());
        
        switch ($this->options['orientation']) {
            case 'top':
                $x = ($value->getImageWidth() - $edge) / 2;
                $y = 0;
                break;
                
            case 'center':
                $x = ($value->getImageWidth() - $edge) / 2;
                $y = ($value->getImageHeight() - $edge) / 2;
                break;
                
            default:
                throw new \Exception('Unknown orientation: ' . $this->options['orientation']);
        }
            
        $value->cropImage($edge, $edge, $x, $y);
        
        return $value;
    }
    

   
    
    

    
}