<?php

namespace Kofus\WebService\PayPalPlus\Form\Fieldset\Experience;

use Zend\Form\Fieldset;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;

class PresentationFieldset extends Fieldset implements InputFilterProviderInterface
{
	public function init()
	{
	    $this->setName('presentation');
	    $this->setLabel('Presentation');
	    
		$el = new Element\Text('brand_name', array('label' => 'Name'));
		$el->setOption('help-block', "A label that overrides the business name in the PayPal account on the PayPal pages. Character length and limitations: 127 single-byte alphanumeric characters.");
		$this->add($el);
		
		$el = new Element\Text('logo_image', array('label' => 'Logo Image'));
		$el->setOption('help-block', "A URL to logo image. Allowed media types: .gif, .jpg, or .png. Limit the image to 190 pixels wide by 60 pixels high. PayPal crops images that are larger. PayPal places your logo image at the top of the cart review area. PayPal recommends that you store the image on a secure (HTTPS) server. Otherwise, web browsers display a message that checkout pages contain non-secure items. Character length and limit: 127 single-byte alphanumeric characters.");
		$this->add($el);
		
		$locales = array('AU', 'AT', 'BE', 'BR', 'CA', 'CH', 'CN', 'DE', 'ES', 'GB', 'FR', 'IT', 'NL', 'PL', 'PT', 'RU', 'US');
		$el = new Element\Select('locale_code', array('label' => 'Locale'));
		$el->setValueOptions(array_combine($locales, $locales));
		$this->add($el);
	}

	public function getInputFilterSpecification()
	{
	    $trim = new \Zend\Filter\StringTrim();
	    $null = new \Zend\Filter\ToNull();
	    $uri = new \Zend\Validator\Uri(array('allowRelative' => false));
	    
		return array(
		    'brand_name' => array('required' => true, 'filters' => array($trim, $null)),
		    'logo_image' => array('required' => true, 'filters' => array($trim, $null), 'validators' => array($uri)),
		);
	}
}
