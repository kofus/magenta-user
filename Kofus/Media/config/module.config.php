<?php

namespace Kofus\Media;

return array (
		'controllers' => array (
				'invokables' => array (
						'Kofus\Media\Controller\Admin' => 'Kofus\Media\Controller\AdminController',
						'Kofus\Media\Controller\Image' => 'Kofus\Media\Controller\ImageController', 
						'Kofus\Media\Controller\Pdf' => 'Kofus\Media\Controller\PdfController', 
				) 
		),
		
		'doctrine' => array (
				'driver' => array (
						__NAMESPACE__ . '_driver' => array (
								'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
								'cache' => 'array',
								'paths' => array (
										__DIR__ . '/../src/' . str_replace ( '\\', '/', __NAMESPACE__ ) . '/Entity' 
								) 
						),
						'orm_default' => array (
								'drivers' => array (
										__NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver' 
								) 
						) 
				) 
		),
    
        'listeners' => array('KofusMediaListener'),
		
		'service_manager' => array (
				'invokables' => array (
						'KofusMediaService' => 'Kofus\Media\Service\MediaService',
				        'KofusMediaListener' => 'Kofus\Media\Listener\MediaListener'
				) 
		),
		
		'nodes' => array (
				'available' => array (
						'IMG' => array (
								'label' => 'Image',
								'entity' => 'Kofus\Media\Entity\ImageEntity',
								'controllers' => array (
										'Kofus\Media\Controller\Image' 
								),
								'form' => array (
										'add' => array (
												'fieldsets' => array (
														'upload' => array (
																'class' => 'Kofus\Media\Form\Fieldset\Image\UploadFieldset',
																'hydrator' => 'Kofus\Media\Form\Hydrator\Image\UploadHydrator' 
														) 
												) 
										),
										'edit' => array (
												'fieldsets' => array (
														'upload' => array (
																'class' => 'Kofus\Media\Form\Fieldset\Image\UploadEditFieldset',
																'hydrator' => 'Kofus\Media\Form\Hydrator\Image\UploadEditHydrator' 
														) 
												) 
										) 
								)
								 
						),
						'PDF' => array (
								'label' => 'PDF',
						        'label_pl' => 'PDFs',
								'entity' => 'Kofus\Media\Entity\PdfEntity',
								'controllers' => array (
										'Kofus\Media\Controller\Pdf' 
								),
								'form' => array (
										'add' => array (
												'fieldsets' => array (
														'upload' => array (
																'class' => 'Kofus\Media\Form\Fieldset\Pdf\UploadFieldset',
																'hydrator' => 'Kofus\Media\Form\Hydrator\Pdf\UploadHydrator' 
														) 
												) 
										),
										'edit' => array (
												'fieldsets' => array (
														'upload' => array (
																'class' => 'Kofus\Media\Form\Fieldset\Pdf\UploadEditFieldset',
																'hydrator' => 'Kofus\Media\Form\Hydrator\Pdf\UploadEditHydrator' 
														) 
												) 
										) 
								)
								 
						) 
				)
				 
		)
		,
		
		'view_manager' => array (
				'template_path_stack' => array (
						'Media' => __DIR__ . '/../view' 
				),
				'controller_map' => array (
						'Kofus\Media' => true 
				),
				'module_layouts' => array (
						'Kofus\Media' => 'kofus/layout/admin' 
				) 
		),
		
		'view_helpers' => array (
				'invokables' => array (
						'media' => 'Kofus\Media\View\Helper\MediaHelper',
						'fileSize' => 'Kofus\Media\View\Helper\FileSizeHelper' 
				) 
		),
		
		'controller_plugins' => array (
				'invokables' => array (
						'media' => 'Kofus\Media\Controller\Plugin\MediaPlugin' 
				) 
		),
		
		'router' => array (
				'routes' => array (
						'kofus_media_image' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/cache/media/image/:display/:filename',
										'constraints' => array (),
										'defaults' => array (
												'__NAMESPACE__' => 'Kofus\Media\Controller',
												'controller' => 'image',
												'action' => 'process' 
										) 
								),
								'may_terminate' => true 
						),
						'kofus_media_pdf' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/cache/media/pdf/:filename',
										'constraints' => array (),
										'defaults' => array (
												'__NAMESPACE__' => 'Kofus\Media\Controller',
												'controller' => 'pdf',
												'action' => 'dump' 
										) 
								),
								'may_terminate' => true 
						),
				    'kofus_media' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/:language/' . KOFUS_ROUTE_SEGMENT . '/media/:controller/:action[/:id[/:id2]]',
										'constraints' => array (
												'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'language' => '[a-z][a-z]' 
										),
										'defaults' => array (
												'language' => 'de',
												'__NAMESPACE__' => 'Kofus\Media\Controller' 
										) 
								),
								'may_terminate' => true 
						) 
				)
				 
		),
		

		
)
;
