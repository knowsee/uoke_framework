<?php declare(strict_types = 1);
if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}
class Cache_Redis {

	private $link = NULL;
	private $config = array();

	public function __construct(array $config) {
		/**
		 * Building Next Time
		 */
		return $this;
	}

}