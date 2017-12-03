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
 * scheduler task
 */
namespace scheduler ;

abstract class Task {
	public $name = __CLASS__ ;
	
	public $options = [] ;
	
	public function _init() {
		$this->name = get_class($this) ;
	}
	
	abstract public function execute() ;
}
