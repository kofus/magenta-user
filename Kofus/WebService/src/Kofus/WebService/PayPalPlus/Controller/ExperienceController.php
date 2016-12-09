<?php

namespace Kofus\WebService\PayPalPlus\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Kofus\WebService\PayPalPlus\Form\Fieldset\Experience;
use Kofus\WebService\PayPalPlus\Form\Hydrator\ExperienceHydrator;

class ExperienceController extends AbstractActionController
{
    public function listAction()
    {
        $this->archive()->uriStack()->push();
        $experiences = $this->webservice()->ppplus()->api('GET', 'v1/payment-experience/web-profiles');
        return new ViewModel(array(
        	'experiences' => $experiences
        ));
    }
    
    public function addAction()
    {
    	$hydrator = new ExperienceHydrator();
    	
    	$form = $this->formBuilder()
	    	->setLabelSize('col-sm-3')->setFieldSize('sm-9')
	    	->addFieldset(new Experience\MasterFieldset())
	    	->addFieldset(new Experience\FlowConfigFieldset())
	    	->addFieldset(new Experience\InputFieldsFieldset())
	    	->addFieldset(new Experience\PresentationFieldset())
	    	->buildForm()
	    	->add(new \Zend\Form\Element\Submit('submit', array('label' => 'Save')));
    	
    	if ($this->getRequest()->isPost()) {
    		$form->setData($this->getRequest()->getPost());
    	
    		if ($form->isValid()) {
    			$requestParams = $hydrator->hydrate($form->getData(), array());
    			$response = $this->webservice()->ppplus()->api('POST', 'v1/payment-experience/web-profiles', $requestParams);
    			return $this->redirect()->toRoute('kofus_webservice_ppplus', array('controller' => 'experience', 'action' => 'list'), true);
    		}
    	
    	}
    	
    	return new ViewModel(array(
    			'form' => $form->prepare(),

    	));    	
    }
    
    public function editAction()
    {
        $experience = $this->webservice()->ppplus()->api('GET', 'v1/payment-experience/web-profiles/' . $this->params('id'));
        $experienceId = $experience->id;
        
        $hydrator = new ExperienceHydrator();
        
        $form = $this->formBuilder()
            ->setLabelSize('col-sm-3')->setFieldSize('sm-9')
            ->addFieldset(new Experience\MasterFieldset())
            ->addFieldset(new Experience\FlowConfigFieldset())
            ->addFieldset(new Experience\InputFieldsFieldset())
            ->addFieldset(new Experience\PresentationFieldset())
            ->buildForm()
            ->add(new \Zend\Form\Element\Submit('submit', array('label' => 'Save')));
        
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            
            if ($form->isValid()) {
                $requestParams = $hydrator->hydrate($form->getData(), array());
                $response = $this->webservice()->ppplus()->api('PUT', 'v1/payment-experience/web-profiles/' . $experienceId, $requestParams);
                return $this->redirect()->toRoute('kofus_webservice_ppplus', array('controller' => 'experience', 'action' => 'list'), true);
                
            }
            
        } else {
            $form->setData($hydrator->extract($experience));            
        }
        
        return new ViewModel(array(
        	'form' => $form->prepare(),
            'experience' => $experience
            
        ));
    }
    
}
