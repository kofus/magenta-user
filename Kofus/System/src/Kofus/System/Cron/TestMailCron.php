<?php
namespace Kofus\System\Cron;
use Kofus\System\Cron\AbstractCron;
use Zend\Mail;
use Zend\Mime;

class TestMailCron extends AbstractCron
{
    public function run()
    {
        $html = new Mime\Part('test');
        $html->type = 'text/html';
        
        $mimeBody = new Mime\Message();
        $mimeBody->setParts(array($html));
        
        $mail = new Mail\Message();
        $mail->setSubject('[' . $_SERVER['HTTP_HOST'] . '] Testmail');
        $mail->addTo('log@kofus.de');
        $mail->setBody($mimeBody);
        //$mail->setFrom('log@kofus.de', 'Fehler-Manager');
        
        $transport = new Mail\Transport\Sendmail();
        $transport->send($mail);
        
        return 'completed';
    }
    

}