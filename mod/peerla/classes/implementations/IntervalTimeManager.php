<?php
namespace mod_kom_peerla;

/**
 * Description of IntervalTimeManager
 *
 * @author Christoph Bohr
 */
class IntervalTimeManager {
	
	/**
	 * Returns the day of the week each interval ends.
	 * 
	 * The day is returned as a number from 1 (Mo.) to 7 (Su.)
	 * 
	 * @return int Day of the week
	 */
	protected function getIntervalEndWeekDay(){
		return 7;
	}
	
	/**
	 * Get the minimal length in days of an interval.
	 * 
	 * @return int Number of days
	 */
	protected function getMinIntervalLength(){
		return 4;
	}


	/**
	 * Returns the interval end as an unix time stamp for an interval which is
	 * created at the given unix time stamp.
	 * 
	 * @param int $intervalCreateTime Unix time stamp for interval creation.
	 */
	public function getIntervalEndTime($intervalCreateTime){
		$createWeekDay = date('N',$intervalCreateTime);
		$createDateDay = strtotime(date('Y-m-d 00:00:00',$intervalCreateTime));
		
		//if current day of the week > end day of week => add 7 to get the
		//number of days until the next end day
		$dayDiff = ($this->getIntervalEndWeekDay() - $createWeekDay + 7) % 7;
		
		//shorter than min day lenght? => add a week
		if ($dayDiff < $this->getMinIntervalLength()){
			$endDay = $createDateDay + 60*60*24* ($dayDiff + 7);
		}
		else{
			$endDay = $createDateDay + 60*60*24* $dayDiff;
		}
		
		return strtotime(date('Y-m-d 23:59:59', $endDay));
	}
	/**
	 * Returns the interval start as an unix time stamp for an interval which is
	 * created at the given unix time stamp.
	 * 
	 * @param int $intervalCreateTime Unix time stamp for interval creation.
	 */
	public function getIntervalStartTime($intervalCreateTime){
		$createDateDay = strtotime(date('Y-m-d 00:00:00',$intervalCreateTime));
		return $createDateDay;
	}
	
}
