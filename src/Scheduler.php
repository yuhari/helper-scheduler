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
 * schedule app
 */
namespace scheduler ;

class Scheduler {
	
	private $tasks = [] ;
	
	private $arguments = [] ;
	
	//scheduler callback
	private $callback = null ;
	
	private $error = null ;
	
	//task callback
	private $taskCallback = null ;
	
	private $taskError = null ;
	
	//process callback
	private $processCallback = null ;
	
	private $processError = null ;
	
	public $schedulerNumber = 0 ;
	
	public function __construct() {
		$this->schedulerNumber = rand(0, 1000) ;
	}
 	
	public function send($arguments = []) {
		$args = is_array($arguments) ? $arguments : func_get_args() ;
		
		if (!empty($args)) $this->arguments = $args ;
		
		return $this ;
	}
	
	public function task(Task $action) {
		$this->parallelTask($action) ;
		
		return $this ;
	}
	
	//串行任务，在同一线程处理
	public function serialTask(Task $task) {
		if ($task instanceof Task) {
			$this->tasks[] = [0 , $task, $this->arguments] ;
		}
		return $this ;
	}
	
	//并行任务，单独线程处理
	public function parallelTask(Task $task) {
		if ($task instanceof Task) {
			$this->tasks[] = [1, $task, $this->arguments] ;
		}
		return $this ;
	}
	
	//callback
	public function callback(\Closure $callback) {
		if (is_callable($callback)) {
			$this->callback = $callback ;
		}
		return $this ;
	}
	
	//error
	public function error(\Closure $callback) {
		if (is_callable($callback)) {
			$this->error = $callback ;
		}
		return $this ;
	}
	
	// 单个task 的callback
	public function taskCallback(\Closure $callback) {
		if (is_callable($callback)) {
			$this->taskCallback = $callback ;
		}
		return $this ;
	}
	
	public function taskError(\Closure $callback) {
		if (is_callable($callback)) {
			$this->taskError = $callback ;
		}
		return $this ;
	}
	
	// 单个线程 的 callback
	public function processCallback(\Closure $callback) {
		if (is_callable($callback)) {
			$this->processCallback = $callback ;
		}
		return $this ;
	}
	
	public function processError(\Closure $callback) {
		if (is_callable($callback)) {
			$this->processError = $callback ;
		}
		return $this ;
	}
 	
	//开始处理这些tasks
	public function run() {
		$pendings = $this->tasks ;
		
		$parallels = [] ; $i = 0 ;
		
		foreach($pendings as $penging) {
			list($flag, $task, $arguments) = $penging ;
			if ($flag === 1) {
				$i ++ ;
				$parallels[$i][] = [$task, $arguments] ;
			}elseif ($flag === 0) {
				$parallels[$i][] = [$task, $arguments] ;
			}
		}
		
		foreach($parallels as $serial) {
			if (!empty($serial)) {
				if (pcntl_fork() == 0) {
					try {
						foreach($serial as $handler) {
							list($task, $params) = $handler ;
							if ($task instanceof Task) {
								
								if (function_exists('cli_set_process_title') && PHP_OS != 'Darwin') {
									cli_set_process_title($task->name) ;
								}
								try{
									$task->options = $params ;
									$task->_init() ;
								
									$task->execute() ;
									
									if (!empty($this->taskCallback)) {
										$callback = $this->taskCallback ;
										$res = $callback($task) ;
									}
									
								}catch(\Exception $e) {
									if (!empty($this->taskError)){
										$error = $this->taskError ;
										$error($task) ;
									}
								}
							}
						}
						
						if (!empty($this->processCallback)) {
							$callback = $this->processCallback ;
							$res = $callback() ;
						}
					} catch (\Exception $e) {
						if (!empty($this->processError)) {
							$error = $this->processError ;
							$error() ;
						}
					} finally {
						exit(0) ;
					}
				}
			}
		}
		
		if (!empty($this->callback)){
			$callback = $this->callback ;
			$callback($this) ;
		}
	}
	
}