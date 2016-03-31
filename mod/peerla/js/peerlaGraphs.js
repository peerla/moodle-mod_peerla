
function initD3NominalOrdinalGraph(containerSelector,data){
    
    //Width and height
    var w = 500;
    var h = 325;
    
    //Create SVG element
    var svg = d3.select(containerSelector)
        .append("svg")
        .attr("width", w)
        .attr("height", h);
    
    displayD3NominalOrdinalGraph(svg, data);
}

/*
 * Get a bar color for a nominal value. Calling this function will automatically
 * add the value to the legend. 
 */
function getNominalValueColor(value,svgElement){
    var colors = ['teal','blue','green'];
    var colorIndex = 0;
    
    if ($(svgElement.node()).find('.legend').length === 0){
        svgElement
            .append('g')
            .attr("class", "legend");
    }
    
    var legend = svgElement.select('g.legend');
    
    var legendElement = $(legend.node()).find('.legendValue[data-value="'+value+'"]');
    if (legendElement.length === 0){
        var legendElementCount = $(legend.node()).find('.legendValue').length;
        colorIndex = legendElementCount;
        var x = 50 + 12 * legendElementCount;
        var y = svgElement.attr("height") - 3;
        
        legend
            .append('circle')
            .attr('cx', x)
            .attr('cy', y)
            .attr('r', 3)
            .attr('data-value', value)
            .attr('data-colorindex', colorIndex)
            .attr('class','legendValue')
            .attr('fill', colors[colorIndex]);
    }
    else{
        colorIndex = legendElement.attr('data-colorindex');
    }
    
    return colors[colorIndex];
}

function displayD3NominalOrdinalGraph(svg,nodes){
    
    var topPadding = 15;
    var bottomPedding = 10;
    var paddingLeft = 40;
    
    var w = svg.attr("width") - paddingLeft;
    var h = svg.attr("height");
    var startingX = paddingLeft;
    
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
    
    var nodePadding = w / displayNodes.length / 100 * 5;
    
    var subPlotWidth = w / displayNodes.length - nodePadding;
    var subPlotHeight = h - topPadding - bottomPedding;
    
    svg.selectAll("*").remove();
    
    svg.selectAll("rect")
        .data(displayNodes)
        .enter()
        .append('g')
        .on("mouseover", function() {
            d3.select(this).select('rect.nodeSpaceRect').attr('fill','#989898')
        }).on('mouseout', function(d){
            d3.select(this).select('rect.nodeSpaceRect').attr('fill','#E8E8E8');
        })
        .on("click", function(d,i){
            var d = d3.select(this).select('rect.nodeSpaceRect').datum();
            displayD3NominalOrdinalGraph(svg,[d]);
        })
        .attr("class", "nodeSpace")
        .append("rect")
        .attr("class", "nodeSpaceRect")
        .attr('fill','#E8E8E8')
        .attr("x", function(d,i){
            return startingX + i * (w / displayNodes.length);
        })
        .attr("y", 0 + topPadding)
        .attr("width", subPlotWidth)
        .attr("height", subPlotHeight);
    
    //display point bars
    svg.selectAll('.nodeSpace').each(function(node,i){
       var g = d3.select(this);
       var rect = g.select('rect');
       displayD3NominalOrdinalGraphPoints(svg,g,node);
   });
        
    //create labels
    svg.selectAll('.nodeSpace').each(function(node,i){
        var rect = d3.select(this).select('.nodeSpaceRect');
        var textHeight = 14;
        var textX = parseFloat(rect.attr("x")) + parseFloat(rect.attr("width")) / 2 - textHeight / 2;
        var textY = parseFloat(rect.attr('y')) + 10;
        d3.select(this)
            .append('text')
            .text(node['label'])
            .attr("x", textX)
            .attr('y', textY)
            .attr("transform", "rotate(90,"+(textX)+","+textY+")" )
            .attr("font-family", "sans-serif")
            .attr("font-size", textHeight+"px")
            .attr("line-size", textHeight+"px")
            .attr("text-anchor",'start')
            .attr("fill", "white");
        ;
    });
    
}

//displays the points of one node
function displayD3NominalOrdinalGraphPoints(svg,nodeContainer,node){
    
    var sidePadding = 10;
    var barPadding = 5;
    var maxBarWidth = 20;
    
    var rect = nodeContainer.select('.nodeSpaceRect');
    
    var w = parseFloat(rect.attr("width")) - (sidePadding*2);
    var h = parseFloat(rect.attr("height"));
    var points = node['points'];
    
    var barWidth = w / points.length - barPadding;
    if (barWidth > maxBarWidth){
        sidePadding = (w - (maxBarWidth + barPadding) * points.length + barPadding) / 2 + sidePadding;
        barWidth = maxBarWidth;
    }
    
    var minX = parseFloat(rect.attr("x")) + sidePadding;
    var minY = parseFloat(rect.attr("y"));
    
    var scaleY = d3.scale.linear()
                    .domain([0, 100])
                    .range([minY+h, minY]);
    
    var scaleValues = d3.scale.linear()
                    .domain([0, 100])
                    .range([0, h]);
    
    nodeContainer.selectAll("rect.pointBar")
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
            return getNominalValueColor(d['x'],'',svg)
        });
    
    drawYAxis(svg,scaleY);
}

function drawYAxis(svg, scale){
    if ($(svg.node()).find('.yAxisContainer').length > 0){
        return;
    }
    
    //create axis
    var yAxisNodes = svg.append("g")
        .attr("transform", "translate(30,0)")
        .attr('class','yAxisContainer')
        .call(d3.svg.axis()
        .scale(scale)
        .orient("left"));
    
    yAxisNodes.selectAll('text').style({ 'stroke-width': '0.1px', 'font-size': '8px'});
    yAxisNodes.selectAll('path').style({ 'stroke': 'Black', 'stroke-width': '0.5px', 'fill': 'none'});
}


