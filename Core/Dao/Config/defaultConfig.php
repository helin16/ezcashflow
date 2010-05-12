<?php
return array(
				'Database' => array(
						'Driver' => 'mysql',
						'LoadBalancer' => 'localhost',
						'ImportNode' => 'localhost',
						'SecondaryNode' => 'localhost',
						'NASNode' => 'localhost',
						'CoreDatabase' => 'ezcashflow',
						'Username' => 'root',
						'Password' => ''
					),
				'Profiler' => array(
								'SQL' => false,
								'Resources' => false
							),
				'theme'=> array(
					'name'=>'default'
					),
				'email'=> array(
					'contactUsReciever'=>'Administrator',
					'contactUsRecieverEmail'=>'helin16@gmail.com'
					),
				'time'=>array(
						'defaultTimeZone'=>'Australia/Melbourne'
					)
			);

?>