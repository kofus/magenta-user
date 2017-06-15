<?php
namespace Kofus\Media\Processor\Imagick;

class DefaultProcessor
{
    protected $specs = array(); 
    
    public function setSpecifications(array $specs)
    {
        $this->specs = $specs; return $this;
    }
    
    protected $imagick;
    
    public function setImagick(\Imagick $imagick)
    {
        $this->imagick = $imagick; return $this;
    }
    
    public function getImagick()
    {
        return $this->imagick;
    }
    
    public function process()
    {
        if (isset($this->specs['colorspace']))
            $this->imagick->setImageColorSpace($this->specs['colorspace']);
        if (isset($this->specs['filters'])) {
        	foreach ($this->specs['filters'] as $filter) {
        	    if (! isset($filter['options'])) $filter['options'] = array();
        		switch ($filter['name']) {
        			case 'auto-rotate':
        			    $this->applyFilterAutoRotate();
        			    break;
        			case 'magazine-page-left':
        				$this->applyFilterMagazinePageLeft();
        				break;
        			case 'magazine-page-right':
        				$this->applyFilterMagazinePageRight();
        				break;
        				
        			case 'branding':
        				$this->applyFilterBranding($filter['options']);
        				break;
        				
        		}
        	}
        }
        $this->resize();
    }
    
    protected function applyFilterBranding($options)
    {
    	$canvas = new \Imagick();
    	$canvas->newImage($this->imagick->getImageWidth(), $this->imagick->getImageHeight(), new \ImagickPixel('white'));
    	$canvas->setImageFormat('jpg');
    	$canvas->compositeImage($this->imagick, \Imagick::COMPOSITE_DEFAULT, 0, 0);
    	
    	$imagick = $canvas;
    	$branding = new \Imagick($options['filename']);
    	$branding->scaleImage($imagick->getImageWidth() / 2, null);
    	
    	//$imagick->compositeImage($branding, Imagick::COMPOSITE_DEFAULT, 0, 0, Imagick::CHANNEL_ALPHA);
    	//$imagick->compositeImage($branding, \Imagick::COMPOSITE_DEFAULT, $imagick->getImageWidth() / 2, $imagick->getImageHeight() / 3, $imagick->getImageAlphaChannel());
    	//$imagick->compositeImage($branding, \Imagick::COMPOSITE_DEFAULT, 0, ($imagick->getImageHeight() / 3 * 2), $imagick->getImageAlphaChannel());

    	$imagick->compositeImage($branding, \Imagick::COMPOSITE_DEFAULT, $imagick->getImageWidth() / 2, ($imagick->getImageHeight() / 3 * 2), $imagick->getImageAlphaChannel());
    	   
    	$this->imagick = $imagick;
    }
    
    protected function applyFilterMagazinePageLeft()
    {
    	$gradient = new \Imagick();
    	$gradient->newPseudoImage($this->imagick->getImageHeight(), $this->imagick->getImageWidth() / 2, "gradient:rgba(0, 0, 0, 0.5)-transparent");
    	$gradient->rotateImage('transparent', 90);
    	$this->imagick->compositeImage($gradient, \Imagick::COMPOSITE_OVER, $this->imagick->getImageWidth() / 2, 0);
    }
    
    protected function applyFilterMagazinePageRight()
    {
    	$gradient = new \Imagick();
    	$gradient->newPseudoImage($this->imagick->getImageHeight(), $this->imagick->getImageWidth() / 9, "gradient:rgba(0, 0, 0, 0.5)-transparent");
    	$gradient->rotateImage('transparent', -90);
    	$this->imagick->compositeImage($gradient, \Imagick::COMPOSITE_OVER, 0, 0);
    }
    
    protected function applyFilterAutoRotate()
    {
        switch ($this->imagick->getImageOrientation()) {
        	case \Imagick::ORIENTATION_TOPLEFT:
        		break;
        	case \Imagick::ORIENTATION_TOPRIGHT:
        		$this->imagick->flopImage();
        		break;
        	case \Imagick::ORIENTATION_BOTTOMRIGHT:
        		$this->imagick->rotateImage("#000", 180);
        		break;
        	case \Imagick::ORIENTATION_BOTTOMLEFT:
        		$this->imagick->flopImage();
        		$this->imagick->rotateImage("#000", 180);
        		break;
        	case \Imagick::ORIENTATION_LEFTTOP:
        		$this->imagick->flopImage();
        		$this->imagick->rotateImage("#000", -90);
        		break;
        	case \Imagick::ORIENTATION_RIGHTTOP:
        		$this->imagick->rotateImage("#000", 90);
        		break;
        	case \Imagick::ORIENTATION_RIGHTBOTTOM:
        		$this->imagick->flopImage();
        		$this->imagick->rotateImage("#000", 90);
        		break;
        	case \Imagick::ORIENTATION_LEFTBOTTOM:
        		$this->imagick->rotateImage("#000", -90);
        		break;
        	default: // Invalid orientation
        		break;
        }
        $this->imagick->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);        
    }
    
    protected function resize()
    {
    	$this->imagick->setImageFormat($this->specs['extension']);
    
    	// Border?
    	if (isset($this->specs['border'])) {
    		$this->imagick->borderImage(
    				$this->specs['border']['color'],
    				$this->specs['border']['width'],
    				$this->specs['border']['height']
    		);
    	}
    
    	// Fixed width and height => Thumbnail on canvas
    	if (isset($this->specs['width']) && isset($this->specs['height'])) {
    		$width = $this->specs['width'];
    		$height = $this->specs['height'];
    		if ($this->imagick->getImageWidth() > $width)
    			$this->imagick->scaleImage($width, null);
    		if ($this->imagick->getImageHeight() > $height)
    			$this->imagick->scaleImage(null, $height);
    
    		// Calc offsets
    		$x = 0; $y = 0;
    		if ($width > $this->imagick->getImageWidth())
    			$x = ($width - $this->imagick->getImageWidth()) / 2;
    		if ($height > $this->imagick->getImageHeight())
    			$y = ($height - $this->imagick->getImageHeight()) / 2;
    
    		$canvas = new \Imagick();
    		if (in_array($this->specs['extension'], array('gif', 'png'))) {
    			$canvas->newImage($width, $height, new \ImagickPixel('transparent'));
    		} else {
    			$canvas->newImage($width, $height, new \ImagickPixel('white'));
    		}
    		$canvas->compositeImage($this->imagick, \Imagick::COMPOSITE_DEFAULT, $x, $y, \Imagick::CHANNEL_ALPHA);
    		$canvas->setImageFormat($this->specs['extension']);
    		if (isset($this->specs['compression_quality']))
    			$canvas->setImageCompressionQuality($this->specs['compression_quality']);
    		$this->imagick = $canvas;
    
        // Max width or height => flexible width/height
    	} elseif (isset($this->specs['max-width']) || isset($this->specs['max-height'])) {
    		if (isset($this->specs['max-width'])) {
    			$maxWidth = $this->specs['max-width'];
    			if ($this->imagick->getImageWidth() > $maxWidth)
    				$this->imagick->scaleImage($maxWidth, null);
    		}
    		if (isset($this->specs['max-height'])) {
    			$maxHeight = $this->specs['max-height'];
    			if ($this->imagick->getImageHeight() > $maxHeight)
    				$this->imagick->scaleImage(null, $maxHeight);
    		}
    	}
    }
    
    
}