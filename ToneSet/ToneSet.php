<?php 

interface ToneSet
{
	public function addTones();
	public function getToneSet();
	public function permute();
	public function retrograde();
	public function truncate();
	public function invert();
	public function transpose();
	public function filterByRange();
	public function extendRange();
}
?>
