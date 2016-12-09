<?php

namespace Kofus\Archive\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class MailController extends AbstractActionController
{
    public function listAction()
    {
        $this->archive()->uriStack()->push();
        $mails = $this->archive()->mails()->getMails();
        $paginator = $this->paginator($mails);
    	return new ViewModel(array(
    		'paginator' => $paginator
    	));
    }
    
    public function viewAction()
    {
        return new ViewModel(array(
        	'mail' => $this->archive()->mails()->getMail($this->params('id'))
        ));
    }
    
    public function bodytextAction()
    {
        $mail = $this->archive()->mails()->getMail($this->params('id'));
        $body = $mail->getBodyText();
        $headers = $mail->getObject()->getHeaders();
        $contentType = $headers->get('Content-Type');
        
        if ('multipart/mixed' == $contentType->getType() && $contentType->getParameter('boundary')) {
            $msg = \Zend\Mime\Message::createFromMessage($body, $contentType->getParameter('boundary'));
            foreach ($msg->getParts() as $part) {
                if (strpos($part->getType(), 'text/html') === false)
                    continue;
                $s = $part->getContent();
                if ('base64' == $part->getEncoding())
                    $s = base64_decode($part->getContent());
            }
        } elseif ('text/plain' == $contentType->getType()) {
            $s = $body;
        } elseif ('text/html' == $contentType->getType()) {
            $s = $body;
            if ($headers->has('Content-Transfer-Encoding')) {
                if ('base64' == $headers->get('Content-Transfer-Encoding')->getFieldValue())
                    $s = base64_decode($body);
            }
        } else {
            $s = 'Mail of content type "' . $contentType->getType() . '" could not be displayed.';
        }

        return $this->getResponse()->setContent($s);
    }
    
}
