<?php
namespace Kofus\Media\Form\Fieldset\Pdf;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\Filter;
use Zend\Validator;


class UploadFieldset extends Fieldset implements InputFilterProviderInterface
{
    protected function parseSize($s)
    {
        $int = Filter\StaticFilter::execute($s, 'Digits');
        $unit = Filter\StaticFilter::execute($s, 'Alpha');
        
        switch (strtoupper($unit)) {
        	case 'G':
        	    $mb = $int * 1024;
        	    break;
        	case 'M':
        	    $mb = $int;
        	    break;
        	default: // byte
        	    $mb = $int / 1024;
        };
        return $mb;
    }
    
    protected function getMaxFilesize()
    {
        $limitUpload = $this->parseSize(ini_get('upload_max_filesize'));
        $limitPost = $this->parseSize(ini_get('post_max_size'));
        
        if ($limitUpload < $limitPost)
            return $limitUpload;
        return $limitPost;
    }
    
    protected function getImageTypes($prefix='')
    {
        $types = array();
        foreach (array('pdf') as $type)
            $types[] = $prefix . $type;
        return $types;
    }

    public function init()
    {
    	$this->setName('upload');
    	$this->setLabel('Upload');
    	
        $el = new Element\File('file', array('label' => 'Pdf File'));
        $el->setAttribute('id', 'file');
        $el->setOption('help-block', 'max. ' . $this->getMaxFilesize() . 'MB (' . implode('/', $this->getImageTypes()) . ')');
        $this->add($el);
        
        //$el = new Element\Checkbox('enabled', array('label' => 'enabled?'));
        //$this->add($el);
        
    }

    public function getInputFilterSpecification()
    {
        $mimeType = new Validator\File\MimeType(array('mimeType' => array('application/pdf')));
        $filesize = new Validator\File\Size(array('max' => $this->getMaxFilesize() . 'MB'));
        
        return array(
            'file' => array(
            		'required' => true,
            		 'validators' => array(
            		 		$mimeType, $filesize
            		 )
            ),
            //'enabled' => array('required' => false)
        );
    }
}
