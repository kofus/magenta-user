<?php
namespace Kofus\System\Batch;


interface BatchInterface
{
    public function setSpecification(array $spec);
    public function getSpecification();
    public function setStoreParams(array $params);
    public function getStoreParams();
    public function run();
}