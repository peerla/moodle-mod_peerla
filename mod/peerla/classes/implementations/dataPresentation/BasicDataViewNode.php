<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/dataPresentation/DataViewTreeNode.php';
require_once realpath(__DIR__).'/../../interfaces/dataPresentation/DataPoint2D.php';

/**
 * A simple implementation of the DataViewTreeNode interface, which only uses getter
 *  and setter methods to provide the data specified in the interface.
 *
 * @author Christoph Bohr
 */
class BasicDataViewNode implements DataViewTreeNode {
	
	protected $label;
	protected $description;
	protected $dataPoints = array();
	protected $childNodes = array();
	protected $displayOwnData = true;
	protected $displayChildData = false;
	protected $maxValueY;
	protected $minValueY;
	
	/**
	 * Add a single data point object.
	 * 
	 * @param DataPoint2D $dataPoint
	 */
	public function addDataPoint(DataPoint2D $dataPoint){
		$this->dataPoints[] = $dataPoint;
	}
	
	/**
	 * Set a data view name.
	 * 
	 * @param string $label Data view name
	 */
	public function setLabel($label){
		$this->label = $label;
	}
	
	/**
	 * Add a new child node.
	 * 
	 * @param \mod_kom_peerla\DataViewTreeNode $child
	 */
	public function addChildNode(DataViewTreeNode $child){
		$this->childNodes[] = $child;
	}

	public function getChildNodes() {
		return $this->childNodes;
	}

	public function getDataPoints() {
		return $this->dataPoints;
	}

	public function getLabel() {
		return $this->label;
	}

	public function hasChildNodes() {
		if (count($this->getChildNodes()) > 0){
			return true;
		}
		return false;
	}

	public function displayChildData() {
		return $this->displayChildData;
	}

	public function displayOwnData() {
		return $this->displayOwnData;
	}
	
	/**
	 * Set if the child data should be rendered, if displaing this node.
	 * 
	 * @param bool $display
	 */
	public function setDisplayChildData($display=true){
		$this->displayChildData = $display;
	}
	
	/**
	 * Set if the data points of this node should be rendered, if displaing this node.
	 * 
	 * @param bool $display
	 */
	public function setDisplayOwnData($display=true){
		$this->displayOwnData = $display;
	}

	/**
	 * Set a highest value which should be displayed at the y axis.
	 * 
	 * @param int $value Description
	 */
	public function setMaxDisplayValueY($value) {
		$this->maxValueY = $value;
	}

	/**
	 * Set the lowest value which should be displayed at the y axis.
	 * 
	 * @param int $value Description
	 */
	public function setMinDisplayValueY($value) {
		$this->minValueY = $value;
	}

	public function getMaxDisplayValueY() {
		return $this->maxValueY;
	}

	public function getMinDisplayValueY() {
		return $this->minValueY;
	}
	
	/**
	 * Set the description text for this data view.
	 * 
	 * @param type $description Description text
	 */
	public function setDescriptionText($description){
		$this->description = $description;
	}

	public function getDescriptionText() {
		return $this->description;
	}

}
