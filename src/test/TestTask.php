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
 * Define DocBlock
 */
namespace scheduler\test ;

class TestTask extends \scheduler\Task {
	
	public function execute() {
		$d = rand(1,3) ;
		echo "rd time $d\n" ;
		sleep($d) ;
		$p = $this->options[0] ;
		echo "output: $p\n" ;
	}
}