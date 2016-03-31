<?php

namespace mod_kom_peerla;

/**
 * Represents a 2D data point.
 *
 * @author Christoph Bohr
 */
interface DataPoint2D {
	
	/**
	 * Get the x coordinate value.
	 * 
	 * @return mixed X value
	 */
	function getValueX();
	
	/**
	 * Get the y coordinate value.
	 * 
	 * @return mixed Y value
	 */
	function getValueY();
	
	/**
	 * Returns, if the x coordinate value represents a nominal value.
	 * 
	 * If TRUE is returned the x coordinate value is nominal, otherwise the x
	 * value is ordinal.
	 * 
	 * @return bool  
	 */
	function xValueIsNominal();
	
	/**
	 * Returns, if the y coordinate value represents a nominal value.
	 * 
	 * If TRUE is returned the y coordinate value is nominal, otherwise the y
	 * value is ordinal.
	 * 
	 * @return bool  
	 */
	function yValueIsNominal();
	
}
