<?php
namespace Kofus\System\Cron;

interface CronInterface
{
    public function setSpecification(array $spec);
    public function getSpecification();
    public function setStoreParams(array $params);
    public function getStoreParams();
    public function run();
}