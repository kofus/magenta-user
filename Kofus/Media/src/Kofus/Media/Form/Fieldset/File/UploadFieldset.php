<?php
namespace Kofus\Media\Form\Fieldset\File;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\Filter;
use Zend\Validator;

class UploadFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function init()
    {
        $this->setName('upload');
        $this->setLabel('Upload');
        
        $el = new Element\File('file', array(
            'label' => 'Image File'
        ));
        $el->setAttribute('id', 'file');
        $el->setOption('help-block', 'max. ' . $this->getMaxFilesize() . 'MB');
        $this->add($el);
    }

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
        }
        ;
        return $mb;
    }

    /**
     * Get max filesize (upload_max_filesize / post_max_size)
     * 
     * @return int
     */
    protected function getMaxFilesize()
    {
        $limitUpload = $this->parseSize(ini_get('upload_max_filesize'));
        $limitPost = $this->parseSize(ini_get('post_max_size'));
        
        if ($limitUpload < $limitPost)
            return $limitUpload;
        return $limitPost;
    }

    public function getInputFilterSpecification()
    {
        return array(
            'file' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\File\Size',
                        'options' => array(
                            'max' => $this->getMaxFilesize() . 'MB'
                        )
                    ),
                )
            )
        );
    }
}
