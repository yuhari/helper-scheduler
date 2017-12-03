<?php
include "../../vendor/autoload.php" ;

\scheduler\TaskFactory::init('\scheduler\test') ;

(new \scheduler\Scheduler())->send(123)
	->task(\scheduler\TaskFactory::factory('TestTask'))
	->serialTask(\scheduler\TaskFactory::factory('TestTask'))
	->send('calors')
	->task(new \scheduler\test\TestTask())
	->processCallback(function(){
		echo "callback\n" ;
		echo 'process' . "\n" ;
	})->run() ;