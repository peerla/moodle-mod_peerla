function BarChart(nodes){
    
    this.svg;
    this.nodes = nodes;
    this.backNavRoot = [];
    this.currentLevelNodes = [];
    this.descriptionArea;
    this.graphDescription;
    
    this.viewBg = '#E8E8E8';
    this.viewBgHover = '#989898';
    this.barColors = ['teal','blue','green','navy'];
    this.barValueColors = {};
    
    this.graphInnerPadding = {'top': 15, 'bottom': 10, 'left': 40, 'right': 0};
    
    this.setDescriptionArea = function(containerSelector, graphText){
        
        this.descriptionArea = $(containerSelector);
        this.descriptionArea.css('padding-top',this.graphInnerPadding['top']);
        this.descriptionArea.css('padding-bottom',this.graphInnerPadding['bottom']);
        
        this.graphDescription = graphText;
    };
    
    this.setBarColors = function(colorArray){
        this.barColors = colorArray;
    };
    
    this.setBarColorForValue = function(value, color){
        this.barValueColors[value] = this.barColors[color];
    };
    
    this.renderNodeDescription = function(node){
        if (this.descriptionArea && this.descriptionArea.length > 0){
            
            this.descriptionArea.html('');
            var container = $('<div></div>');
            container.addClass('barGraphDescriptionBox');
            container.css('height','100%');
            container.css('overflow-y','auto')
            
            if (!node){
                var displayNodes = this.getDipslayNodes(this.currentLevelNodes);
                if (displayNodes.length === 1){
                    node = displayNodes[0];
                }
            }
            
            this.renderLegend(container);
            
            //render node description
            if (node && node['description']){
                var nodeDescriptionElement = $('<div></div');
                nodeDescriptionElement.addClass('nodeDescriptionText');
                nodeDescriptionElement.html(node['description']);
                container.append(nodeDescriptionElement);
            }
            
            //render graph description
            if (this.graphDescription){
                var graphDescriptionElement = $('<div></div');
                graphDescriptionElement.addClass('graphDescriptionText');
                graphDescriptionElement.html(this.graphDescription);
                container.append(graphDescriptionElement);
            }
            
            this.descriptionArea.append(container);
        }
    }
    
    //render legend
    this.renderLegend = function(containerElement){
        var legendContainer = $('<div></div>');
        legendContainer.addClass('legendContainer');
        
        var legendHeadline = $('<div></div>');
        legendHeadline.addClass('legendHeadline');
        legendHeadline.html('Legende');
        legendContainer.append(legendHeadline);
        
        var legendElement = $('<ul></ul>');
        legendElement.addClass('graphLegend');
        legendContainer.append(legendElement);
        
        $.each(this.barValueColors,function(value, color){
            var listElement = $('<li></li>');
            
            var listSymbolElement = $('<div></div>');
            listSymbolElement.css('background-color',color);
            listSymbolElement.addClass('legendSymbol');

            var listTextElement = $('<div></div>');
            listTextElement.html(value);
            listTextElement.addClass('legendText');

            listElement.append(listSymbolElement);
            listElement.append(listTextElement);
            legendElement.append(listElement);
        });
        
        legendElement.html();
        containerElement.append(legendContainer);
            
    }
    
    this.displayGraph = function(containerSelector, width, height){
       
        if (!width){
            width = $(containerSelector).width();
        }
        if (!height){
            height = $(containerSelector).height();
        }
        
         //Create SVG element
        this.svg = d3.select(containerSelector)
            .append("svg")
            .attr("width", width)
            .attr("height", height);
    
        this.backNavRoot = [];
        
        this.renderNodes(this.nodes);
    };
    
    /*
     * Renders the given points in the current svg
     */
    this.renderNodes = function(nodes){
        
        this.currentLevelNodes = nodes;
        this.renderNodeDescription();
        
        var displayNodes = this.getDipslayNodes(nodes);
        
        //clear img
        this.svg.selectAll("*").remove();
        //render nod backgrounds
        this.renderNodeBackgrounds(displayNodes);

        var self = this;
        this.svg.selectAll('.nodeSpace').each(function(node,i){
            var rect = d3.select(this).select('.nodeSpaceRect');
            //render sub plot labels
            self.renderSubPlotLabel(rect, node);
            //render node points
            self.renderSubPlotNodes(rect,node);
       });
        
       //render y axis
       this.renderAxisY();
       
       //render back navigation link
       this.renderBackNavigation();
    };
    
    this.getSubPlotPadding = function(){
        var displayNodes = this.getDipslayNodes(this.currentLevelNodes);
        var ContainerWidth = this.svg.attr('width')
                - this.graphInnerPadding['left'] - this.graphInnerPadding['right'];
        var padding = ContainerWidth / displayNodes.length / 100 * 5;
        return padding;
    };
    
    /*
     * Renders a navigate back link
     */
    this.renderBackNavigation = function(){
        
        if (this.backNavRoot.length === 0){
            return;
        }
        
        var self = this;
        var g = this.svg.append('g')
            .attr('class','backNavigation')
            .on("mouseover", function() {
                d3.select(this).select('rect.backNavBg').style('fill',self.viewBgHover)
            }).on('mouseout', function(){
                d3.select(this).select('rect.backNavBg').style('fill',self.viewBg);
            })
            .style('cursor','pointer')
            .on('click', function(){
                self.navigateBack();
            });
    
        var startX = this.svg.attr('width') - this.getSubPlotPadding() - this.graphInnerPadding['right'];
        var length = 10;
        var y = 6;
    
        g.append('rect')
            .attr('class','backNavBg')
            .attr('x',startX-length-12)
            .attr('y',0)
            .attr('height',12)
            .attr('width',length+12)
            .style('fill',this.viewBg);
        
        g.append('svg:defs')
            .append('svg:marker')
            .attr('id','test')
            .attr('refX',1)
            .attr('refY',5)
            .attr('viewBox', '0 0 10 10')
            .attr('markerWidth',4)
            .attr('markerHeight',4)
            .attr('orient','auto')
            .style('fill','white')
            .append("svg:path")
            .attr("d", "M 0 0 L 10 5 L 0 10 z")
            .style('fill','white');
            
        g.append('line')
            .attr('x1',startX-3)
            .attr('x2',startX-3-length)
            .attr('y1',y)
            .attr('y2',y)
            .style('stroke','white')
            .style('fill','white')
            .attr('stroke-width', 2)
            .attr('marker-end','url(#test)')
            .on('click', function(){
                self.navigateBack();
            });
    };
    
    this.navigateBack = function(){
        if (this.backNavRoot.length > 0){
            var nodes = this.backNavRoot.pop();
            this.renderNodes(nodes);
        }
    };
    
    /*
     * Takes an array of nodes and returns all nodes, that should be displayed.
     * The returned nodes are the nodes themself and/or their children, 
     * depending on their display option.
     */
    this.getDipslayNodes = function(nodes){
        var displayNodes = [];
        
        $.each(nodes,function(index, node){
            if (node['displayOwnData']){
                displayNodes.push(node);
            }
            if (node['displayChildData']){
                $.each(node['children'],function(childIndex, child){
                    displayNodes.push(child);
                });
            }
        });
        
        return displayNodes;
    }
    
    /*
     * Renders the background rects for the current display nodes
     */
    this.renderNodeBackgrounds = function(nodes){
        
        var startX = this.graphInnerPadding['left'];
        var startY = this.graphInnerPadding['top'];
        var ContainerWidth = this.svg.attr('width')
                - this.graphInnerPadding['left'] - this.graphInnerPadding['right'];
        var ContainerHeight = this.svg.attr('height')
                - this.graphInnerPadding['top'] - this.graphInnerPadding['bottom'];
        
        var nodePadding = this.getSubPlotPadding();
        var subPlotWidth = ContainerWidth / nodes.length - nodePadding;
        
        var self = this;
        
        //render container elements
        this.svg.selectAll("rect")
            .data(nodes)
            .enter()
            .append('g')
            .on("mouseover", function() {
                var viewContainer = d3.select(this).select('rect.nodeSpaceRect').attr('fill',self.viewBgHover);
                self.renderNodeDescription(viewContainer.datum());
                //d3.select(this).select('.subPlotLabel').style('opacity','1');
                d3.select(this).selectAll('.pointBar').style('opacity','0.3');
            }).on('mouseout', function(){
                d3.select(this).select('rect.nodeSpaceRect').attr('fill',self.viewBg);
                self.renderNodeDescription();
                d3.select(this).selectAll('.pointBar').style('opacity','1');
                //d3.select(this).select('.subPlotLabel').style('opacity','0.5');
            })
            .style('cursor','pointer')
            .on("click", function(){
                var d = d3.select(this).select('rect.nodeSpaceRect').datum();
                //show no detail view, if the new node has no points or equals the
                //current node
                if (self.nodesAreEqual([d], self.currentLevelNodes) || d['points'].length == 0){
                    return false;
                }
                self.backNavRoot.push(self.currentLevelNodes);
                self.renderNodes([d]);
            })
            .attr("class", "nodeSpace")
            .append("rect")
            .attr("class", "nodeSpaceRect")
            .attr('fill',this.viewBg)
            .attr("x", function(d,i){
                return startX + i * (ContainerWidth / nodes.length);
            })
            .attr("y", startY)
            .attr("width", subPlotWidth)
            .attr("height", ContainerHeight);
    }
    
    
    //displays the points of one node
    this.renderSubPlotNodes = function(subPlotContainer,node){

        var sidePadding = 10;
        var barPadding = 5;
        var maxBarWidth = 20;

        var w = parseFloat(subPlotContainer.attr("width")) - (sidePadding*2);
        var h = parseFloat(subPlotContainer.attr("height"));
        var points = node['points'];
        
        //no points present? -> display message
        if (points.length == 0){
            d3.select(subPlotContainer.node().parentNode)
                    .append('text')
                    .text(this.wrapText('Momentan keine Daten'), w, h)
                    .attr("x", parseFloat(subPlotContainer.attr("x")) + sidePadding + w/2)
                    .attr('y', parseFloat(subPlotContainer.attr("y")) + h/2)
                    .attr('width',w)
                    .attr('font-size',10)
                    .attr('text-anchor','middle');
            return;
        }

        var barWidth = w / points.length - barPadding;
        if (barWidth > maxBarWidth){
            sidePadding = (w - (maxBarWidth + barPadding) * points.length + barPadding) / 2 + sidePadding;
            barWidth = maxBarWidth;
        }

        var minX = parseFloat(subPlotContainer.attr("x")) + sidePadding;
        var minY = parseFloat(subPlotContainer.attr("y"));

        var valueDomain = this.getCurrentValueDomain();
        var scaleValues = d3.scale.linear()
                        .domain(valueDomain)
                        .range([0, h]);

        var self = this;
        d3.select(subPlotContainer.node().parentNode).selectAll("rect.pointBar")
            .data(points)
            .enter()
            .append('rect')
            .attr("x", function(d,i){
                return minX + i * (barWidth + barPadding);
            })
            .attr("y", function(d,i){
                return h-scaleValues(d['y']) + minY;
            })
            .attr("width", barWidth)
            .attr("height", function(d,i){
                return scaleValues(d['y']);
            })
            .attr("class", "pointBar")
            .attr("fill", function(d,i){
                return self.getNominalValueColor(d['x'])
            });
    };
    
    /*
    * Get a bar color for a nominal value. Calling this function will automatically
    * add the value to the legend. 
    */
    this.getNominalValueColor = function(value){
       
        if (!this.barValueColors[value]){
            var lastUsedColorIndex = Object.keys(this.barValueColors).length;
            var totalNumberOfColors = this.barColors.length;
            if (totalNumberOfColors === 0){
               return '#000000';
            }
            
            var nextColorIndex = (lastUsedColorIndex % (totalNumberOfColors));
            
            this.barValueColors[value] = this.barColors[nextColorIndex];
            this.renderNodeDescription();
        }
        
        return this.barValueColors[value];
   };
   
    this.renderSubPlotLabel = function(subPlotContainer,node){
        
        var text = node['label'];
        var textHeight = 14;
        
        var textX = parseFloat(subPlotContainer.attr("x")) + parseFloat(subPlotContainer.attr("width")) / 2 - textHeight / 2;
        var textY = parseFloat(subPlotContainer.attr('y')) + 10;
        
        d3.select(subPlotContainer.node().parentNode)
            .append('text')
            .text(text)
            .attr('class','subPlotLabel')
            .attr("x", textX)
            .attr('y', textY)
            .attr("transform", "rotate(90,"+(textX)+","+textY+")" )
            .attr("font-family", "sans-serif")
            .attr("font-size", textHeight+"px")
            .attr("line-size", textHeight+"px")
            .attr("text-anchor",'start')
            //.style('opacity','0.5')
            .attr("fill", "white");
        ;
    };
    
    /*
     * Get the d3 scale for the current data
     */
    this.getAxisScaleY = function(){
        
        var domain = this.getCurrentValueDomain();
        
        //rage = inner content
        var innerGraphHeight = this.svg.attr('height')
                -this.graphInnerPadding['bottom']-this.graphInnerPadding['top'];
        var innerGraphMinY = this.graphInnerPadding['top'];
        var range = [innerGraphMinY+innerGraphHeight,innerGraphMinY];
        
        return d3.scale.linear()
            .domain(domain)
            .range(range);
    }
    
    /*
     * Get the current value domain
     */
    this.getCurrentValueDomain = function(){
        var displayNodes = this.getDipslayNodes(this.currentLevelNodes);
        
        //find the highest X value
        var maxY = d3.max(displayNodes, function(node) {
            if (node['fixedMaxValue']){
                return node['fixedMaxValue'];
            }
            
            return d3.max(node['points'], function(point){
                return point['y'];
            });
        });
        //find the lowest X value
        var minY = d3.min(displayNodes, function(node) {
            if (node['fixedMinValue']){
                return node['fixedMinValue'];
            }
            /*
            return d3.min(node['points'], function(point){
                return point['y'];
            });
            */
           return 0;
        });
        
        return [minY, maxY];
    }
    
    /*
     * Render the Y axis.
     */
    this.renderAxisY = function(){
        if ($(this.svg.node()).find('.yAxisContainer').length > 0){
            return;
        }
        
        var scale = this.getAxisScaleY();

        //create axis
        var yAxisNodes = this.svg.append("g")
            .attr("transform", "translate(30,0)")
            .attr('class','yAxisContainer')
            .call(d3.svg.axis()
            .scale(scale)
            .orient("left"));

        yAxisNodes.selectAll('text').style({ 'stroke-width': '0.1px', 'font-size': '8px'});
        yAxisNodes.selectAll('path').style({ 'stroke': 'Black', 'stroke-width': '0.5px', 'fill': 'none'});
    }
    
    this.wrapText = function(text,width,height){
        return text;
    }
    
    /*
     * Compares 2 node json objects and returns, if they have the same content
     */
    this.nodesAreEqual = function(node1, node2){
        if (JSON.stringify(node1) === JSON.stringify(node2)){
            return true;
        }
        return false;
    }
}

