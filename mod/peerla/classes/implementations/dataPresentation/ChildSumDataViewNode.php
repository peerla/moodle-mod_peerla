<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/BasicDataViewNode.php';
require_once realpath(__DIR__).'/SumCalculationDataPoint2D.php';

/**
 * A data view tree node, that generates the sum of all child nodes
 * data points.
 *
 * @author Christoph Bohr
 */
class ChildSumDataViewNode extends BasicDataViewNode{
	protected $groupBy = 'x';
	protected $avgGenerates = false;
	
	/**
	 * Set which coordinate should be used for grouping the data.
	 * 
	 * Calling the method will result in the x coordinate values of the child 
	 * nodes beeing unchanged and the y coordinate values beeing averaged.
	 */
	public function groupByXvalue(){
		$this->groupBy = 'x';
	}
	
	/**
	 * Set which coordinate should be used for grouping the data.
	 * 
	 * Calling the method will result in the y coordinate values of the child 
	 * nodes beeing unchanged and the x coordinate values beeing averaged.
	 */
	public function groupByYvalue(){
		$this->groupBy = 'y';
	}

	protected function generateDataPoints(){
		$avgDataPoints = array();
		
		foreach($this->getChildNodes() as $node){
			foreach($node->getDataPoints() as $dataPoint){
				
				if ($this->groupBy == 'x'){
					$value = $dataPoint->getValueX();
				}
				else{
					$value = $dataPoint->getValueY();
				}
				
				//skip the point, if the value for average calculation is nominal.
				if (($dataPoint->yValueIsNominal() && $this->groupBy == 'x')
						|| $dataPoint->xValueIsNominal() && $this->groupBy == 'y'){
					continue;
				}
				
				if (!isset($avgDataPoints[$value])){
					$newPoint = new SumCalculationDataPoint2D();
					if ($this->groupBy == 'x'){
						$newPoint->setValueX($value);
					}
					else{
						$newPoint->setValueY($value);
					}
					$avgDataPoints[$value] = $newPoint;
				}
				
				if ($this->groupBy == 'x'){
					$avgDataPoints[$value]->addValueY($dataPoint->getValueY());
				}
				else{
					$avgDataPoints[$value]->addValueY($dataPoint->getValueX());
				}
			}
		}
		
		$this->dataPoints = array();
		foreach($avgDataPoints as $dataPoint){
			$this->dataPoints[] = $dataPoint;
		}
	}

	public function getDataPoints() {
		if (!$this->avgGenerates){
			$this->generateDataPoints();
		}
		
		return $this->dataPoints;
	}
}
