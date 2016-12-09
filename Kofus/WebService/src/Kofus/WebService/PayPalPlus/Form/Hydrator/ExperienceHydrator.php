<?php
namespace Kofus\WebService\PayPalPlus\Form\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface;

class ExperienceHydrator implements HydratorInterface
{

    public function extract($object)
    {
        $array = array(
            'master' => array(
                'id' => $object->id,
                'name' => $object->name
            )
        );
        
        if (isset($object->flow_config)) {
            if (isset($object->flow_config->landing_page_type))
                $array['flow_config']['landing_page_type'] = $object->flow_config->landing_page_type;
            if (isset($object->flow_config->bank_txn_pending_url))
                $array['flow_config']['bank_txn_pending_url'] = $object->flow_config->bank_txn_pending_url;
            if (isset($object->flow_config->user_action))
                $array['flow_config']['user_action'] = $object->flow_config->user_action;
        }
        
        if (isset($object->input_fields)) {
            if (isset($object->input_fields->allow_note))
                $array['input_fields']['allow_note'] = $object->input_fields->allow_note;
            if (isset($object->input_fields->no_shipping))
                $array['input_fields']['no_shipping'] = $object->input_fields->no_shipping;
            if (isset($object->input_fields->address_override))
                $array['input_fields']['address_override'] = $object->input_fields->address_override;
        }
        
        if (isset($object->presentation)) {
            if (isset($object->presentation->brand_name))
                $array['presentation']['brand_name'] = $object->presentation->brand_name;
            if (isset($object->presentation->logo_image))
                $array['presentation']['logo_image'] = $object->presentation->logo_image;
            if (isset($object->presentation->locale_code))
                $array['presentation']['locale_code'] = $object->presentation->locale_code;
        }
        
        return $array;
    }

    public function hydrate(array $data, $object)
    {
        $object['name'] = $data['master']['name'];
        $object['flow_config'] = $data['flow_config'];
        $object['input_fields'] = $data['input_fields'];
        $object['presentation'] = $data['presentation'];
        
        return $object;
    }
}