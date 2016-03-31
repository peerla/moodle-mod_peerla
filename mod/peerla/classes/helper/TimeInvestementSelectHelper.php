<?php
namespace mod_kom_peerla;

require_once realpath(__DIR__).'/TimeInvestmentFormater.php';

/**
 * Description of TimeInvestementSelectHelper
 *
 * @author Christoph Bohr
 */
class TimeInvestementSelectHelper {
	
	protected $expectedTimeInvestment;
	protected $selectedValue;

	/**
	 * Set the expected value for the time investmet. 
	 * 
	 * @param float $investment Time investment in minutes
	 */
	public function setExpectedTimeInvestment($investment){
		$this->expectedTimeInvestment = $investment;
	}
	
	/**
	 * Set the prefilled value.
	 * 
	 * @param int $value
	 */
	public function setSelectedValue($value){
		$this->selectedValue = $value;
	}

	public function getOptionHtmlString(){
		$values = $this->getSelectValues();
		
		$html = '';
		foreach($values as $value){
			$html .= $this->getSingleOptionString($value);
		}
		
		return $html;
	}
	
	/**
	 * Get the html string for a single option with the given value.
	 * 
	 * @param int $value Time in minutes
	 * @return string
	 */
	protected function getSingleOptionString($value){
		$selected = '';
		if (isset($this->selectedValue) && $this->selectedValue == $value){
			$selected = ' selected="selected"';
		}
		
		$formater = new TimeInvestmentFormater();
		
		$html = '<option value="'.$value.'"'.$selected.'>';
		$html .= $formater->formatForOutput($value);
		$html .= '</option>';
		
		return $html;
	}
	
	/**
	 * Returns the valid hour values.
	 * 
	 * @return int[] Select values in minutes
	 */
	protected function getSelectValues(){
		return array(
			15,30,45,60,90,60*2,60*3,60*4,60*5
		);
	}
}
