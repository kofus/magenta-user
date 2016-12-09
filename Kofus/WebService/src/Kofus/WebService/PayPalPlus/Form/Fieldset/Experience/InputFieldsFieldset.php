<?php

namespace Kofus\WebService\PayPalPlus\Form\Fieldset\Experience;

use Zend\Form\Fieldset;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;

class InputFieldsFieldset extends Fieldset
{
	public function init()
	{
	    $this->setName('input_fields');
	    $this->setLabel('Input Fields');
	    
		$el = new Element\Select('allow_note', array('label' => 'Allow note?'));
		$el->setValueOptions(array('false' => 'false', 'true' => 'true'));
		$el->setOption('help-block', 'Enables the buyer to enter a note to the merchant on the PayPal page during checkout.');
		$this->add($el);
		
		$el = new Element\Select('no_shipping', array('label' => 'No shipping?'));
		$el->setValueOptions(array(0 => 0, 1 => 1, 2 => 2));
		$el->setOption('help-block', "Determines whether PayPal shows shipping address fields on the experience pages. Allowed values: 0, 1, or 2. When set to 0, PayPal displays the shipping address on the PayPal pages. When set to 1, PayPal does not display shipping address fields whatsoever. When set to 2, if you do not pass the shipping address, PayPal obtains it from the buyer's account profile. For digital goods, this field is required, and you must set it to 1. Possible types: integer");
		$this->add($el);
		
		$el = new Element\Select('address_override', array('label' => 'Address override?'));
		$el->setValueOptions(array(0 => 0, 1 => 1));
		$el->setOption('help-block', "Determines whether the PayPal pages show the shipping address supplied in this call, rather than the shipping address on file with PayPal for this buyer. Displaying the address on file does not allow the buyer to edit the address. Allowed values: 0 or 1. When set to 0, the PayPal pages should display the address on file. When set to 1, the PayPal pages should display the addresses supplied in this call instead of the address from the buyer's PayPal account. Possible types: integer");
		$this->add($el);
	}

}
