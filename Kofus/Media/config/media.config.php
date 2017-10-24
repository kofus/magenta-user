<?php
return array(
    'media' => array(
        'image' => array(
            'displays' => array(
                
                'available' => array(
                    'thumb' => array(
                        'filters' => array(
                            array(
                                'name' => 'ImageFormat',
                                'options' => array(
                                    'format' => 'png'
                                )
                            ),
                            array(
                                'name' => 'Resize',
                                'options' => array(
                                    'width' => 120,
                                    'height' => 120
                                )
                            )
                        )
                    ),
                )
            )
        )
    )
);