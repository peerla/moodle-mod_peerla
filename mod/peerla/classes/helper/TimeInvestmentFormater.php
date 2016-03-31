<?php
namespace mod_kom_peerla;

/**
 * Helper class for formating the time investment values.
 *
 * @author Christoph Bohr
 */
class TimeInvestmentFormater {
	
	/**
	 * Get a string representing the given minutes in a user friendly format.
	 * 
	 * @param int $value Time investment in minutes
	 * @return string
	 */
	public function formatForOutput($value){
		$string = '';
		
		$hours = $this->getHourPart($value);
		$minutes = $this->getMinutePart($value);
		
		if ($hours > 0){
			$string .= $hours;
			$string .= ' ';
			if ($hours == 1){
				$string .= get_string('hour','peerla');
			}
			else{
				$string .= get_string('hours','peerla');
			}
		}
		
		if ($minutes > 0 || $hours == 0){
			if ($hours > 0){
				$string .= ', ';
			}
			$string .= $minutes;
			$string .= ' '.get_string('minutes','peerla');
		}
		
		return $string;
	}
	
	/**
	 * Get the number of full hours from the time investment
	 * 
	 * @param int $value Time investment in minutes
	 * @return int Number of (full) hours
	 */
	public function getHourPart($value){
		return floor($value / 60);
	}
	
	/**
	 * Get the number of minutes after all full hours have been subtracted.
	 * 
	 * @param int $value Time investment in minutes
	 * @return int Number of minutes
	 */
	public function getMinutePart($value){
		return floor($value % 60);
	}
	
}
