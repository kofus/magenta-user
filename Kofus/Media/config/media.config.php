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
                                    'format' => 'jpg'
                                )
                            ),
                            array(
                                'name' => 'Resize',
                                'options' => array(
                                    'width' => 160,
                                    'height' => 160
                                )
                            )
                        )
                    ),
                )
            )
        )
    )
);