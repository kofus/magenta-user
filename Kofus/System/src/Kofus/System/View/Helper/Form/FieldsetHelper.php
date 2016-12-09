<?php

namespace Kofus\System\View\Helper\Form;
use Zend\View\Helper\AbstractHelper;

class FieldsetHelper extends AbstractHelper
{
    protected $fieldset;
    
    public function __invoke($fieldset)
    {
    	$this->fieldset = $fieldset; return $this;
    }
    
    public function __toString()
    {
        $sFormContent = '<fieldset class="form-horizontal">';
        $bHasColumnSizes = false;
        $sFormLayout = 'horizontal';
        foreach($this->fieldset as $oElement){
        	$aOptions = $oElement->getOptions();
        	if (!$bHasColumnSizes && !empty($aOptions['column-size'])) {
        		$bHasColumnSizes = true;
        	}
        	//Define layout option to form elements if not already defined
        	if($sFormLayout && empty($aOptions['twb-layout'])){
        		$aOptions['twb-layout'] = $sFormLayout;
        		$oElement->setOptions($aOptions);
        	}
        		
        	if ($oElement instanceof \Zend\Form\FieldsetInterface) {
        		$sFormContent .= $this->getView()->formCollection($oElement);
        	} else {
        		$sFormContent .= $this->getView()->formRow($oElement);
        	}
        }
        if ($bHasColumnSizes && $sFormLayout !== 'horizontal') {
        	$sFormContent = sprintf('<div class="row">%s</div>', $sFormContent);
        }
        
        $sFormContent .= '</fieldset>';
        
    	return $sFormContent;
    }
}