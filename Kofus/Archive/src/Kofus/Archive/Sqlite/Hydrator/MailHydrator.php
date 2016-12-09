<?php
namespace Kofus\Archive\Sqlite\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Mail\Headers;

class MailHydrator implements HydratorInterface
{
	public function extract($object)
	{
	    $data = array(
    	    'encoding'         => $object->getEncoding(),
    	    'headers'          => $object->getHeaders()->toString(),
    	    'body'             => serialize($object->getBody()),
	        'body_text'        => $object->getBodyText()
        );
		return $data;
	}

	public function hydrate(array $data, $object)
	{
	    $object->setEncoding($data['encoding']);
	    
	    if ($data['headers']) {
	        $headers = Headers::fromString($data['headers']);
	        try {
	           $headers->get('Sender');
	        } catch (\Exception $e) {
	            $headers->removeHeader('Sender');
	        }
           $object->setHeaders($headers);
	    }

	    /*
	    if ($data['body']) {
	        $body = unserialize($data['body']);
	        if ($body)
                $object->setBody($body);
	    } */
		return $object;
	}
}