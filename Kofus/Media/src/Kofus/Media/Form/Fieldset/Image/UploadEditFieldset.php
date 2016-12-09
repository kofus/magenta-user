<?php
namespace Kofus\Media\Form\Fieldset\Image;


use Zend\Validator;


class UploadEditFieldset extends UploadFieldset
{
    public function getInputFilterSpecification()
    {
        $mimeType = new Validator\File\MimeType(array('mimeType' => $this->getImageTypes('image/')));
        $filesize = new Validator\File\Size(array('max' => $this->getMaxFilesize() . 'MB'));
        
        
        $spec = array(
            'file' => array(
            		'required' => false,
            		 'validators' => array(
            		 		
            		 )
            ),
            //'enabled' => array('required' => false)
        );

        // Hack: allow empty file in this case
        if (isset($_FILES['upload']['error']['file']) && 4 == $_FILES['upload']['error']['file'])
        	return $spec;
        
       	$spec['file']['validators'][] = $mimeType;
       	$spec['file']['validators'][] = $filesize;
        return $spec;
    }
}
