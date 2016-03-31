<?php

namespace mod_kom_peerla;

/**
 * A node of a tree which holds 2d data points.
 * 
 * Each node of the tree represents one view on the data. The child nodes 
 * represent a more detailed view (on one aspect) of the data.
 * 
 * @author Christoph Bohr
 */
interface DataViewTreeNode {
	
	/**
	 * Get the highest value which should be displayed at the y axis.
	 * 
	 * @return int Maximum y axis value
	 */
	function getMaxDisplayValueY();
	
	/**
	 * Get the lowest value which should be displayed at the y axis.
	 * 
	 * @return int Minimum y axis value
	 */
	function getMinDisplayValueY();
	
	/**
	 * Get a descriptive name for this data view.
	 * 
	 * @return string View name
	 */
	function getLabel();
	
	/**
	 * Get a discreption text for this data view.
	 * 
	 * @return string Description text
	 */
	function getDescriptionText();
	
	/**
	 * Get all data points for this view.
	 * 
	 * @return DataPoint2D[] Array of data point objects
	 */
	function getDataPoints();
	
	/**
	 * Get all child views of this data view.
	 * 
	 * @return DataViewTreeNode[] Array of child view objects
	 */
	function getChildNodes();
	
	/**
	 * Returns, if a this view has child views.
	 * 
	 * @return bool True if child views exist, false otherwise
	 */
	function hasChildNodes();
	
	/**
	 * Returns if the data points of this node should be displayed.
	 * 
	 * The return value of the method determens, how the node will be displayed.
	 * If this node is rendered and TRUE is returned, then the data points 
	 * returns by this node will be rendered (possibly in addition to child node
	 * data points). The return value has no effect, if the parent node of this
	 * node is displaing all it children nodes data. In that case, the child 
	 * nodes data points will allways be displayed.
	 * 
	 * @return bool  
	 */
	function displayOwnData();
	
	/**
	 * Returns if the data points of the children nodes should be displayed.
	 * 
	 * The return value of the method determens, how the node will be displayed.
	 * If this node is rendered and TRUE is returned, then all data points of 
	 * all child nodes will be rendered (possibly in addition to the point of 
	 * this note). The return value has no effect, if the parent node of this
	 * node is displaing all it children nodes data. In that case, the child 
	 * nodes data points will allways be displayed.
	 * 
	 * @return bool  
	 */
	function displayChildData();
}
