<?php

namespace Kofus\WebService\PayPalPlus\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Kofus\WebService\PayPalPlus\Form\Fieldset\Experience;
use Kofus\WebService\PayPalPlus\Form\Hydrator\ExperienceHydrator;

class PaymentController extends AbstractActionController
{
    public function listAction()
    {
        $this->archive()->uriStack()->push();
        $payments = $this->webservice()->ppplus()->api('GET', 'v1/payments/payment');
        return new ViewModel(array(
        	'payments' => $payments->payments
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
                print_r($response); die();
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
