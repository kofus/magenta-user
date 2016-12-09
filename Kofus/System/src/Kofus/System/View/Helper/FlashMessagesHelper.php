<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;

class FlashMessagesHelper extends AbstractHelper
{
    public function __toString()
    {
		$flash = $this->view->flashMessenger();
		$flash->setAutoEscape(false);
        $flash->setMessageOpenFormat('<div%s>
				     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
         				&times;
     					</button>
     					');
        $flash->setMessageCloseString('</div>');
        $flash->setMessageSeparatorString($flash->getMessageCloseString() . $flash->getMessageOpenFormat());
        
        $s  = $flash->render('success', array('alert', 'alert-success', 'alert-dismissable'));
        $s .= $flash->render('info', array('alert', 'alert-info'));
        $s .= $flash->render('warning', array('alert', 'alert-warning'));
        $s .= $flash->render('error', array('alert', 'alert-danger'));
        return $s;
    }
}