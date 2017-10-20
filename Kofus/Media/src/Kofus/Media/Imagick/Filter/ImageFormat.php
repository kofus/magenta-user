<?php
namespace Kofus\Media\Imagick\Filter;
use Kofus\Media\Imagick\AbstractFilter;

class ImageFormat extends AbstractFilter
{
    public function filter($value)
    {
        if (! $value instanceof \Imagick)
            throw new \Exception('Filter value must be an instance of Imagick');

        $value->setImageFormat($this->getFormat());
            
        return $value;
    }
    
    public function setFormat($value)
    {
        $this->options['format'] = $value; return $this;
    }
    
    public function getFormat()
    {
        return $this->options['format'];
    }
}