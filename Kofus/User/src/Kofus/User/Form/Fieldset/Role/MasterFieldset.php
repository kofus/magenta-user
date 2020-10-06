<?php
namespace Kofus\User\Form\Fieldset\Role;

use Zend\Form\Fieldset;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MasterFieldset extends Fieldset implements InputFilterProviderInterface, ServiceLocatorAwareInterface
{

    public function init()
    {
        $el = new Element\Text('name', array(
            'label' => 'Name'
        ));
        $this->add($el);
        
        $el = new Element\Select('parent', array(
            'label' => 'Inherits from',
            'value_options' => $this->getValueOptions(),
            'empty_option' => ''
        
        ));
        $this->add($el);
    }

    public function getInputFilterSpecification()
    {
        return array(
            'name' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'stringtrim')
                )
            ),
            'parent' => array(
                'required' => false
            )
        );
    }

    protected function getValueOptions()
    {
        $values = array();
        foreach ($this->nodes()
            ->getRepository('UR')
            ->findAll() as $entity) {
            $values[$entity->getNodeId()] = (string) $entity;
        }
        return $values;
    }

    protected function nodes()
    {
        return $this->getServiceLocator()->get('KofusNodeService');
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
}
