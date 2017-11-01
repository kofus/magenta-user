<?php

namespace Kofus\System\Form\Element;

use Traversable;
use Zend\InputFilter\InputProviderInterface;
use Zend\Form\Element\Select as ZendSelect;

class NodeSelect extends ZendSelect implements InputProviderInterface
{
	
	protected static $ajaxUrl;
	
	public static function setDefaultAjaxUrl($url)
	{
		self::$ajaxUrl = $url;
	}
	
	public static function getDefaultAjaxUrl()
	{
		return self::$ajaxUrl;
	}
	
	
	public function __construct($name = null, $options = array())
	{
		parent::__construct($name, $options);
		$this->setAttribute('class', 'node-select');
		
		if ($this->getOption('node-type')) {
			$url = self::getDefaultAjaxUrl();
			$url = urldecode($url);
				
			$filter = new \Kofus\System\Filter\SubstitutionFilter();
			$filter->setParam('node_type', $this->getOption('node-type'));
			$url = $filter->filter($url);
			$this->setAttribute('data-ajax--url', $url);

		}
	}
	
    public function setValue($value)
    {
    	$this->value = $value;
        $options = array($this->getValueOptions());
        if ($value && ! isset($options[$value])) {
        	if ($this->getOption('node-type') == 'LANGUAGE') {
        		$languages = self::$sm->get('KofusConfig')->get('nodes.available.LANGUAGE.values');
        		$options[$value] = \Locale::getDisplayLanguage($value, \Locale::getDefault());
        		
        	} elseif ($this->getOption('node-type') == 'COUNTRY') {
        		$countries = self::$sm->get('KofusConfig')->get('nodes.available.COUNTRY.values');
        		$options[$value] = \Locale::getDisplayRegion('-' . $value, \Locale::getDefault());
        		
        	} elseif (self::$sm->get('KofusNodeService')->isNode($value)) {
        	    $node = self::$sm->get('KofusNodeService')->getNode($value);
        	    $options[$value] = (string) $node;
        	} else {
        		$options[$value] = $value . ' [neu]';
        	}
        	
        	
            $this->setValueOptions($options);
        }
    	return $this;
    }
    
    protected function getOptionValue($key, $optionSpec)
    {
        if (is_array($optionSpec) && array_key_exists('value', $optionSpec))
            return $optionSpec['value'];
        return $key;
    }
    
    protected static $sm;
    
    public static function setDefaultServiceLocator($sm)
    {
    	self::$sm = $sm;
    }
    
   
    
    
}
