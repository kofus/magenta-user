<?php
namespace Kofus\User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AclController extends AbstractActionController
{
    public function indexAction()
    {
        $this->archive()->uriStack()->push();
        $aclService = $this->getServiceLocator()->get('KofusAclService');
        
        $isAllowed = null;
        
        $fieldset = new \Kofus\User\Form\Fieldset\Acl\TestFieldset('test');
        $fieldset->setAcl($aclService);
        $form = $this->formBuilder()
            ->setLabelSize('col-sm-3')->setFieldSize('sm-9')
            ->addFieldset($fieldset)
            ->buildForm();
        
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $isAllowed = $this->user()->acl()->isAllowed(
                    $form->get('test')->get('role')->getValue(),
                    $form->get('test')->get('resource')->getValue(),
                    $form->get('test')->get('privilege')->getValue()
                );
            }
        }
        
        return new ViewModel(array(
            'form' => $form->prepare(),
            'isAllowed' => $isAllowed,
            'aclService' => $aclService
        ));
        
    }
    
}
