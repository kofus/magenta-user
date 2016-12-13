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
	
	/**
	 * @ORM\Column(nullable=true, type="integer")
	 */
	protected $width;
	
	public function setWidth($value)
	{
		$this->width = $value; return $this;
	}
	
	public function getWidt()
	{
		return $this->width;
	}	
	
	/**
	 * @ORM\Column(nullable=true, type="integer")
	 */
	protected $height;
	
	public function setHeight($value)
	{
		$this->height = $value; return $this;
	}
	
	public function getHeight()
	{
		return $this->height;
	}
	
	
	
}

