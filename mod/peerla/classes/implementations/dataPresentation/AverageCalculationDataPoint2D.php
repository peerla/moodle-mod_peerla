<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/BasicDataPoint2D.php';

/**
 * A data point objects, which can be used to calculate the average of 
 * multiple values.
 *
 * @author Christoph Bohr
 */
class AverageCalculationDataPoint2D extends BasicDataPoint2D {
	
	protected $xSum = 0;
	protected $ySum = 0;
	protected $xCount = 0;
	protected $yCount = 0;
	
	protected $x;
	protected $y;
	protected $xIsNominal = false;
	protected $yIsNominal = false;
	
	/**
	 * Add an additional x value.
	 * 
	 * getValueX will return the linear average of all values added via this
	 * method. 
	 * 
	 * @param int|float $value  
	 */
	function addValueX($value){
		if (is_numeric($value) && !$this->xValueIsNominal()){
			$this->xSum += $value;
			$this->xCount++;
		}
	}
	
	/**
	 * Add an additional y value.
	 * 
	 * getValueY will return the linear average of all values added via this
	 * method. 
	 * 
	 * @param int|float $value  
	 */
	function addValueY($value){
		if (is_numeric($value) && !$this->yValueIsNominal()){
			$this->ySum += $value;
			$this->yCount++;
		}
	}
	
	/**
	 * Set the x coordinate value.
	 * 
	 * Calling this method will overwrite the values generated by addValueX.
	 * 
	 * @param mixed $value
	 */
	public function setValueX($value) {
		$this->xSum = $value;
		$this->xCount = 1;
	}

	/**
	 * Set the y coordinate value.
	 * 
	 * Calling this method will overwrite the values generated by addValueY.
	 * 
	 * @param mixed $value
	 */
	public function setValueY($value) {
		$this->ySum = $value;
		$this->yCount = 1;
	}
	
	/**
	 * Set if the x coordinate value is a nominal value.
	 * 
	 * If false is given, a ordinal value is expected.
	 * 
	 * @param bool $nominal
	 */
	public function setXvalueIsNominal($nominal=true){
		$this->xIsNominal = $nominal;
	}
	
	/**
	 * Set if the y coordinate value is a nominal value.
	 * 
	 * If false is given, a ordinal value is expected.
	 * 
	 * @param bool $nominal
	 */
	public function setYvalueIsNominal($nominal=true){
		$this->yIsNominal = $nominal;
	}

	public function getValueX() {
		//needed to handle nominal values set by setValue
		if ($this->xCount == 1){
			return $this->xSum;
		}
		
		if ($this->xCount == 0){
			return null;
		}
		
		return $this->xSum / $this->xCount;
	}

	public function getValueY() {
		//needed to handle nominal values set by setValue
		if ($this->yCount == 1){
			return $this->ySum;
		}
		
		if ($this->yCount == 0){
			return null;
		}
		
		return $this->ySum / $this->yCount;
	}

	public function xValueIsNominal() {
		return $this->xIsNominal;
	}

	public function yValueIsNominal() {
		return $this->yIsNominal;
	}
	
}
