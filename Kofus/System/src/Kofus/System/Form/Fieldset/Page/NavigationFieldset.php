<?php
namespace Kofus\System\Form\Fieldset\Page;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Filter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NavigationFieldset extends Fieldset implements InputFilterProviderInterface, ServiceLocatorAwareInterface
{
	public function init()
	{
	    $this->setLabel('Menu Item');
	    
		$el = new Element\Text('nav_label', array('label' => 'Label'));
		$el->setAttribute('placeholder', 'will be generated automatically when empty');
		$this->add($el);
		
		$config = $this->getServiceLocator()->get('KofusConfig');
		$navService = $this->getServiceLocator()->get('KofusNavigationService');
		$indent = '-----';
		$positions = array();
		foreach ($config->get('navbars') as $id => $navConfig) {
		    if (isset($navConfig['system'])) continue;
		    $nav = $navService->setNavbar($id)
                ->excludeDisabledPages(false)
                ->excludeInvisiblePages(false)
                ->getContainer();
		    $positions[$id] = $navConfig['title'];
		    
		    $iterator = new \RecursiveIteratorIterator($nav, \RecursiveIteratorIterator::SELF_FIRST);
		    foreach ($iterator as $page)
		        $positions[$page->get('node-id')] = str_repeat('.....', $iterator->getDepth() + 1) .  ' ' . $page->getLabel();
		    
		    //foreach ($nav->getPages() as $page) 
		      //  $positions[$page->get('node-id')] = $page->getLabel();
		}
		
		$el = new Element\Select('position', array('label' => 'Position'));
		$el->setValueOptions($positions);
		$this->add($el);
		
	    $el = new Element\Text('uri_segment', array('label' => 'URL Segment'));
		$el->setAttribute('placeholder', 'will be generated automatically when empty');
		$this->add($el);
		
		$el = new Element\Number('priority', array('label' => 'Priority'));
		$this->add($el);
		
		$el = new Element\Checkbox('nav_visible', array('label' => 'visible?'));
		$this->add($el);
	}
	
	protected $sm;
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->sm = $serviceLocator;
	}
	
	public function getServiceLocator()
	{
		return $this->sm;
	}
	

	
	public function getInputFilterSpecification()
	{
	    $trim = new Filter\StringTrim();
	    $null = new Filter\ToNull();
		return array(
		    'label' => array(
		        'required' => false,
		        'filters' => array($trim, $null)
		    ),
		    'uri_segment' => array(
		        'required' => false,
		        'filters' => array($trim, $null)
		    ),
		);
	}
	
	
}

