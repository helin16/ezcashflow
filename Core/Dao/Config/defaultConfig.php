<?php
return array(
				'Database' => array(
						'Driver' => 'mysql',
						//'LoadBalancer' => 'wordpress.ckhuf3oqhprr.ap-southeast-2.rds.amazonaws.com',
						'LoadBalancer' => 'localhost',
						'CoreDatabase' => 'ezbk',
						'Username' => 'root',
						'Password' => 'root'
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
