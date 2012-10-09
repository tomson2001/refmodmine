<?php
interface IMapping {
	
	public function __construct(EPC $epc1, EPC $epc2);
	public function setParams(Array $params);
	public function map();
	
}
?>