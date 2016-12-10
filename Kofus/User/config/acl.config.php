<?php
return array(
		'user' => array(
				'acl' => array(
						'roles' => array(
								'Guest' => array(),
								'Member' => array('Guest'),
								'Editor' => array('Member'),
								'Staff' => array('Editor'),
								'Administrator' => array('Staff')
						),
				)
		)
		
);