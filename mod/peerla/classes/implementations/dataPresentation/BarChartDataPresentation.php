<?php

namespace mod_kom_peerla;
require_once realpath(__DIR__).'/../../interfaces/dataPresentation/DataPresentation.php';

/**
 * Description of DummyDataPresentation
 *
 * @author Christoph Bohr
 */
class BarChartDataPresentation implements DataPresentation {
	
	protected $rootNode;
	protected $width = null;
	protected $height = null;
	protected $colors;
	
	protected $descriptionSelector;
	protected $descriptionText;

	/**
	 * Set the width of the graph.
	 * 
	 * If no width is given, the width of the parent element will be used.
	 * 
	 * @param int $width
	 */
	public function setChartWidht($width){
		$this->width = $width;
	}
	
	/**
	 * Set the height of the graph.
	 * 
	 * If no width is given, the height of the parent element will be used.
	 * 
	 * @param int $height
	 */
	public function setCharHeight($height){
		$this->height = $height;
	}
	
	public function setGraphDescription($containerSelector, $graphDescriptionText){
		$this->descriptionSelector = $containerSelector;
		$this->descriptionText = $graphDescriptionText;
	}
	
	public function setColors(array $colors){
		$this->colors = $colors;
	}


	/**
	 * Get the HTML/JavaScript code the graph generation
	 * 
	 * @param string $containerSelector jQuery style selector for the graph container element
	 * @return string
	 */
	public function getHtmlString($containerSelector='') {
		if (!isset($this->rootNode)){
			return 'Could not display data.';
		}
		$data = array();
		$data[] = $this->generateNodeDataArray($this->rootNode);
		
		$html = '<script>';
		
		$html .= '	$(function(){';
		$html .= '		var barChart = new BarChart('.json_encode($data).');';
		
		if ($this->colors){
			$html .= '	barChart.setBarColors('.  json_encode($this->colors).');';
		}
		
		if ($this->descriptionSelector){
			$html .= '	barChart.setDescriptionArea('
					. '		"'.$this->descriptionSelector.'",'
					. '		"'.$this->descriptionText.'"'
					. '	);';
		}
		
		$html .= '		barChart.displayGraph("'.$containerSelector.'","'.$this->width.'","'.$this->height.'");';
		
		$html .= '	});';
		
		$html .= '</script>';
		
		return $html;
	}
	
	/**
	 * Get the an array with all the graph node data.
	 * 
	 * @param \mod_kom_peerla\DataViewTreeNode $node
	 * @return array
	 */
	protected function generateNodeDataArray(DataViewTreeNode $node){
		
		$data = array(
			'label' => $node->getLabel(),
			'displayChildData' => $node->displayChildData(),
			'displayOwnData' => $node->displayOwnData(),
			'points' => $this->getDataPointsDataArray($node),
			'children' => array(),
			'fixedMaxValue' => $node->getMaxDisplayValueY(),
			'fixedMinValue' => $node->getMinDisplayValueY(),
			'description' => $node->getDescriptionText()
		);
		
		if ($node->hasChildNodes()){
			$children = array();
			foreach($node->getChildNodes() as $child){
				$children[] = $this->generateNodeDataArray($child);
			}
			$data['children'] = $children;
		}
		
		return $data;
	}
	
	/**
	 * Get an array with all the data points data of one node
	 * 
	 * @param \mod_kom_peerla\DataViewTreeNode $node
	 * @return array
	 */
	protected function getDataPointsDataArray(DataViewTreeNode $node){
		$points = $node->getDataPoints();
		if (!is_array($points)){
			return array();
		}
		
		$data = array();
		foreach($points as $point){
			$data[] = array(
				'x' => $point->getValueX(),
				'y' => $point->getValueY()
			);
		}
		
		return $data;
	}

	/**
	 * Set the (root) data view node element.
	 * 
	 * @param \mod_kom_peerla\DataViewTreeNode $dataView
	 */
	public function setData(DataViewTreeNode $dataView) {
		$this->rootNode = $dataView;
	}

}
