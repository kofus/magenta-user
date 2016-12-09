<?php

namespace Kofus\WebService\PayPalPlus\Form\Fieldset\Experience;

use Zend\Form\Fieldset;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;

class FlowConfigFieldset extends Fieldset implements InputFilterProviderInterface
{
	public function init()
	{
	    $this->setName('flow_config');
	    $this->setLabel('Flow Configuration');
	    
		$el = new Element\Select('landing_page_type', array('label' => 'Landing Page Type'));
		$el->setValueOptions(array('Billing' => 'Billing', 'Login' => 'Login'));
		$el->setOption('help-block', 'Type of PayPal page to display when a user lands on the PayPal site for checkout. Allowed values: Billing or Login. When set to Billing, the Non-PayPal account landing page is used. When set to Login, the PayPal account login landing page is used.');
		$this->add($el);
		
		$el = new Element\Text('bank_txn_pending_url', array('label' => 'Bandk Pending URL'));
		$el->setOption('help-block', 'The URL on the merchant site for transferring to after a bank transfer payment. Use this field only if you are using giropay or bank transfer payment methods in Germany.');
		$this->add($el);
		
		$el = new Element\Text('user_action', array('label' => 'User Action'));
		$el->setOption('help-block', 'Determines whether buyers complete their purchases on PayPal or on merchant website.');
		$this->add($el);
		
		
	}

	public function getInputFilterSpecification()
	{
	    $trim = new \Zend\Filter\StringTrim();
	    $null = new \Zend\Filter\ToNull();
	    $uri = new \Zend\Validator\Uri(array('allowRelative' => false));
	    
		return array(
			'bank_txn_pending_url' => array('required' => false, 'filters' => array($trim, $null)),
		    'user_action' => array('required' => false, 'filters' => array($trim, $null)),
		);
	}
}
