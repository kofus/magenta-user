<?php
namespace Kofus\Media\Form\Fieldset\Pdf;


use Zend\Validator;


class UploadEditFieldset extends UploadFieldset
{
    public function getInputFilterSpecification()
    {
        if (! isset($_FILES['file']))
            return array();
        
        $mimeType = new Validator\File\MimeType(array('mimeType' => array('application/pdf')));
        $filesize = new Validator\File\Size(array('max' => $this->getMaxFilesize() . 'MB'));
        
        return array(
            'file' => array(
            		'required' => false,
            		 'validators' => array(
            		 		$mimeType, $filesize
            		 )
            ),
        );
    }
}
