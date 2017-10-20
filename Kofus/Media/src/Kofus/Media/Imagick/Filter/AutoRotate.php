<?php
namespace Kofus\Media\Imagick\Filter;
use Kofus\Media\Imagick\AbstractFilter;

class AutoRotate extends AbstractFilter
{
    public function filter($value)
    {
        if (! $value instanceof \Imagick)
            throw new \Exception('Filter value must be an instance of Imagick');
        
        switch ($value->getImageOrientation()) {
            case \Imagick::ORIENTATION_TOPLEFT:
                break;
            case \Imagick::ORIENTATION_TOPRIGHT:
                $value->flopImage();
                break;
            case \Imagick::ORIENTATION_BOTTOMRIGHT:
                $value->rotateImage("#000", 180);
                break;
            case \Imagick::ORIENTATION_BOTTOMLEFT:
                $value->flopImage();
                $value->rotateImage("#000", 180);
                break;
            case \Imagick::ORIENTATION_LEFTTOP:
                $value->flopImage();
                $value->rotateImage("#000", -90);
                break;
            case \Imagick::ORIENTATION_RIGHTTOP:
                $value->rotateImage("#000", 90);
                break;
            case \Imagick::ORIENTATION_RIGHTBOTTOM:
                $value->flopImage();
                $value->rotateImage("#000", 90);
                break;
            case \Imagick::ORIENTATION_LEFTBOTTOM:
                $value->rotateImage("#000", -90);
                break;
            default: // Invalid orientation
                break;
        }
        $value->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);
            
        return $value;
    }
}