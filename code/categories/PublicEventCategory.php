<?php
/**
 * Public Event Category
 * 
 * @package calendar
 * @subpackage categories
 */
class PublicEventCategory extends EventCategory {
	
	public function ComingEvents($from=false){
		$events = $this->Events()
			->filter(array(
					'StartDateTime:GreaterThan' => date('Y-m-d', $from ? strtotime($from) : time())
				)
			);
		return $events;
	}
}