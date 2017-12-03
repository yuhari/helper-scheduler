<?php
/**
 * 
 *
 * @author yuhari
 * @version $Id$
 * @copyright , 3 December, 2017
 * @package default
 */

/**
 * task factory
 */
namespace scheduler ;

class TaskFactory {
	
	public static $task_namespace = '' ;
	
	public static function init($namespace) {
		static::$task_namespace = $namespace ;
	}
	
	public static function factory($class_name) {
		$class = static::$task_namespace . "\\$class_name" ;
		if (class_exists($class)) {
			$task = new $class ;
			return $task ;
		} else {
			throw new \Exception("Class [$class] noe exists.") ;
		}
	}
}
