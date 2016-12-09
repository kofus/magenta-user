<?php
namespace Kofus\Media\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ImageEntity extends FileEntity 
{
	public function getNodeType()
	{
		return 'IMG';
	}
	
	/**
	 * @return \Imagick
	 */
	public function getImagick()
	{
	    return new \Imagick($this->getPath());
	}
}

