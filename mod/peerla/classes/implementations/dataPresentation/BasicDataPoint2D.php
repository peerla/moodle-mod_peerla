<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/dataPresentation/DataPoint2D.php';

/**
 * A simple implementation of the DataPoint2D interface, which only uses getter
 *  and setter methods to provide the data specified in the interface.
 *
 * @author Christoph Bohr
 */
class BasicDataPoint2D implements DataPoint2D {
	
	protected $x;
	protected $y;
	protected $xIsNominal = false;
	protected $yIsNominal = false;

	/**
	 * Set the x coordinate value.
	 * 
	 * @param mixed $value
	 */
	public function setValueX($value) {
		$this->x = $value;
	}

	/**
	 * Set the y coordinate value.
	 * 
	 * @param mixed $value
	 */
	public function setValueY($value) {
		$this->y = $value;
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
		return $this->x;
	}

	public function getValueY() {
		return $this->y;
	}

	public function xValueIsNominal() {
		return $this->xIsNominal;
	}

	public function yValueIsNominal() {
		return $this->yIsNominal;
	}

}
