<?php
namespace Kofus\WebService\Service;
use Kofus\System\Service\AbstractService;

class WebService extends AbstractService
{
    public function ppplus()
    {
        $service = new \Kofus\WebService\PayPalPlus\PayPalPlusService();
        $service->setServiceLocator($this->getServiceLocator());
        return $service;
    }
}