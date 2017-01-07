function EPCViz(name, container, nodes, edges, matchViz, defaultNodeHeight, defaultNodeWidth, useGrid, horizontalDistance, verticalDistance, horizontalDistanceSattelite, connectorSize) {
    // given data
    this.name = name;
    this.container = container;
    this.genericNodes = [];
    this.genericEdges = [];
    this.matchViz = matchViz;

    // calculated data
    this.loopEdges = [];
    this.allNodesIncludingDummy = [];
    this.maxLayerNumber = -1;
    this.listOfNodesInLayer = [];
    this.listOfNodesInLayerWithDummyNodes = [];
    // wird im Anschluss berechnet
    this.maxLabelLength = undefined;

    // configuration
    this.debug = false;
    this.useGrid = true;
    this.defaultNodeHeight = 50;
    this.defaultNodeWidth = 100;
    this.connectorHeight = 38;
    // vertikaler Abstand zwischen Knoten
    this.y_Distance = 100;
    // gibt an wie lange die Kante erst geradeaus geht, bevor sie einen Knick macht
    this.edgeStraightLengthDistance = 15;
    // Breite des Pfeils
    this.edgeWidth = 10;

    // in case of useGrid
    // Abstand zwischen den Mittelpunkten zweier Knoten
    this.x_Distance = 125;
    // Abstand zwischen den Mittelpunkten eines Knoten und eines Satelitt-Objektes
    this.x_Distance_Satellite = 110;

    // in case of no useGrid
    // Abstand zwischen Knoten und Sattelitenobjekt
    this.satelliteDistance = 20;
    // Abstand zwischen Knoten
    this.nodeDistance = 30;


    this.labels = [];
    this.language = "en-gb";

    this.nodeIdsToNodes = [];
    this.edgeIdsToEdge = [];

    this.isRMMLayout = false;


    // update for given values
    if (useGrid !== undefined) {
        this.useGrid = useGrid;
    }
    if (defaultNodeHeight !== undefined) {
        this.defaultNodeHeight = defaultNodeHeight;
    }
    if (defaultNodeWidth !== undefined) {
        this.defaultNodeWidth = defaultNodeWidth;
    }
    if (horizontalDistance !== undefined) {
        if (useGrid !== undefined && useGrid) {
            this.x_Distance = horizontalDistance;
        } else {
            this.nodeDistance = horizontalDistance;
        }
    }
    if (horizontalDistanceSattelite !== undefined) {
        if (useGrid !== undefined && useGrid) {
            this.x_Distance_Satellite = horizontalDistanceSattelite;
        } else {
            this.satelliteDistance = horizontalDistanceSattelite;
        }
    }
    if (verticalDistance !== undefined) {
        if (useGrid !== undefined) {
            this.y_Distance = verticalDistance;
        }
    }
    if (connectorSize !== undefined) {
        this.connectorHeight = connectorSize;
    }


    // visualization stuff
    this.svgID = 0;
    this.mapSVGIDToNode = [];
    this.shapes = [];
    this.warnings = [];
    this.strokeColor = "black";
    this.selectedNodes = [];
    this.selectedNodesInThisEPC = [];
    this.svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    this.svg.id = "svg_" + this.container.id;
    this.svg.setAttribute("width", "100%");
    this.svg.setAttribute("height", "900");
    var svgNS = this.svg.namespaceURI;
    epcObject = this;


    this.getMaxLabelLength = function () {
        if (this.maxLabelLength !== undefined) {
            return this.maxLabelLength;
        }
        var max = -1;
        for (var i = 0; i < this.genericNodes.length; i++) {
            var node = this.genericNodes[i];
            if (node.label.length > max) {
                max = node.label.length;
            }
        }

        this.maxLabelLength = max;
        return max;
    }

    this.setMatchViz = function (matchViz) {
        this.vizEPC.setMatchViz(matchViz);
        this.matchViz = matchViz;
    }

    this.setColor = function (nodeID, color, matchID) {

        var l = this.genericNodes.length;
        var id;

        for (var i = 0; i < l; i++) {
            var node = this.genericNodes[i];
            if (node.id == nodeID) {
                id = Number(nodeID);
                if (isNaN(id)) {
                    id = nodeID;
                }
                if (color === null && matchID == null) {
                    var warning = this.warnings[id];
                    warning.setAttribute('visibility', 'hidden');

                    color = node.defaultColor;
                    this.genericNodes[i].colors = [];
                    this.genericNodes[i].matches = [];
                    for (var colNum = 0; colNum < this.shapes[node.id].length; colNum++) {
                        var col = 'white';

                        if (colNum < 2) {
                            this.shapes[node.id][colNum].setAttribute('fill', col);
                        }

                    }
                    return;
                } else {
                    if (color === null) {
                        color = 'white';
                    }
                    var index = -1;
                    for (var t = 0; t < this.genericNodes[i].matches.length; t++) {
                        var storedMatchID = this.genericNodes[i].matches[t];
                        if (matchID === storedMatchID) {
                            index = t;
                            break;
                        }
                    }
                    if (index !== -1) {
                        this.genericNodes[i].colors[index] = color;
                    } else {
                        this.genericNodes[i].colors.push(color);
                        this.genericNodes[i].matches.push(matchID);
                    }

                    if (this.genericNodes[i].colors.length > 2) {
                        var warning = this.warnings[id];
                        warning.setAttribute('visibility', 'visible');
                    }

                }
                for (var colNum = 0; colNum < this.shapes[node.id].length; colNum++) {
                    var col = 'white';

                    // TODO: if one color is white use other color

                    if (this.genericNodes[i].colors.length > 0) {
                        col = this.genericNodes[i].colors[0];
                    }
                    if (this.genericNodes[i].colors.length > 1) {
                        col = this.genericNodes[i].colors[colNum];
                    }

                    if (col === 'white' && this.genericNodes[i].colors.length > 1) {
                        if (this.genericNodes[i].colors[0] !== 'white') {
                            col = this.genericNodes[i].colors[0];
                        } else if (this.genericNodes[i].colors[1] !== 'white') {
                            col = this.genericNodes[i].colors[1];
                        }
                    }

                    if (colNum < 2) {
                        this.shapes[node.id][colNum].setAttribute('fill', col);
                    }
                }
                return;
            }

        }
    }

    this.setNodesWhite = function () {

        var l = this.genericNodes.length;

        for (var i = 0; i < l; i++) {
            var node = this.genericNodes[i];
            if (node instanceof  FunctionNode || node instanceof EventNode || node instanceof OperatorNode) {
                this.genericNodes[i].color = 'white';
                if (!(node instanceof OperatorNode)) {
                    this.genericNodes[i].defaultColor = 'white';
                }
                this.setColor(this.genericNodes[i].id, null, null);
            }
        }
    }

    this.drawLabel = function (node, group, centerX, centerY, nodeHeight, nodeWidth) {
        var x_center = node.x_inEPC;
        var y_center = node.y_inEPC;
        var height = node.height;
        var width = node.width;

        if (centerX !== undefined) {
            x_center = centerX;
        }
        if (centerY !== undefined) {
            y_center = centerY;
        }
        if (nodeHeight !== undefined) {
            height = nodeHeight;
        }
        if (nodeWidth !== undefined) {
            width = nodeWidth;
        }


        var lineLength = [];
        if (node.useRMM) {
            lineLength[12] = 12;
            lineLength[11] = 13;
            lineLength[10] = 14;
            lineLength[9] = 16;
            lineLength[8] = 18;
            lineLength[7] = 21;
            lineLength[6] = 25;
            lineLength[5] = 30;
            lineLength[4] = 36;
            lineLength[3] = 44;
            lineLength[2] = 55;
            lineLength[1] = 70;
        } else {
            lineLength[12] = 14;
            lineLength[11] = 15;
            lineLength[10] = 17;
            lineLength[9] = 20;
            lineLength[8] = 23;
            lineLength[7] = 26;
            lineLength[6] = 30;
            lineLength[5] = 36;
            lineLength[4] = 42;
            lineLength[3] = 50;
            lineLength[2] = 60;
            lineLength[1] = 80;
        }



        var size = -1;
        var oneLine = false;
        var length = epcObject.getMaxLabelLength();
        if (epcObject.getMaxLabelLength() < lineLength[12]) {
            size = 12;
            oneLine = true;
        } else {
            for (var i = 12; i >= 1; i--) {
                if (lineLength[i] * 3 >= epcObject.getMaxLabelLength()) {
                    size = i;
                    break;
                }
            }
        }

        // calculate Hyphens: https://github.com/mnater/Hyphenator/blob/wiki/en_PublicAPI.md#public-api
        Hyphenator.config({hyphenchar: '-'});
        var hyphenatedLabel = Hyphenator.hyphenate(node.label, this.language);

        var length = node.label.length;
        if (oneLine) {
            var label = document.createElementNS(svgNS, 'text');
            label.textContent = node.label;
            label.setAttribute('x', x_center);
            label.setAttribute('y', y_center);
            label.setAttribute("text-anchor", "middle");
            label.setAttribute("dominant-baseline", "central");
            label.setAttribute("style", "font-size:" + size + "px");

            group.appendChild(label);
        } else {
            var lines = [];
            if (length > lineLength[size] * 2) {
                // 3 lines
                var numLines = 3;
                var lastEnd = 0;
                var text = "";
                for (var lineNum = 0; lineNum < numLines; lineNum++) {

                    var subStringStart = lastEnd;
                    if (node.label.charAt(subStringStart) === " ") {
                        subStringStart + 1;
                    }
                    var subStringEnd = (lineNum + 1) * lineLength[size];

                    text = node.label.substring(subStringStart, subStringEnd);
                    lastEnd = subStringEnd;


                    var realLength = 0;


                    // modify substring if necessary
                    if (node.label.charAt(subStringEnd - 1) !== " " && node.label.charAt(subStringEnd - 2) === " " && node.label.charAt(subStringEnd) !== " ") {
                        subStringEnd = subStringEnd - 1;
                        lastEnd = subStringEnd;
                        text = node.label.substring(subStringStart, subStringEnd);
                    } else if (node.label.charAt(subStringEnd) !== " " && node.label.charAt(subStringEnd - 1) !== " " && (node.label.charAt(subStringEnd + 1) === " " || node.label.charAt(subStringEnd + 1) === "")) {
                        // new line would start with character and has a space following
                        subStringEnd = subStringEnd + 1;
                        lastEnd = subStringEnd;
                        text = node.label.substring(subStringStart, subStringEnd);
                    } else if (lineNum !== 2 && node.label.charAt(subStringEnd - 1) !== " " && node.label.charAt(subStringEnd - 2) !== " " && node.label.charAt(subStringEnd) !== " ") {
                        var lastHyphenPosition = -1;
                        var islastAHyphen = false;

                        var cont = true;
                        for (var pos = 0; pos < hyphenatedLabel.length; pos++) {
                            var n = hyphenatedLabel[pos];
                            if (hyphenatedLabel[pos] !== "-") {
                                realLength++;
                                if (hyphenatedLabel[pos] === " ") {
                                    lastHyphenPosition = realLength;
                                    islastAHyphen = false;
                                }
                            } else {
                                lastHyphenPosition = realLength;
                                islastAHyphen = true;
                            }

                            if (!cont) {
                                break;
                            }
                            if (realLength === subStringEnd) {
                                cont = false;
                            }
                        }
                        if (lastHyphenPosition === -1) {
                            lastHyphenPosition = subStringEnd;
                            islastAHyphen = true;
                        }
                        text = node.label.substring(subStringStart, lastHyphenPosition);
                        if (islastAHyphen) {
                            text = text + "-";
                        }
                        lastEnd = lastHyphenPosition;
                    }

                    if (text !== "") {
                        lines.push(text);
                    }
                }
            } else if (length > lineLength[size]) {
                // 2 lines
                var numLines = 2;
                var realLength = 0;
                var lastEnd = 0;

                for (var lineNum = 0; lineNum < numLines; lineNum++) {

                    var subStringStart = lastEnd;
                    if (node.label.charAt(subStringStart) === " ") {
                        subStringStart + 1;
                    }
                    var subStringEnd = (lineNum + 1) * lineLength[size];

                    text = node.label.substring(subStringStart, subStringEnd);
                    lastEnd = subStringEnd;

                    // modify substring if necessary
                    if (node.label.charAt(subStringEnd - 1) !== " " && node.label.charAt(subStringEnd - 2) === " " && node.label.charAt(subStringEnd) !== " ") {
                        subStringEnd = subStringEnd - 1;
                        lastEnd = subStringEnd;
                        text = node.label.substring(subStringStart, subStringEnd);
                    } else if (node.label.charAt(subStringEnd) !== " " && node.label.charAt(subStringEnd - 1) !== " " && (node.label.charAt(subStringEnd + 1) === " " || node.label.charAt(subStringEnd + 1) === "")) {
                        // new line would start with character and has a space following
                        subStringEnd = subStringEnd + 1;
                        lastEnd = subStringEnd;
                        text = node.label.substring(subStringStart, subStringEnd);
                    } else if (lineNum !== 1 && node.label.charAt(subStringEnd - 1) !== " " && node.label.charAt(subStringEnd - 2) !== " " && node.label.charAt(subStringEnd) !== " ") {
                        var lastHyphenPosition = -1;
                        var islastAHyphen = false;

                        var cont = true;
                        for (var pos = 0; pos < hyphenatedLabel.length; pos++) {
                            var n = hyphenatedLabel[pos];
                            if (hyphenatedLabel[pos] !== "-") {
                                realLength++;
                                if (hyphenatedLabel[pos] === " ") {
                                    lastHyphenPosition = realLength;
                                    islastAHyphen = false;
                                }
                            } else {
                                lastHyphenPosition = realLength;
                                islastAHyphen = true;
                            }

                            if (!cont) {
                                break;
                            }
                            if (realLength === subStringEnd) {
                                cont = false;
                            }
                        }
                        if (lastHyphenPosition === -1) {
                            lastHyphenPosition = subStringEnd;
                            islastAHyphen = true;
                        }
                        text = node.label.substring(subStringStart, lastHyphenPosition);
                        if (islastAHyphen) {
                            text = text + "-";
                        }
                        lastEnd = lastHyphenPosition;
                    }

                    if (text !== "") {
                        lines.push(text);
                    }
                }
            } else {
                lines.push(node.label);
            }

            if (lines.length === 3) {
                // 3 lines
                var numLines = 3;
                var text = "";
                for (var lineNum = 0; lineNum < numLines; lineNum++) {
                    var centerY = 0;
                    var y_centerLine1 = y_center - 15;
                    var y_centerLine2 = y_center;
                    var y_centerLine3 = y_center + 15;

                    if (lineNum == 0) {
                        centerY = y_centerLine1;
                    }
                    if (lineNum == 1) {
                        centerY = y_centerLine2;
                    }
                    if (lineNum == 2) {
                        centerY = y_centerLine3;
                    }

                    var label = document.createElementNS(svgNS, 'text');
                    label.textContent = lines[lineNum];
                    label.setAttribute('x', x_center);
                    label.setAttribute('y', centerY);
                    label.setAttribute("text-anchor", "middle");
                    label.setAttribute("dominant-baseline", "central");
                    label.setAttribute("style", "font-size:" + size + "px");
                    group.appendChild(label);
                }
            } else if (lines.length === 2) {
                // 2 lines
                var numLines = 2;
                for (var lineNum = 0; lineNum < numLines; lineNum++) {
                    var centerY = 0;
                    var y_centerLine1 = y_center - 7.5;
                    var y_centerLine2 = y_center + 7.5;

                    if (lineNum == 0) {
                        centerY = y_centerLine1;
                    }
                    if (lineNum == 1) {
                        centerY = y_centerLine2;
                    }

                    var label = document.createElementNS(svgNS, 'text');
                    label.textContent = lines[lineNum];
                    label.setAttribute('x', x_center);
                    label.setAttribute('y', centerY);
                    label.setAttribute("text-anchor", "middle");
                    label.setAttribute("dominant-baseline", "central");
                    label.setAttribute("style", "font-size:" + size + "px");
                    group.appendChild(label);
                }
            } else {
                // 1 line
                var label = document.createElementNS(svgNS, 'text');
                label.textContent = lines[0];
                label.setAttribute('x', x_center);
                label.setAttribute('y', y_center);
                label.setAttribute("style", "font-size:" + size + "px");
                label.setAttribute("text-anchor", "middle");
                label.setAttribute("dominant-baseline", "central");
                group.appendChild(label);
            }
        }
    }

    function GenericEdge(source, target, hasArrow, color) {
        this.source = source;
        this.target = target;
        this.color = color;
        this.x_Positions = [];
        this.y_Positions = [];

        this.x_Positions_hierarchical = [];
        this.y_Positions_hierarchical = [];

        this.x_Positions_RMM = [];
        this.y_Positions_RMM = [];

        this.hasArrow = hasArrow;
        this.dummyNodes = [];
        this.reversed = false;
        this.useRMM = false;
    }

    GenericEdge.prototype.draw = function () {
        var edge = this;
        var pointArray = [];
        for (var currentPoint = 0; currentPoint < edge.x_Positions.length; currentPoint++) {
            pointArray[currentPoint] = edge.x_Positions[currentPoint] + "," + edge.y_Positions[currentPoint];
        }
        var points = "";
        for (var pointNum = 0; pointNum < pointArray.length; pointNum++) {
            points = points + pointArray[pointNum] + " ";
        }

        var line = document.createElementNS(svgNS, 'polyline');
        line.setAttribute("points", points);
        line.setAttribute("style", "fill:none;stroke:" + edge.color + ";stroke-width:1");
        epcObject.svg.appendChild(line);

        if (edge.hasArrow) {
            var startPfeilspitzeX = edge.x_Positions[edge.x_Positions.length - 1];
            var startPfeilspitzeY = edge.y_Positions[edge.y_Positions.length - 1];
            var xFirst = edge.x_Positions[0];
            var xLast = edge.x_Positions[edge.x_Positions.length - 1];
            if (edge.y_Positions[edge.y_Positions.length - 2] > edge.y_Positions[edge.y_Positions.length - 1] || edge.reversed) {
                // back edge

                if (edge.reversed) {
                    startPfeilspitzeX = edge.x_Positions[0];
                    startPfeilspitzeY = edge.y_Positions[0];
                }

                var pointArrayPfeil = [];


                pointArrayPfeil[0] = startPfeilspitzeX + "," + startPfeilspitzeY;
                pointArrayPfeil[1] = (startPfeilspitzeX + 5) + "," + (startPfeilspitzeY + 10);
                pointArrayPfeil[2] = startPfeilspitzeX + "," + (startPfeilspitzeY + 7);
                pointArrayPfeil[3] = (startPfeilspitzeX - 5) + "," + (startPfeilspitzeY + 10);
                pointArrayPfeil[4] = startPfeilspitzeX + "," + startPfeilspitzeY;



                var pointsPfeil = "";
                for (var pointNum = 0; pointNum < pointArrayPfeil.length; pointNum++) {
                    pointsPfeil = pointsPfeil + pointArrayPfeil[pointNum] + " ";
                }
                var line = document.createElementNS(svgNS, 'polyline');
                line.setAttribute("points", pointsPfeil);
                line.setAttribute("style", "fill:" + edge.color + ";stroke:" + edge.color + ";stroke-width:1");



                epcObject.svg.appendChild(line);


            } else if (edge.y_Positions[edge.y_Positions.length - 2] < edge.y_Positions[edge.y_Positions.length - 1]) {
                // forward edge

                var pointArrayPfeil = [];


                pointArrayPfeil[0] = startPfeilspitzeX + "," + startPfeilspitzeY;
                pointArrayPfeil[1] = (startPfeilspitzeX + 5) + "," + (startPfeilspitzeY - 10);
                pointArrayPfeil[2] = startPfeilspitzeX + "," + (startPfeilspitzeY - 7);
                pointArrayPfeil[3] = (startPfeilspitzeX - 5) + "," + (startPfeilspitzeY - 10);
                pointArrayPfeil[4] = startPfeilspitzeX + "," + startPfeilspitzeY;


                var pointsPfeil = "";
                for (var pointNum = 0; pointNum < pointArrayPfeil.length; pointNum++) {
                    pointsPfeil = pointsPfeil + pointArrayPfeil[pointNum] + " ";
                }
                var line = document.createElementNS(svgNS, 'polyline');
                line.setAttribute("points", pointsPfeil);
                line.setAttribute("style", "fill:" + edge.color + ";stroke:" + edge.color + ";stroke-width:1");



                var ex = edge.x_Positions[edge.x_Positions.length - 2];
                var ey = edge.y_Positions[edge.y_Positions.length - 2];
                var cx = edge.x_Positions[edge.x_Positions.length - 1];
                var cy = edge.y_Positions[edge.y_Positions.length - 1];


                var dy = ey - cy;
                var dx = ex - cx;
                var angle = Math.atan2(dy, dx);
                angle = angle * (180 / Math.PI);
                angle = angle + 90;

                line.setAttribute("transform", "rotate(" + angle + " " + startPfeilspitzeX + " " + startPfeilspitzeY + ")");

                epcObject.svg.appendChild(line);


            } else {
                // side edge
                var pointArrayPfeil = [];

                if (edge.x_Positions[edge.x_Positions.length - 2] > edge.x_Positions[edge.x_Positions.length - 1]) {
                    // edge from right
                    pointArrayPfeil[0] = startPfeilspitzeX + "," + startPfeilspitzeY;
                    pointArrayPfeil[1] = (startPfeilspitzeX + 10) + "," + (startPfeilspitzeY + 5);
                    pointArrayPfeil[2] = (startPfeilspitzeX + 7) + "," + (startPfeilspitzeY);
                    pointArrayPfeil[3] = (startPfeilspitzeX + 10) + "," + (startPfeilspitzeY - 5);
                    pointArrayPfeil[4] = startPfeilspitzeX + "," + startPfeilspitzeY;
                } else if (edge.x_Positions[edge.x_Positions.length - 2] < edge.x_Positions[edge.x_Positions.length - 1]) {
                    // edge from left
                    pointArrayPfeil[0] = startPfeilspitzeX + "," + startPfeilspitzeY;
                    pointArrayPfeil[1] = (startPfeilspitzeX - 10) + "," + (startPfeilspitzeY + 5);
                    pointArrayPfeil[2] = (startPfeilspitzeX - 7) + "," + (startPfeilspitzeY);
                    pointArrayPfeil[3] = (startPfeilspitzeX - 10) + "," + (startPfeilspitzeY - 5);
                    pointArrayPfeil[4] = startPfeilspitzeX + "," + startPfeilspitzeY;
                }



                var pointsPfeil = "";
                for (var pointNum = 0; pointNum < pointArrayPfeil.length; pointNum++) {
                    pointsPfeil = pointsPfeil + pointArrayPfeil[pointNum] + " ";
                }
                var line = document.createElementNS(svgNS, 'polyline');
                line.setAttribute("points", pointsPfeil);
                line.setAttribute("style", "fill:" + edge.color + ";stroke:" + edge.color + ";stroke-width:1");
                epcObject.svg.appendChild(line);

            }
        }

    };

    function GenericNode(id, label, x_inEPC, y_inEPC, height, width, defaultColor) {
        this.id = id;
        this.label = label;
        this.x_inEPC = x_inEPC;
        this.y_inEPC = y_inEPC;
        this.height = height;
        this.width = width;

        this.useRMM = false;

        this.x_inEPC_hierarchical = undefined;
        this.y_inEPC_hierarchical = undefined;
        this.height_hierarchical = height;
        this.width_hierarchical = width;

        this.x_inEPC_RMM = undefined;
        this.y_inEPC_RMM = undefined;
        this.height_RMM = undefined;
        this.width_RMM = undefined;

        this.colors = [];
        this.matches = [];
        this.defaultColor = defaultColor;
        this.marked = false;
        this.temporaryMarked = false;
    }

    GenericNode.prototype.draw = function () {
        console.log("draw Generic Node!");
    };

    function WorkingOrControlNode(id, label, x_inEPC, y_inEPC, height, width, defaultColor) {
        GenericNode.call(this, id, label, x_inEPC, y_inEPC, height, width, defaultColor);

        this.predecessors = [];
        this.sucsessors = [];
        this.originalPredecessors = [];
        this.originalSucsessors = [];
        this.dummyPredecessors = [];
        this.dummySucsessors = [];
        this.assignedObjects = [];
    }
    ;

    function InformationNode(id, label, x_inEPC, y_inEPC, height, width, defaultColor) {
        GenericNode.call(this, id, label, x_inEPC, y_inEPC, height, width, defaultColor);

        this.sourceNodes = [];
    }
    ;

    WorkingOrControlNode.prototype = Object.create(GenericNode.prototype);
    WorkingOrControlNode.prototype.constructor = WorkingOrControlNode;
    InformationNode.prototype = Object.create(GenericNode.prototype);
    InformationNode.prototype.constructor = InformationNode;


    function OrganisationObjectNode(id, label, x_inEPC, y_inEPC, height, width, defaultColor) {
        InformationNode.call(this, id, label, x_inEPC, y_inEPC, height, width, defaultColor);
    }
    ;

    OrganisationObjectNode.prototype = Object.create(InformationNode.prototype);
    OrganisationObjectNode.prototype.constructor = OrganisationObjectNode;

    OrganisationObjectNode.prototype.draw = function () {
        var node = this;
        var group = document.createElementNS(svgNS, 'g');
        var ellipse = document.createElementNS(svgNS, 'ellipse');
        var x_leftTop = node.x_inEPC - (node.width / 2);
        var x_center = node.x_inEPC;
        var y_center = node.y_inEPC;
        var y_leftTop = node.y_inEPC - node.height / 2;
        var factor = node.height / node.width;
        ellipse.setAttribute('cx', x_center);
        ellipse.setAttribute('cy', y_center);
        ellipse.setAttribute('rx', (node.width / 2));
        ellipse.setAttribute('ry', (node.height / 2));
        ellipse.setAttribute('style', 'fill:' + node.defaultColor + ';stroke:' + epcObject.strokeColor);
        var x = x_center;
        var y = y_center;
        var line = document.createElementNS(svgNS, 'line');
        var x1 = (x - 0.85 * (node.width / 2));
        var y1 = (y - (node.height / 2) / 2 - 1);
        var x2 = (x - 0.85 * (node.width / 2));
        var y2 = (y + (node.height / 2) / 2 + 1);
        line.setAttribute("x1", x1);
        line.setAttribute("y1", y1);
        line.setAttribute("x2", x2);
        line.setAttribute("y2", y2);
        line.setAttribute("style", "stroke:" + epcObject.strokeColor + ";stroke-width:1");

        group.appendChild(ellipse);
        group.appendChild(line);

        epcObject.drawLabel(node, group);

        group.setAttribute('class', 'node');
        var id = "svgNodeID_" + epcObject.svgID;
        group.setAttribute('id', id);
        epcObject.mapSVGIDToNode[id] = node;
        epcObject.svgID++;

        var oldThis = this;


        $(group).mousedown(function (evt) {
            if (oldThis.matchViz !== undefined && oldThis.matchViz !== null && evt.button === 0) {
                if (this.debug) {
                    console.log("clicked on Node");
                }

                if (evt.ctrlKey) {
                    var item = undefined;
                    if (this.children.length > 3) {
                        item = this.children[2];
                    } else {
                        item = this.children[0];
                    }
                    if (oldThis.matchViz.contains(oldThis.selectedNodes, item)) {
                        // node is already selected -> unselect
                        var index = oldThis.selectedNodes.indexOf(item);
                        if (index > -1) {
                            oldThis.selectedNodes.splice(index, 1);
                            oldThis.selectedNodesInThisEPC.splice(index, 1);
                        }
                        oldThis.matchViz.removeSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "1", "stroke": "black"});
                    } else {
                        oldThis.selectedNodes.push(item);
                        oldThis.selectedNodesInThisEPC.push({nodeID: node.id, modelID: oldThis.name, vizEPC: oldThis});
                        oldThis.matchViz.addSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "3", "stroke": "black"});
                    }
                } else {
                    var item = undefined;
                    if (this.children.length > 3) {
                        item = this.children[2];
                    } else {
                        item = this.children[0];
                    }

                    if (oldThis.matchViz.contains(oldThis.selectedNodes, item)) {
                        // node is already selected -> unselect
                        var index = oldThis.selectedNodes.indexOf(item);
                        if (index > -1) {
                            oldThis.selectedNodes.splice(index, 1);
                            oldThis.selectedNodesInThisEPC.splice(index, 1);
                        }
                        oldThis.matchViz.removeSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "1", "stroke": "black"});
                    } else {
                        if (oldThis.selectedNodes.length > 0) {
                            for (var i = 0; i < oldThis.selectedNodes.length; i++) {
                                $(oldThis.selectedNodes[i]).css({"strokeWidth": "1", "stroke": "black"});
                                oldThis.matchViz.removeSelectedNode(oldThis.selectedNodesInThisEPC[i].nodeID, oldThis.selectedNodesInThisEPC[i].modelID, oldThis.selectedNodesInThisEPC[i].vizEPC);
                            }
                            oldThis.selectedNodes = [];
                            oldThis.selectedNodesInThisEPC = [];
                        }

                        oldThis.selectedNodes[0] = item;
                        oldThis.selectedNodesInThisEPC[0] = {nodeID: node.id, modelID: oldThis.name, vizEPC: oldThis};
                        oldThis.matchViz.addSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "3", "stroke": "black"});
                    }


                }
            }

        });


        epcObject.svg.appendChild(group);
        epcObject.container.appendChild(epcObject.svg);

    }


    function DataObjectNode(id, label, x_inEPC, y_inEPC, height, width, defaultColor) {
        InformationNode.call(this, id, label, x_inEPC, y_inEPC, height, width, defaultColor);
    }
    ;

    DataObjectNode.prototype = Object.create(InformationNode.prototype);
    DataObjectNode.prototype.constructor = DataObjectNode;

    DataObjectNode.prototype.draw = function () {
        var node = this;
        var group = document.createElementNS(svgNS, 'g');
        var rect = document.createElementNS(svgNS, 'rect');
        var x_leftTop = node.x_inEPC - (node.width / 2);
        var x_center = node.x_inEPC;
        var y_center = node.y_inEPC;
        var y_leftTop = node.y_inEPC - node.height / 2;
        rect.setAttribute('x', x_leftTop);
        rect.setAttribute('y', y_leftTop);
        rect.setAttribute('width', (node.width));
        rect.setAttribute('height', (node.height));
        rect.setAttribute('style', 'fill:' + node.defaultColor + ';stroke:' + epcObject.strokeColor);

        var rect2 = document.createElementNS(svgNS, 'rect');
        rect2.setAttribute('x', x_leftTop + 5);
        rect2.setAttribute('y', y_leftTop);
        rect2.setAttribute('width', (node.width - 10));
        rect2.setAttribute('height', (node.height));
        rect2.setAttribute('style', 'fill:' + node.defaultColor + ';stroke:' + epcObject.strokeColor);

        var x = x_center;
        var y = y_center;

        group.appendChild(rect);
        group.appendChild(rect2);

        epcObject.drawLabel(node, group);

        group.setAttribute('class', 'node');
        var id = "svgNodeID_" + epcObject.svgID;
        group.setAttribute('id', id);
        epcObject.mapSVGIDToNode[id] = node;
        epcObject.svgID++;

        var oldThis = this;


        $(group).mousedown(function (evt) {
            if (oldThis.matchViz !== undefined && oldThis.matchViz !== null && evt.button === 0) {
                if (this.debug) {
                    console.log("clicked on Node");
                }

                if (evt.ctrlKey) {
                    var item = undefined;
                    if (this.children.length > 3) {
                        item = this.children[2];
                    } else {
                        item = this.children[0];
                    }
                    if (oldThis.matchViz.contains(oldThis.selectedNodes, item)) {
                        // node is already selected -> unselect
                        var index = oldThis.selectedNodes.indexOf(item);
                        if (index > -1) {
                            oldThis.selectedNodes.splice(index, 1);
                            oldThis.selectedNodesInThisEPC.splice(index, 1);
                        }
                        oldThis.matchViz.removeSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "1", "stroke": "black"});
                    } else {
                        oldThis.selectedNodes.push(item);
                        oldThis.selectedNodesInThisEPC.push({nodeID: node.id, modelID: oldThis.name, vizEPC: oldThis});
                        oldThis.matchViz.addSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "3", "stroke": "black"});
                    }
                } else {
                    var item = undefined;
                    if (this.children.length > 3) {
                        item = this.children[2];
                    } else {
                        item = this.children[0];
                    }

                    if (oldThis.matchViz.contains(oldThis.selectedNodes, item)) {
                        // node is already selected -> unselect
                        var index = oldThis.selectedNodes.indexOf(item);
                        if (index > -1) {
                            oldThis.selectedNodes.splice(index, 1);
                            oldThis.selectedNodesInThisEPC.splice(index, 1);
                        }
                        oldThis.matchViz.removeSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "1", "stroke": "black"});
                    } else {
                        if (oldThis.selectedNodes.length > 0) {
                            for (var i = 0; i < oldThis.selectedNodes.length; i++) {
                                $(oldThis.selectedNodes[i]).css({"strokeWidth": "1", "stroke": "black"});
                                oldThis.matchViz.removeSelectedNode(oldThis.selectedNodesInThisEPC[i].nodeID, oldThis.selectedNodesInThisEPC[i].modelID, oldThis.selectedNodesInThisEPC[i].vizEPC);
                            }
                            oldThis.selectedNodes = [];
                            oldThis.selectedNodesInThisEPC = [];
                        }

                        oldThis.selectedNodes[0] = item;
                        oldThis.selectedNodesInThisEPC[0] = {nodeID: node.id, modelID: oldThis.name, vizEPC: oldThis};
                        oldThis.matchViz.addSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "3", "stroke": "black"});
                    }


                }
            }

        });


        epcObject.svg.appendChild(group);
        epcObject.container.appendChild(epcObject.svg);

    }


    function FunctionNode(id, label, x_inEPC, y_inEPC, height, width, defaultColor) {
        if (defaultColor === undefined) {
            defaultColor = '#80ff80';
        }
        WorkingOrControlNode.call(this, id, label, x_inEPC, y_inEPC, height, width, defaultColor);
    }
    ;

    FunctionNode.prototype = Object.create(WorkingOrControlNode.prototype);
    FunctionNode.prototype.constructor = FunctionNode;

    FunctionNode.prototype.draw = function () {
        var group = document.createElementNS(svgNS, 'g');
        var node = this;
        var x_leftTop = node.x_inEPC - (node.width / 2);
        var x_center = node.x_inEPC;
        var y_center = node.y_inEPC;
        var y_leftTop = node.y_inEPC - node.height / 2;

        if (this.useRMM) {
            x_leftTop = this.x_inEPC;
            x_center = this.x_inEPC + this.width / 2;
            y_center = this.y_inEPC + this.height / 2;
            y_leftTop = this.y_inEPC;
        }

        var numColors = 2;
        var colWidth = (node.width) / numColors;



        if (numColors === 1) {
            var path = document.createElementNS(svgNS, 'path');
            path.setAttribute('d', 'M' + (x_leftTop + 10) + ',' + (y_leftTop) + ' h' + (node.width - 20) + ' a10,10 0 0 1 10,10 v' + (node.height - 20) + ' a10,10 0 0 1 -10,10 h-' + (node.width - 20) + ' a10,10 0 0 1 -10,-10 v-' + (node.height - 20) + ' a10,10 0 0 1 10,-10 z');
            path.setAttribute('fill', this.defaultColor);
            group.appendChild(path);
            epcObject.shapes[node.id] = [path];
        } else if (numColors === 2) {
            var path = document.createElementNS(svgNS, 'path');
            path.setAttribute('d', 'M' + (x_leftTop + 10) + ',' + (y_leftTop) + ' h' + (colWidth - 5) + ' v' + (node.height) + ' h-' + (colWidth - 5) + ' a10,10 0 0 1 -10,-10 v-' + (node.height - 20) + ' a10,10 0 0 1 10,-10 z');
            path.setAttribute('fill', this.defaultColor);
            path.setAttribute('stroke', this.defaultColor);
            path.setAttribute('stroke-width', 0);
            group.appendChild(path);
            epcObject.shapes[node.id] = [path];

            var path = document.createElementNS(svgNS, 'path');
            path.setAttribute('d', 'M' + (x_leftTop + ((node.width) - colWidth)) + ',' + (y_leftTop) + ' h' + (colWidth - 10) + ' a10,10 0 0 1 10,10 v' + (node.height - 20) + ' a10,10 0 0 1 -10,10 h-' + (colWidth - 10) + ' z');
            path.setAttribute('fill', this.defaultColor);
            path.setAttribute('stroke', this.defaultColor);
            path.setAttribute('stroke-width', 0);
            group.appendChild(path);
            epcObject.shapes[node.id].push(path);
        } else {
            // more than 2 colors
            var path = document.createElementNS(svgNS, 'path');
            path.setAttribute('d', 'M' + (x_leftTop + 10) + ',' + (y_leftTop) + ' h' + (colWidth - 10) + ' v' + (node.height) + ' h-' + (colWidth - 10) + ' a10,10 0 0 1 -10,-10 v-' + (node.height - 20) + ' a10,10 0 0 1 10,-10 z');
            path.setAttribute('fill', 'yellow');
            group.appendChild(path);
            epcObject.shapes[node.id] = [path];

            for (var colNum = 1; colNum < numColors - 1; colNum++) {
                var path = document.createElementNS(svgNS, 'path');
                path.setAttribute('d', 'M' + (x_leftTop + (colWidth * colNum)) + ',' + (y_leftTop) + ' h' + (colWidth) + ' v' + (node.height) + ' h-' + (colWidth) + ' z');
                path.setAttribute('fill', 'red');
                group.appendChild(path);
                epcObject.shapes[node.id].push(path);
            }

            var path = document.createElementNS(svgNS, 'path');
            path.setAttribute('d', 'M' + (x_leftTop + ((node.width) - colWidth)) + ',' + (y_leftTop) + ' h' + (colWidth - 10) + ' a10,10 0 0 1 10,10 v' + (node.height - 20) + ' a10,10 0 0 1 -10,10 h-' + (colWidth - 10) + ' z');
            path.setAttribute('fill', 'blue');
            group.appendChild(path);
            epcObject.shapes[node.id].push(path);
        }

        var rect = document.createElementNS(svgNS, 'rect');

        rect.setAttribute('x', x_leftTop);
        rect.setAttribute('rx', 10);
        rect.setAttribute('ry', 10);
        rect.setAttribute('y', y_leftTop);
        rect.setAttribute('width', node.width);
        rect.setAttribute('height', node.height);
        rect.setAttribute('fill', this.defaultColor);
        rect.setAttribute('fill-opacity', 0.0);
        rect.setAttribute('stroke', epcObject.strokeColor);
        group.appendChild(rect);

        epcObject.drawLabel(node, group, x_center, y_center);

        group.setAttribute('class', 'node');
        var id = "svgNodeID_" + epcObject.svgID;
        group.setAttribute('id', id);
        epcObject.mapSVGIDToNode[id] = node;
        epcObject.svgID++;



        var warningGroup = document.createElementNS(svgNS, 'g');

        var warning = document.createElementNS(svgNS, 'rect');
        warning.setAttribute('x', x_leftTop + node.width - 15);
        warning.setAttribute('y', y_leftTop - 17.5);
        warning.setAttribute('width', 15);
        warning.setAttribute('height', 15);
        warning.setAttribute('fill', 'red');
        warning.setAttribute('fill-opacity', 1.0);
        warning.setAttribute('stroke', epcObject.strokeColor);
        warningGroup.appendChild(warning);

        var label = document.createElementNS(svgNS, 'text');
        label.textContent = "!";
        label.setAttribute('x', x_leftTop + node.width - 7.5);
        label.setAttribute('y', y_leftTop - 10);
        label.setAttribute("text-anchor", "middle");
        label.setAttribute("dominant-baseline", "central");
        label.setAttribute("style", "font-size:" + 15 + "px");

        var warningGroupTitle = document.createElementNS(svgNS, 'title');
        warningGroupTitle.textContent = "Only the first 2 Match Colors are shown!";
        warningGroup.setAttribute('visibility', 'hidden');

        warningGroup.appendChild(warningGroupTitle);
        warningGroup.appendChild(label);

        epcObject.warnings[node.id] = warningGroup;
        epcObject.svg.appendChild(warningGroup);
        epcObject.container.appendChild(epcObject.svg);





        var oldThis = epcObject;


        $(group).mousedown(function (evt) {
            if (oldThis.matchViz !== undefined && oldThis.matchViz !== null && evt.button === 0) {
                if (this.debug) {
                    console.log("clicked on Node");
                }

                if (evt.ctrlKey) {
                    var item = undefined;
                    if (this.children.length > 3) {
                        item = this.children[2];
                    } else {
                        item = this.children[0];
                    }
                    if (oldThis.matchViz.contains(oldThis.selectedNodes, item)) {
                        // node is already selected -> unselect
                        var index = oldThis.selectedNodes.indexOf(item);
                        if (index > -1) {
                            oldThis.selectedNodes.splice(index, 1);
                            oldThis.selectedNodesInThisEPC.splice(index, 1);
                        }
                        oldThis.matchViz.removeSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "1", "stroke": "black"});
                    } else {
                        oldThis.selectedNodes.push(item);
                        oldThis.selectedNodesInThisEPC.push({nodeID: node.id, modelID: oldThis.name, vizEPC: oldThis});
                        oldThis.matchViz.addSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "3", "stroke": "black"});
                    }
                } else {
                    var item = undefined;
                    if (this.children.length > 3) {
                        item = this.children[2];
                    } else {
                        item = this.children[0];
                    }

                    if (oldThis.matchViz.contains(oldThis.selectedNodes, item)) {
                        // node is already selected -> unselect
                        var index = oldThis.selectedNodes.indexOf(item);
                        if (index > -1) {
                            oldThis.selectedNodes.splice(index, 1);
                            oldThis.selectedNodesInThisEPC.splice(index, 1);
                        }
                        oldThis.matchViz.removeSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "1", "stroke": "black"});
                    } else {
                        if (oldThis.selectedNodes.length > 0) {
                            for (var i = 0; i < oldThis.selectedNodes.length; i++) {
                                $(oldThis.selectedNodes[i]).css({"strokeWidth": "1", "stroke": "black"});
                                oldThis.matchViz.removeSelectedNode(oldThis.selectedNodesInThisEPC[i].nodeID, oldThis.selectedNodesInThisEPC[i].modelID, oldThis.selectedNodesInThisEPC[i].vizEPC);
                            }
                            oldThis.selectedNodes = [];
                            oldThis.selectedNodesInThisEPC = [];
                        }

                        oldThis.selectedNodes[0] = item;
                        oldThis.selectedNodesInThisEPC[0] = {nodeID: node.id, modelID: oldThis.name, vizEPC: oldThis};
                        oldThis.matchViz.addSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "3", "stroke": "black"});
                    }


                }
            }

        });


        epcObject.svg.appendChild(group);
        epcObject.container.appendChild(epcObject.svg);
    };


    function EventNode(id, label, x_inEPC, y_inEPC, height, width, defaultColor) {
        if (defaultColor === undefined) {
            defaultColor = '#FF8080';
        }
        WorkingOrControlNode.call(this, id, label, x_inEPC, y_inEPC, height, width, defaultColor);
    }
    ;

    EventNode.prototype = Object.create(WorkingOrControlNode.prototype);
    EventNode.prototype.constructor = EventNode;

    EventNode.prototype.draw = function () {
        var node = this;
        var group = document.createElementNS(svgNS, 'g');
        var polygon = document.createElementNS(svgNS, 'polygon');
        var x_leftTop = this.x_inEPC - (this.width / 2);
        var x_center = this.x_inEPC;
        var y_center = this.y_inEPC;
        var y_leftTop = this.y_inEPC - this.height / 2;

        if (this.useRMM) {
            x_leftTop = this.x_inEPC;
            x_center = this.x_inEPC + this.width / 2;
            y_center = this.y_inEPC + this.height / 2;
            y_leftTop = this.y_inEPC;
        }


        var x = x_leftTop;
        var y = y_leftTop;

        var width = this.width;
        var height = this.height;



        var pointArray = [];
        pointArray[0] = (0.0 * width + x) + "," + (y + (height / 2));
        pointArray[1] = (0.1 * width + x) + "," + (y + height);
        pointArray[2] = (x + 0.5 * width) + "," + (y + height);
        pointArray[3] = (x + 0.5 * width) + "," + (y);
        pointArray[4] = (x + 0.1 * width) + "," + (y);

        var points = "";
        for (var pointNum = 0; pointNum < pointArray.length; pointNum++) {
            points = points + pointArray[pointNum] + " ";
        }

        var poly = document.createElementNS(svgNS, 'polygon');
        poly.setAttribute('points', points);
        poly.setAttribute('x', x_leftTop);
        poly.setAttribute('y', y_leftTop);
        poly.setAttribute('width', this.width);
        poly.setAttribute('height', this.height);
        poly.setAttribute('fill', this.defaultColor);
        poly.setAttribute('stroke', this.defaultColor);
        poly.setAttribute('stroke-width', 0);
        group.appendChild(poly);
        epcObject.shapes[node.id] = [poly];




        pointArray = [];
        pointArray[0] = (0.5 * width + x - 1) + "," + (y + height);
        pointArray[1] = (x + 0.9 * width) + "," + (y + height);
        pointArray[2] = (x + 1.0 * width) + "," + (y + (height / 2));
        pointArray[3] = (x + 0.9 * width) + "," + (y);
        pointArray[4] = (0.5 * width + x - 1) + "," + (y);

        points = "";
        for (var pointNum = 0; pointNum < pointArray.length; pointNum++) {
            points = points + pointArray[pointNum] + " ";
        }

        var poly = document.createElementNS(svgNS, 'polygon');
        poly.setAttribute('points', points);
        poly.setAttribute('x', x_leftTop);
        poly.setAttribute('y', y_leftTop);
        poly.setAttribute('width', this.width);
        poly.setAttribute('height', this.height);
        poly.setAttribute('fill', this.defaultColor);
        poly.setAttribute('stroke', this.defaultColor);
        poly.setAttribute('stroke-width', 0);
        group.appendChild(poly);
        epcObject.shapes[node.id].push(poly);




        pointArray = [];
        pointArray[0] = (x + 0.9 * width) + "," + (y + height);
        pointArray[1] = (x + 1.0 * width) + "," + (y + (height / 2));
        pointArray[2] = (x + 0.9 * width) + "," + (y);
        pointArray[3] = (0.1 * width + x) + "," + (y);
        pointArray[4] = (0.0 * width + x) + "," + (y + (height / 2));
        pointArray[5] = (0.1 * width + x) + "," + (y + height);
        pointArray[6] = (x + 0.9 * width) + "," + (y + height);

        var points = "";
        for (var pointNum = 0; pointNum < pointArray.length; pointNum++) {
            points = points + pointArray[pointNum] + " ";
        }

        polygon.setAttribute('points', points);
        polygon.setAttribute('x', x_leftTop);
        polygon.setAttribute('y', y_leftTop);
        polygon.setAttribute('width', this.width);
        polygon.setAttribute('height', this.height);
        polygon.setAttribute('fill-opacity', 0.0);
        polygon.setAttribute('stroke', epcObject.strokeColor);
        group.appendChild(polygon);

        epcObject.drawLabel(node, group, x_center, y_center);

        group.setAttribute('class', 'node');
        var id = "svgNodeID_" + epcObject.svgID;
        group.setAttribute('id', id);
        epcObject.mapSVGIDToNode[id] = node;
        epcObject.svgID++;

        var warningGroup = document.createElementNS(svgNS, 'g');

        var warning = document.createElementNS(svgNS, 'rect');
        warning.setAttribute('x', x_leftTop + node.width - 15);
        warning.setAttribute('y', y_leftTop - 17.5);
        warning.setAttribute('width', 15);
        warning.setAttribute('height', 15);
        warning.setAttribute('fill', 'red');
        warning.setAttribute('fill-opacity', 1.0);
        warning.setAttribute('stroke', epcObject.strokeColor);
        warningGroup.appendChild(warning);

        var label = document.createElementNS(svgNS, 'text');
        label.textContent = "!";
        label.setAttribute('x', x_leftTop + node.width - 7.5);
        label.setAttribute('y', y_leftTop - 10);
        label.setAttribute("text-anchor", "middle");
        label.setAttribute("dominant-baseline", "central");
        label.setAttribute("style", "font-size:" + 15 + "px");

        var warningGroupTitle = document.createElementNS(svgNS, 'title');
        warningGroupTitle.textContent = "Only the first 2 Match Colors are shown!";
        warningGroup.setAttribute('visibility', 'hidden');

        warningGroup.appendChild(label);

        epcObject.warnings[node.id] = warningGroup;
        epcObject.svg.appendChild(warningGroup);
        epcObject.container.appendChild(epcObject.svg);

        var oldThis = epcObject;


        $(group).mousedown(function (evt) {
            if (oldThis.matchViz !== undefined && oldThis.matchViz !== null && evt.button === 0) {
                if (this.debug) {
                    console.log("clicked on Node");
                }

                if (evt.ctrlKey) {
                    var item = undefined;
                    if (this.children.length > 3) {
                        item = this.children[2];
                    } else {
                        item = this.children[0];
                    }
                    if (oldThis.matchViz.contains(oldThis.selectedNodes, item)) {
                        // node is already selected -> unselect
                        var index = oldThis.selectedNodes.indexOf(item);
                        if (index > -1) {
                            oldThis.selectedNodes.splice(index, 1);
                            oldThis.selectedNodesInThisEPC.splice(index, 1);
                        }
                        oldThis.matchViz.removeSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "1", "stroke": "black"});
                    } else {
                        oldThis.selectedNodes.push(item);
                        oldThis.selectedNodesInThisEPC.push({nodeID: node.id, modelID: oldThis.name, vizEPC: oldThis});
                        oldThis.matchViz.addSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "3", "stroke": "black"});
                    }
                } else {
                    var item = undefined;
                    if (this.children.length > 3) {
                        item = this.children[2];
                    } else {
                        item = this.children[0];
                    }

                    if (oldThis.matchViz.contains(oldThis.selectedNodes, item)) {
                        // node is already selected -> unselect
                        var index = oldThis.selectedNodes.indexOf(item);
                        if (index > -1) {
                            oldThis.selectedNodes.splice(index, 1);
                            oldThis.selectedNodesInThisEPC.splice(index, 1);
                        }
                        oldThis.matchViz.removeSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "1", "stroke": "black"});
                    } else {
                        if (oldThis.selectedNodes.length > 0) {
                            for (var i = 0; i < oldThis.selectedNodes.length; i++) {
                                $(oldThis.selectedNodes[i]).css({"strokeWidth": "1", "stroke": "black"});
                                oldThis.matchViz.removeSelectedNode(oldThis.selectedNodesInThisEPC[i].nodeID, oldThis.selectedNodesInThisEPC[i].modelID, oldThis.selectedNodesInThisEPC[i].vizEPC);
                            }
                            oldThis.selectedNodes = [];
                            oldThis.selectedNodesInThisEPC = [];
                        }

                        oldThis.selectedNodes[0] = item;
                        oldThis.selectedNodesInThisEPC[0] = {nodeID: node.id, modelID: oldThis.name, vizEPC: oldThis};
                        oldThis.matchViz.addSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "3", "stroke": "black"});
                    }


                }
            }

        });


        epcObject.svg.appendChild(group);
        epcObject.container.appendChild(epcObject.svg);
    };


    function OperatorNode(id, label, x_inEPC, y_inEPC, height, width, defaultColor) {
        if (defaultColor === undefined) {
            defaultColor = 'gray';
        }
        WorkingOrControlNode.call(this, id, label, x_inEPC, y_inEPC, height, width, defaultColor);
    }
    ;

    OperatorNode.prototype = Object.create(WorkingOrControlNode.prototype);
    OperatorNode.prototype.constructor = OperatorNode;

    OperatorNode.prototype.draw = function () {
        var node = this;
        var x = node.x_inEPC;
        var y = node.y_inEPC;

        if (this.useRMM) {
            x = node.x_inEPC + node.width / 2;
            y = node.y_inEPC + node.height / 2;
        }
        var group = document.createElementNS(svgNS, 'g');


        var circle2 = document.createElementNS(svgNS, 'circle');
        circle2.setAttribute('cx', x);
        circle2.setAttribute('cy', y);
        circle2.setAttribute("r", (node.height / 2));
        circle2.setAttribute('stroke', node.defaultColor);
        circle2.setAttribute('fill', 'white');
        circle2.setAttribute('stroke-width', 0);
        group.appendChild(circle2);
        epcObject.shapes[node.id] = [circle2];
        var height = node.height;

        var path = document.createElementNS(svgNS, 'path');
        var highpointy = y - (node.height / 2);
        var lowPoint = y + (node.height / 2);
        var r = (node.height / 2);
        path.setAttribute('d', 'M' + x + ',' + y + ' L' + x + ',' + highpointy + ' A' + r + ',' + r + ' 1 0,1 ' + x + ',' + lowPoint + ' z');
        path.setAttribute('fill', 'white');
        path.setAttribute('stroke', this.defaultColor);
        path.setAttribute('stroke-width', 0);
        group.appendChild(path);
        epcObject.shapes[node.id].push(path);

        var circle = document.createElementNS(svgNS, 'circle');
        circle.setAttribute('cx', x);
        circle.setAttribute('cy', y);
        circle.setAttribute("r", (node.height / 2));
        circle.setAttribute('stroke', node.defaultColor);
        circle.setAttribute('fill', 'none');
        group.appendChild(circle);


        switch (node.label) {
            case "xor":
                var line = document.createElementNS(svgNS, 'line');
                line.setAttribute("x1", (x + (height / 2) / Math.sqrt(2)));
                line.setAttribute("y1", (y + (height / 2) / Math.sqrt(2)));
                line.setAttribute("x2", (x - (height / 2) / Math.sqrt(2)));
                line.setAttribute("y2", (y - (height / 2) / Math.sqrt(2)));
                line.setAttribute("style", "stroke:" + node.defaultColor + ";stroke-width:1");
                group.appendChild(line);
                var line2 = document.createElementNS(svgNS, 'line');
                line2.setAttribute("x1", (x - (height / 2) / Math.sqrt(2)));
                line2.setAttribute("y1", (y + (height / 2) / Math.sqrt(2)));
                line2.setAttribute("x2", (x + (height / 2) / Math.sqrt(2)));
                line2.setAttribute("y2", (y - (height / 2) / Math.sqrt(2)));
                line2.setAttribute("style", "stroke:" + node.defaultColor + ";stroke-width:1");
                group.appendChild(line2);

                break;

            case "and":
                var x1 = height / 2 - (height / 2 * 0.4);
                var line = document.createElementNS(svgNS, 'line');
                line.setAttribute("x1", (x + x1));
                line.setAttribute("y1", (y + x1));
                line.setAttribute("x2", (x));
                line.setAttribute("y2", (y - ((height / 2) / 1.5)));
                line.setAttribute("style", "stroke:" + node.defaultColor + ";stroke-width:1");
                group.appendChild(line);
                var line2 = document.createElementNS(svgNS, 'line');
                line2.setAttribute("x1", (x - x1));
                line2.setAttribute("y1", (y + x1));
                line2.setAttribute("x2", (x));
                line2.setAttribute("y2", (y - ((height / 2) / 1.5)));
                line2.setAttribute("style", "stroke:" + node.defaultColor + ";stroke-width:1");
                group.appendChild(line2);
                break;

            case "or":
                var x1 = height / 2 - (height / 2 * 0.4);
                var line = document.createElementNS(svgNS, 'line');
                line.setAttribute("x1", (x + x1));
                line.setAttribute("y1", (y - x1));
                line.setAttribute("x2", (x));
                line.setAttribute("y2", (y + ((height / 2) / 1.5)));
                line.setAttribute("style", "stroke:" + node.defaultColor + ";stroke-width:1");
                group.appendChild(line);
                var line2 = document.createElementNS(svgNS, 'line');
                line2.setAttribute("x1", (x - x1));
                line2.setAttribute("y1", (y - x1));
                line2.setAttribute("x2", (x));
                line2.setAttribute("y2", (y + ((height / 2) / 1.5)));
                line2.setAttribute("style", "stroke:" + node.defaultColor + ";stroke-width:1");
                group.appendChild(line2);
                break;
            default:

        }

        group.setAttribute('class', 'node');
        var id = "svgNodeID_" + epcObject.svgID;
        group.setAttribute('id', id);
        epcObject.mapSVGIDToNode[id] = node;
        epcObject.svgID++;


        var warningGroup = document.createElementNS(svgNS, 'g');

        var warning = document.createElementNS(svgNS, 'rect');
        warning.setAttribute('x', x + node.width - 15);
        warning.setAttribute('y', y - 20);
        warning.setAttribute('width', 15);
        warning.setAttribute('height', 15);
        warning.setAttribute('fill', 'red');
        warning.setAttribute('fill-opacity', 1.0);
        warning.setAttribute('stroke', epcObject.strokeColor);
        warningGroup.appendChild(warning);

        var label = document.createElementNS(svgNS, 'text');
        label.textContent = "!";
        label.setAttribute('x', x + node.width - 7.5);
        label.setAttribute('y', y - 12);
        label.setAttribute("text-anchor", "middle");
        label.setAttribute("dominant-baseline", "central");
        label.setAttribute("style", "font-size:" + 15 + "px");

        var warningGroupTitle = document.createElementNS(svgNS, 'title');
        warningGroupTitle.textContent = "Only the first 2 Match Colors are shown!";
        warningGroup.setAttribute('visibility', 'hidden');

        warningGroup.appendChild(label);

        epcObject.warnings[node.id] = warningGroup;
        epcObject.svg.appendChild(warningGroup);
        epcObject.container.appendChild(epcObject.svg);

        var oldThis = epcObject;
        $(group).mousedown(function (evt) {
            if (oldThis.matchViz !== undefined && oldThis.matchViz !== null && evt.button === 0) {

                if (evt.ctrlKey) {
                    var item = undefined;
                    if (this.children.length > 3) {
                        item = this.children[2];
                    } else {
                        item = this.children[0];
                    }
                    if (oldThis.matchViz.contains(oldThis.selectedNodes, item)) {
                        // node is already selected -> unselect
                        var index = oldThis.selectedNodes.indexOf(item);
                        if (index > -1) {
                            oldThis.selectedNodes.splice(index, 1);
                            oldThis.selectedNodesInThisEPC.splice(index, 1);
                        }
                        oldThis.matchViz.removeSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "1", "stroke": "black"});
                    } else {
                        oldThis.selectedNodes.push(item);
                        oldThis.selectedNodesInThisEPC.push({nodeID: node.id, modelID: oldThis.name, vizEPC: oldThis});
                        oldThis.matchViz.addSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "3", "stroke": "black"});
                    }
                } else {
                    var item = undefined;
                    if (this.children.length > 3) {
                        item = this.children[2];
                    } else {
                        item = this.children[0];
                    }

                    if (oldThis.matchViz.contains(oldThis.selectedNodes, item)) {
                        // node is already selected -> unselect
                        var index = oldThis.selectedNodes.indexOf(item);
                        if (index > -1) {
                            oldThis.selectedNodes.splice(index, 1);
                            oldThis.selectedNodesInThisEPC.splice(index, 1);
                        }
                        oldThis.matchViz.removeSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "1", "stroke": "black"});
                    } else {
                        if (oldThis.selectedNodes.length > 0) {
                            for (var i = 0; i < oldThis.selectedNodes.length; i++) {
                                $(oldThis.selectedNodes[i]).css({"strokeWidth": "1", "stroke": "black"});
                                oldThis.matchViz.removeSelectedNode(oldThis.selectedNodesInThisEPC[i].nodeID, oldThis.selectedNodesInThisEPC[i].modelID, oldThis.selectedNodesInThisEPC[i].vizEPC);
                            }
                            oldThis.selectedNodes = [];
                            oldThis.selectedNodesInThisEPC = [];
                        }

                        oldThis.selectedNodes[0] = item;
                        oldThis.selectedNodesInThisEPC[0] = {nodeID: node.id, modelID: oldThis.name, vizEPC: oldThis};
                        oldThis.matchViz.addSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "3", "stroke": "black"});
                    }


                }
            }

        });

        epcObject.svg.appendChild(group);
        epcObject.container.appendChild(epcObject.svg);
    };



    function ProcessInterfaceNode(id, label, x_inEPC, y_inEPC, height, width, defaultColor) {
        if (defaultColor === undefined) {
            defaultColor = '#FF8080';
        }
        WorkingOrControlNode.call(this, id, label, x_inEPC, y_inEPC, height, width, defaultColor);
    }
    ;

    ProcessInterfaceNode.prototype = Object.create(WorkingOrControlNode.prototype);
    ProcessInterfaceNode.prototype.constructor = ProcessInterfaceNode;

    ProcessInterfaceNode.prototype.draw = function () {
        var node = this;
        var group = document.createElementNS(svgNS, 'g');
        var polygon = document.createElementNS(svgNS, 'polygon');
        var x_leftTop = this.x_inEPC - (this.width / 2);
        var x_center = this.x_inEPC;
        var y_center = this.y_inEPC;
        var y_leftTop = this.y_inEPC - this.height / 2;
        var x = x_leftTop;
        var y = y_leftTop;

        var rect = document.createElementNS(svgNS, 'rect');
        var x_leftTop = node.x_inEPC - (node.width / 2);
        var x_center = node.x_inEPC;
        var y_center = node.y_inEPC;
        var y_leftTop = node.y_inEPC - node.height / 2;
        rect.setAttribute('x', x_leftTop);
        rect.setAttribute('y', y_leftTop);
        rect.setAttribute('width', (node.width - 10));
        rect.setAttribute('height', (node.height - 7));
        rect.setAttribute('style', 'fill:' + node.defaultColor + ';stroke:' + epcObject.strokeColor);


        var width = this.width;
        var height = this.height;

        var pointArray = [];
        pointArray[0] = (x + 0.9 * width) + "," + (y + height);
        pointArray[1] = (x + 1.0 * width) + "," + (y + (height / 2));
        pointArray[2] = (x + 0.9 * width) + "," + (y);
        pointArray[3] = (0.1 * width + x) + "," + (y);
        pointArray[4] = (0.0 * width + x) + "," + (y + (height / 2));
        pointArray[5] = (0.1 * width + x) + "," + (y + height);
        pointArray[6] = (x + 0.9 * width) + "," + (y + height);

        var points = "";
        for (var pointNum = 0; pointNum < pointArray.length; pointNum++) {
            points = points + pointArray[pointNum] + " ";
        }

        polygon.setAttribute('points', points);
        polygon.setAttribute('x', x_leftTop + 10);
        polygon.setAttribute('y', y_leftTop + 7);
        polygon.setAttribute('width', this.width - 10);
        polygon.setAttribute('height', this.height - 6);
        polygon.setAttribute('fill', this.defaultColor);
        polygon.setAttribute('stroke', epcObject.strokeColor);
        epcObject.shapes[this.id] = [polygon];
        group.appendChild(polygon);
        group.appendChild(rect);

        var lineLength = [];
        lineLength[12] = 13;
        lineLength[11] = 14;
        lineLength[10] = 15;
        lineLength[9] = 18;
        lineLength[8] = 21;
        lineLength[7] = 24;
        lineLength[6] = 28;
        lineLength[5] = 34;

        var size = -1;
        var oneLine = false;
        if (epcObject.getMaxLabelLength() < lineLength[12]) {
            size = 12;
            oneLine = true;
        } else {
            for (var i = 12; i >= 5; i--) {
                if (lineLength[i] * 3 >= epcObject.getMaxLabelLength()) {
                    size = i;
                    break;
                }
            }
        }

        epcObject.drawLabel(node, group, node.x_inEPC - 5, node.y_inEPC - 3);

        group.setAttribute('class', 'node');
        var id = "svgNodeID_" + epcObject.svgID;
        group.setAttribute('id', id);
        epcObject.mapSVGIDToNode[id] = node;
        epcObject.svgID++;

        var oldThis = epcObject;


        $(group).mousedown(function (evt) {
            if (oldThis.matchViz !== undefined && oldThis.matchViz !== null && evt.button === 0) {
                if (this.debug) {
                    console.log("clicked on Node");
                }

                if (evt.ctrlKey) {
                    var item = undefined;
                    if (this.children.length > 3) {
                        item = this.children[2];
                    } else {
                        item = this.children[0];
                    }
                    if (oldThis.matchViz.contains(oldThis.selectedNodes, item)) {
                        // node is already selected -> unselect
                        var index = oldThis.selectedNodes.indexOf(item);
                        if (index > -1) {
                            oldThis.selectedNodes.splice(index, 1);
                            oldThis.selectedNodesInThisEPC.splice(index, 1);
                        }
                        oldThis.matchViz.removeSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "1", "stroke": "black"});
                    } else {
                        oldThis.selectedNodes.push(item);
                        oldThis.selectedNodesInThisEPC.push({nodeID: node.id, modelID: oldThis.name, vizEPC: oldThis});
                        oldThis.matchViz.addSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "3", "stroke": "black"});
                    }
                } else {
                    var item = undefined;
                    if (this.children.length > 3) {
                        item = this.children[2];
                    } else {
                        item = this.children[0];
                    }

                    if (oldThis.matchViz.contains(oldThis.selectedNodes, item)) {
                        // node is already selected -> unselect
                        var index = oldThis.selectedNodes.indexOf(item);
                        if (index > -1) {
                            oldThis.selectedNodes.splice(index, 1);
                            oldThis.selectedNodesInThisEPC.splice(index, 1);
                        }
                        oldThis.matchViz.removeSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "1", "stroke": "black"});
                    } else {
                        if (oldThis.selectedNodes.length > 0) {
                            for (var i = 0; i < oldThis.selectedNodes.length; i++) {
                                $(oldThis.selectedNodes[i]).css({"strokeWidth": "1", "stroke": "black"});
                                oldThis.matchViz.removeSelectedNode(oldThis.selectedNodesInThisEPC[i].nodeID, oldThis.selectedNodesInThisEPC[i].modelID, oldThis.selectedNodesInThisEPC[i].vizEPC);
                            }
                            oldThis.selectedNodes = [];
                            oldThis.selectedNodesInThisEPC = [];
                        }

                        oldThis.selectedNodes[0] = item;
                        oldThis.selectedNodesInThisEPC[0] = {nodeID: node.id, modelID: oldThis.name, vizEPC: oldThis};
                        oldThis.matchViz.addSelectedNode(node.id, oldThis.name, oldThis);
                        $(item).css({"strokeWidth": "3", "stroke": "black"});
                    }


                }
            }

        });


        epcObject.svg.appendChild(group);
        epcObject.container.appendChild(epcObject.svg);
    };



    function DummyNode(id, label, type, x, y, height, width, level, defaultColor) {
        this.id = id;
        this.level = undefined;
        this.label = label;
        this.type = type;
        this.genericNodeType = "InformationObject";
        if (type === "function" || type === "event") {
            this.genericNodeType = "WorkingNode";
        } else if (node.type === "operator") {
            this.genericNodeType = "ControlNode";
        }
        this.color = defaultColor;
        this.colors = [];
        this.defaultColor = defaultColor;
        this.edge;
        this.x_inEPC = x;
        this.y_inEPC = y;
        this.predecessors = [];
        this.sucsessors = [];
        this.originalPredecessors = [];
        this.originalSucsessors = [];
        this.height = height;
        this.width = width;
        this.assignedObjects = [];
        this.marked = false;
        this.temporaryMark = false;
        this.dummyPredecessors = [];
        this.dummySucsessors = [];
    }

    this.getNode = function (id) {
        var node;
        var l = this.genericNodes.length;
        for (var i = 0; i < l; i++) {
            node = this.genericNodes[i];
            if (node.id == id) {
                return node;
            }
        }
    }

    this.setRMMLayout = function (nodes, edges) {
        for (var i = 0; i < nodes.length; i++) {
            var node = nodes[i];
            var genericNode = this.nodeIdsToNodes[node.id];
            genericNode.x_inEPC_RMM = node.layout.x;
            genericNode.y_inEPC_RMM = node.layout.y;
            genericNode.height_RMM = node.layout.height;
            genericNode.width_RMM = node.layout.width;
        }


        for (var i = 0; i < edges.length; i++) {
            var edge = edges[i];
            var edgeID = edge.source + ";" + edge.target;
            var genericEdge = this.edgeIdsToEdge[edgeID];
            var layout = edge.layout;

            genericEdge.x_Positions_RMM = [];
            genericEdge.y_Positions_RMM = [];

            if (genericEdge.reversed) {
                for (var j = layout.length - 1; j >= 0; j--) {
                    var point = layout[j];
                    genericEdge.x_Positions_RMM.push(point.x);
                    genericEdge.y_Positions_RMM.push(point.y);
                }
            } else {
                for (var j = 0; j < layout.length; j++) {
                    var point = layout[j];
                    genericEdge.x_Positions_RMM.push(point.x);
                    genericEdge.y_Positions_RMM.push(point.y);
                }
            }

        }
    }

    this.useRMMLayout = function (useRMM) {
        this.isRMMLayout = useRMM;
        if (useRMM) {
            for (var i = 0; i < this.genericNodes.length; i++) {
                var node = this.genericNodes[i];
                node.useRMM = true;
                node.x_inEPC = node.x_inEPC_RMM;
                node.y_inEPC = node.y_inEPC_RMM;
                node.height = node.height_RMM;
                node.width = node.width_RMM;
            }
            for (var i = 0; i < this.genericEdges.length; i++) {
                var edge = this.genericEdges[i];
                edge.useRMM = true;
                edge.x_Positions = edge.x_Positions_RMM;
                edge.y_Positions = edge.y_Positions_RMM;
            }
        } else {
            for (var i = 0; i < this.genericNodes.length; i++) {
                var node = this.genericNodes[i];
                node.useRMM = false;
                node.x_inEPC = node.x_inEPC_hierarchical;
                node.y_inEPC = node.y_inEPC_hierarchical;
                node.height = node.height_hierarchical;
                node.width = node.width_hierarchical;
            }
            for (var i = 0; i < this.genericEdges.length; i++) {
                var edge = this.genericEdges[i];
                edge.useRMM = false;
                edge.x_Positions = edge.x_Positions_hierarchical;
                edge.y_Positions = edge.y_Positions_hierarchical;
            }
        }

        epcObject = this;
        this.clearSVG();
        this.drawNodes();
        this.drawEdges();
        this.createControl(this.container.id);
    }

    // create nodes
    for (var i = 0; i < nodes.length; i++) {
        var height = this.defaultNodeHeight;
        var width = this.defaultNodeWidth;

        if (nodes[i].type === "operator") {
            if (this.connectorHeight !== undefined) {
                height = this.connectorHeight;
            }

            width = height;
        }


        if (nodes[i].layout !== undefined) {
            if (nodes[i].layout.height !== undefined) {
                height = nodes[i].layout.height;
            }
            if (nodes[i].layout.width !== undefined) {
                width = nodes[i].layout.width;
            }
        }

        var genericNode = undefined;
        if (nodes[i].type === "function") {
            genericNode = new FunctionNode(nodes[i].id, nodes[i].label, -1, -1, height, width, nodes[i].color);
        } else if (nodes[i].type === "event") {
            genericNode = new EventNode(nodes[i].id, nodes[i].label, -1, -1, height, width, nodes[i].color);
        } else if (nodes[i].type === "operator") {
            genericNode = new OperatorNode(nodes[i].id, nodes[i].label, -1, -1, height, width, nodes[i].color);
        } else if (nodes[i].type === "orgUnit") {
            genericNode = new OrganisationObjectNode(nodes[i].id, nodes[i].label, -1, -1, height, width, nodes[i].color);
        } else if (nodes[i].type === "dataObject") {
            genericNode = new DataObjectNode(nodes[i].id, nodes[i].label, -1, -1, height, width, nodes[i].color);
        } else if (nodes[i].type === "processInterface") {
            genericNode = new ProcessInterfaceNode(nodes[i].id, nodes[i].label, -1, -1, height, width, nodes[i].color);
        } else {
            genericNode = new GenericNode(nodes[i].id, nodes[i].label, -1, -1, height, width, nodes[i].color);
        }

        this.labels.push(nodes[i].label);

        this.nodeIdsToNodes[genericNode.id] = genericNode;

        this.genericNodes.push(genericNode);

    }

    this.germanWords = ['ü', 'ö', 'ä', ' ist ', ' und ', ' nicht ', ' der ', ' das '];

    this.detectLanguage = function () {
        var numGermanWords = 0;
        for (var i = 0; i < this.labels.length; i++) {
            var label = this.labels[i];
            var german = false;
            for (var j = 0; j < this.germanWords.length; j++) {
                if (label.includes(this.germanWords[j])) {
                    german = true;
                    break;
                }
            }
            if (german) {
                numGermanWords++;
            }
        }
        var criticalNumber = this.labels.length / 10;
        if (numGermanWords > 0 && numGermanWords >= criticalNumber) {
            this.language = "de";
        } else {
            this.language = "en-gb";
        }
    }

    this.detectLanguage();

    var l_edges = edges.length;

    for (var j = 0; j < l_edges; j++) {
        var genericEdge = undefined;
        if (edges[j].type === "sequence") {
            genericEdge = new GenericEdge(edges[j].from, edges[j].to, true, edges[j].color);
        } else if (edges[j].type === "relation") {
            genericEdge = new GenericEdge(edges[j].from, edges[j].to, false, edges[j].color);
        } else {
            alert("Edge Error!");
        }

        this.genericEdges.push(genericEdge);

        var edgeID = genericEdge.source + ";" + genericEdge.target;
        this.edgeIdsToEdge[edgeID] = genericEdge;
    }

    // set assignedObjects
    for (var i = 0; i < this.genericEdges.length; i++) {
        var edge = this.genericEdges[i];
        if (!edge.hasArrow) {
            var relatingNodeID = this.genericEdges[i].source;
            var satteliteObjID = this.genericEdges[i].target;

            for (var j = 0; j < this.genericNodes.length; j++) {
                var node = this.genericNodes[j];
                if (node.id === relatingNodeID) {
                    var nodeIsWorkflowNode = node instanceof WorkingOrControlNode;
                    if (!nodeIsWorkflowNode) {
                        var tmpRelatingNodeID = relatingNodeID;
                        relatingNodeID = satteliteObjID;
                        satteliteObjID = tmpRelatingNodeID;
                        var tmpSource = this.genericEdges[i].source;
                        this.genericEdges[i].source = this.genericEdges[i].target;
                        this.genericEdges[i].target = tmpSource;
                    }
                    break;
                }
            }

            for (var j = 0; j < this.genericNodes.length; j++) {
                var node = this.genericNodes[j];
                if (node instanceof WorkingOrControlNode) {
                    if (node.id === relatingNodeID) {
                        var satteliteObject;
                        for (var k = 0; k < this.genericNodes.length; k++) {
                            var satObj = this.genericNodes[k];
                            if (satObj.id === satteliteObjID) {
                                satteliteObject = satObj;
                                break;
                            }
                        }

                        node.assignedObjects.push(satteliteObject);

                    }
                }
            }
        }
    }

    this.setPredecessorsAndSucsessors = function () {

        var nodesLength = this.genericNodes.length;

        for (var i = 0; i < nodesLength; i++) {
            var node = this.genericNodes[i];
            if (node instanceof WorkingOrControlNode) {
                node.sucsessors = [];
                node.predecessors = [];
                node.dummyPredecessors = [];
                node.dummySucsessors = [];
            }
        }

        var edge;

        var l = this.genericEdges.length;

        for (var i = 0; i < l; i++) {
            edge = this.genericEdges[i];
            if (edge.hasArrow) {
                var sourceNode = this.getNode(edge.source);
                var targetNode = this.getNode(edge.target);
                sourceNode.sucsessors.push(targetNode);
                sourceNode.dummySucsessors.push(targetNode);
                targetNode.predecessors.push(sourceNode);
                targetNode.dummyPredecessors.push(sourceNode);
            }
        }
    }

    this.setPredecessorsAndSucsessors();

    this.markAllNodesAs = function (visited) {
        for (var i = 0; i < this.genericNodes.length; i++) {
            var node = this.genericNodes[i];
            if (node instanceof WorkingOrControlNode) {
                node.marked = visited;
                node.temporaryMark = visited;
            }
        }
    }

    this.getAllOriginalStartNodes = function () {
        var node;
        var startNodes = [];

        var l = this.genericNodes.length;

        for (var i = 0; i < l; i++) {
            node = this.genericNodes[i];
            if (node instanceof WorkingOrControlNode) {
                if (node.originalPredecessors.length === 0) {
                    startNodes.push(node);
                }
            }
        }
        return startNodes;
    }


    function hasLoopGroupEdge(loopGroup, edge) {
        for (var i = 0; i < loopGroup.length; i++) {
            var edgeTmp = loopGroup[i];
            var edgeStartId = edge.source;
            var edgeTargetId = edge.target;
            var edgeTmpStart = edgeTmp.source;
            var edgeTmpTarget = edgeTmp.target;
            if ((edgeTmp.source === edge.source) && (edgeTmp.target === edge.target)) {
                return true;
            }
        }
        return false;
    }

    this.getEdgeWithNode = function (start, end) {
        var numEdges = this.genericEdges.length;
        for (var edgeNum = 0; edgeNum < numEdges; edgeNum++) {
            var edge = this.genericEdges[edgeNum];
            if (edge.source === start.id && edge.target === end.id) {
                return edge;
            }
        }
    }

    // basic source: http://stackoverflow.com/questions/19113189/detecting-cycles-in-a-graph-using-dfs-2-different-approaches-and-whats-the-dif
    function DFSDirectedCycle2TakeSpecialEdgesFirst(epc, startNode) {
        this.l = [];
        this.epc = epc;
        this.loopEdges = [];
        epc.markAllNodesAs(false);

        this.visit = function (node, predNode) {
            if (node.temporaryMark === true) {
                var loopEdge = epc.getEdgeWithNode(predNode, node);
                this.loopEdges.push(loopEdge);
                return;
            }
            if (node.marked === false) {
                node.temporaryMark = true;
                var numSucs = node.originalSucsessors.length;
                for (var sucNum = 0; sucNum < numSucs; sucNum++) {
                    var suc = node.originalSucsessors[sucNum];
                    this.visit(suc, node);
                }
                node.marked = true;
                node.temporaryMark = false;
                this.l.push(node);
            }
        }

        this.getLoopEdges = function () {
            return this.loopEdges;
        }

        this.getFirstUnmarkedNode = function () {
            for (var nodeNum = 0; nodeNum < this.epc.genericNodes.length; nodeNum++) {
                var node = this.epc.genericNodes[nodeNum];
                if (node instanceof WorkingOrControlNode) {
                    if (node.marked === false) {
                        return node;
                    }
                }
            }
            return undefined;
        }

        var firstUnmarkedNode = startNode;

        while (firstUnmarkedNode !== undefined) {
            this.visit(firstUnmarkedNode, undefined);
            firstUnmarkedNode = this.getFirstUnmarkedNode();
        }
    }

    this.setOriginalPredecessorsAndSucsessors = function () {

        var nodesLength = this.genericNodes.length;

        for (var i = 0; i < nodesLength; i++) {
            var node = this.genericNodes[i];
            if (node instanceof WorkingOrControlNode) {
                node.originalSucsessors = [];
                node.originalPredecessors = [];
            }
        }

        var edge;

        var l = this.genericEdges.length;

        for (var i = 0; i < l; i++) {
            edge = this.genericEdges[i];
            if (edge.hasArrow) {
                var sourceNode = this.getNode(edge.source);
                var targetNode = this.getNode(edge.target);
                sourceNode.originalSucsessors.push(targetNode);
                targetNode.originalPredecessors.push(sourceNode);
            }
        }
    }

    this.removeLoops = function () {
        this.setOriginalPredecessorsAndSucsessors();

        var startNodes = this.getAllOriginalStartNodes();
        var nodeNum = 0;
        var edgeLoopsGrouped = [];
        for (var nodeNum = 0; nodeNum < startNodes.length; nodeNum++) {
            this.markAllNodesAs(false);
            var startNode = startNodes[nodeNum];
            var cycle = new DFSDirectedCycle2TakeSpecialEdgesFirst(this, startNode);
            var loopEdges = cycle.getLoopEdges();
            if (loopEdges.length > 0) {
                edgeLoopsGrouped.push(loopEdges);
            }
        }

        var loopGroup = edgeLoopsGrouped[0];
        var loopGroupsSatisfying = [];
        for (loopGroupNum = 0; loopGroupNum < edgeLoopsGrouped.length; loopGroupNum++) {
            var loopGroupTMP = edgeLoopsGrouped[loopGroupNum];
            // TODO wenn mehrere in Frage kommen sollte die Gruppe mit der geringsten Anzahl an Loop Edges gewählt werden
                loopGroup = loopGroupTMP;
                loopGroupsSatisfying.push(loopGroupTMP);
            
        }

        if (loopGroupsSatisfying.length > 1) {
            var minSize = -Number.MAX_VALUE;
            for (var slgn = 0; slgn < loopGroupsSatisfying.length; slgn++) {
                var tmpGroup = loopGroupsSatisfying[slgn];
                if (tmpGroup.length < minSize) {
                    minSize = tmpGroup.length;
                    loopGroup = tmpGroup;
                }
            }
        }





        if (loopGroup !== undefined && loopGroup.length > 0) {
            for (var edgeNum = this.genericEdges.length - 1; edgeNum >= 0; edgeNum--) {
                var edge = this.genericEdges[edgeNum];
                if (hasLoopGroupEdge(loopGroup, edge)) {
                    var newEdge = new GenericEdge(edge.target, edge.source, edge.hasArrow, edge.color);
                    newEdge.reversed = true;
                    this.loopEdges.push(newEdge);
                    this.genericEdges.splice(edgeNum, 1);
                    var edgeID = edge.source + ";" + edge.target;
                    this.edgeIdsToEdge[edgeID] = newEdge;
                    // reverse edge
                    this.genericEdges.push(newEdge);
                }
            }
        }


        this.setPredecessorsAndSucsessors();
    }

    this.removeLoops();


    this.getNodeWithPredecessorsOnlyofEralierLevel = function (listOfNodes, currentLevel) {
        for (var i = 0; i < listOfNodes.length; i++) {
            var node = listOfNodes[i];
            var allAssigned = true;
            for (var j = 0; j < node.predecessors.length; j++) {
                if (node.predecessors[j].level === undefined || node.predecessors[j].level >= currentLevel) {
                    allAssigned = false;
                    break;
                }
            }
            if (allAssigned) {
                return node;
            }
        }
    }


    // calculates the levels of the nodes: The level is defined as the size of the longest path from a startNode to the current Node.
    this.calculateLevels = function () {
        // create list containing all nodes including the dummy nodes
        this.allNodesIncludingDummy = [];
        for (var n = 0; n < this.genericNodes.length; n++) {
            var node = this.genericNodes[n];
            if (node instanceof WorkingOrControlNode) {
                this.allNodesIncludingDummy.push(this.genericNodes[n]);
            }
        }

        // copy all nodes to renaining nodes list
        var remainingNodes = this.allNodesIncludingDummy.slice();

        // list for saving the nodes for each level
        this.listOfNodesInLayer = [];
        this.listOfNodesInLayer[0] = [];

        var currentLayer = 0;

        // assign the level to each node
        while (remainingNodes.length > 0) {
            var v = this.getNodeWithPredecessorsOnlyofEralierLevel(remainingNodes, currentLayer);
            if (v !== undefined) {
                v.level = currentLayer;
                this.maxLayerNumber = currentLayer;
                this.listOfNodesInLayer[currentLayer].push(v);
                var index = remainingNodes.indexOf(v);
                if (index > -1) {
                    remainingNodes.splice(index, 1);
                }
            } else {
                currentLayer = currentLayer + 1;
                this.listOfNodesInLayer[currentLayer] = [];
            }

            if (currentLayer > 500) {
                alert("Over 500 levels!!!");
                return;
            }
        }

        // adjust levels: every level of node with level 0 is changed to level = level of sucsessor - 1
        var nodesInLayer = this.listOfNodesInLayer[0];
        var delIndexes = [];
        for (var nodeNum = 0; nodeNum < nodesInLayer.length; nodeNum++) {
            var node = nodesInLayer[nodeNum];
            if (node.sucsessors.length === 1 && node.sucsessors[0].level !== node.level + 1) {
                node.level = node.sucsessors[0].level - 1;
                delIndexes.push(nodeNum);
                this.listOfNodesInLayer[node.level].push(node);
            }
        }
        if (delIndexes.length !== 0) {
            for (var delInd = 0; delInd < delIndexes.length; delInd++) {
                delIndexes.splice(delIndexes[delInd], 1);
            }
        }

        // insert new Layers for assigned Nodes (if more than 1 node is assigned)
        var changed = true;
        var nextLayerNum = 0;
        while (changed) {
            changed = false;
            for (var layNum = nextLayerNum; layNum < this.listOfNodesInLayer.length; layNum++) {
                var nodesInLayer = this.listOfNodesInLayer[layNum];
                var maxNumAssignedNodes = -1;
                for (var nodeNum = 0; nodeNum < nodesInLayer.length; nodeNum++) {
                    var node = nodesInLayer[nodeNum];
                    if (node.assignedObjects.length > maxNumAssignedNodes) {
                        maxNumAssignedNodes = node.assignedObjects.length;
                    }
                }
                if (maxNumAssignedNodes > 1) {
                    // a new layer has to be created
                    changed = true;
                    for (var tmpNum = 1; tmpNum < maxNumAssignedNodes; tmpNum++) {
                        var num = layNum + tmpNum;
                        this.listOfNodesInLayer.splice(num, 0, []);
                    }
                    nextLayerNum = layNum + 1;
                    break;
                }
            }
        }
        for (var ln = 0; ln < this.listOfNodesInLayer.length; ln++) {
            var nodesInLayer = this.listOfNodesInLayer[ln];
            for (var nodeNum = 0; nodeNum < nodesInLayer.length; nodeNum++) {
                var node = nodesInLayer[nodeNum];
                node.level = ln;
            }
        }




        // insert Dummy Nodes
        var dummyID = 0;
        var changed = true;
        while (changed) {
            changed = false;
            var length = this.allNodesIncludingDummy.length;
            for (var i = 0; i < length; i++) {
                var node = this.allNodesIncludingDummy[i];
                for (var j = 0; j < node.dummySucsessors.length; j++) {
                    var sucsessor = node.dummySucsessors[j];
                    if (sucsessor.level !== (node.level + 1)) {
                        changed = true;
                        // insert Dummy Node
                        var id = "DummyID_" + dummyID;
                        var dummyNode = new DummyNode(id, "Dummy Node" + dummyID, "function", -1, -1, this.defaultNodeHeight, this.defaultNodeWidth, -1, "nodes[i].color");
                        dummyNode.level = (node.level + 1);
                        dummyNode.dummyPredecessors.push(node);
                        dummyNode.dummySucsessors.push(sucsessor);
                        var edge = this.getEdgeWithNode(node, sucsessor);
                        if (node.edge === undefined && edge !== undefined && edge !== null) {
                            var dummies = edge.dummyNodes;
                            dummies.push(dummyNode);
                            dummyNode.edge = edge;
                        }
                        if (node.edge !== undefined) {
                            var edgeTmp = node.edge;
                            var dummies = edgeTmp.dummyNodes;
                            dummies.push(dummyNode);
                            dummyNode.edge = edgeTmp;
                        }
                        dummyID++;
                        var index = node.dummySucsessors.indexOf(sucsessor);
                        if (index > -1) {
                            node.dummySucsessors.splice(index, 1);
                        }
                        node.dummySucsessors.push(dummyNode);
                        var index = sucsessor.dummyPredecessors.indexOf(node);
                        if (index > -1) {
                            sucsessor.dummyPredecessors.splice(index, 1);
                        }
                        sucsessor.dummyPredecessors.push(dummyNode);
                        this.allNodesIncludingDummy.push(dummyNode);
                    }
                }
            }
        }

        this.listOfNodesInLayerWithDummyNodes = [];
        for (var n = 0; n <= this.listOfNodesInLayer.length; n++) {
            this.listOfNodesInLayerWithDummyNodes[n] = [];
        }


        // add Dummy Nodes to levels list
        for (var k = 0; k < this.allNodesIncludingDummy.length; k++) {
            var node = this.allNodesIncludingDummy[k];
            this.listOfNodesInLayerWithDummyNodes[node.level].push(node);
        }
    }


    this.calculateLevels();

    this.getFirstIndexOf = function (nodeBlock, nodes) {
        for (var nodeNumInCurrent = 0; nodeNumInCurrent < nodes.length; nodeNumInCurrent++) {
            var node = nodes[nodeNumInCurrent];
            var index = nodeBlock.indexOf(node);
            if (index > -1) {
                return nodeNumInCurrent;
            }
        }
    }

    this.orderNodes = function () {
        // von unten nach oben
        for (var i = this.maxLayerNumber - 1; i >= 0; i--) {
            var nodesLowerLevel = this.listOfNodesInLayerWithDummyNodes[i + 1];
            var nodesCurrentLevel = this.listOfNodesInLayerWithDummyNodes[i];

            // nodeBlocks enthaelt Listen von Elementen, die zusammen gehören
            var nodeBlocks = [];

            var nodesSet = new Set();
            var overlappingNodesSet = new Set();

            // calculate nodeBlocks
            for (var k = 0; k < nodesLowerLevel.length; k++) {
                var node = nodesLowerLevel[k];
                nodeBlocks[k] = [];
                for (var j = 0; j < node.dummyPredecessors.length; j++) {
                    var pred = node.dummyPredecessors[j];
                    nodeBlocks[k].push(pred);

                    if (nodesSet.has(pred)) {
                        overlappingNodesSet.add(pred);
                    }
                    nodesSet.add(pred);
                }
            }


            // if the nodeBlocks have overlapping nodes order the nodes in each nodeBlock, so that a Overlapping Node is on the left or on the right side
            var myArr = Array.from(overlappingNodesSet);
            if (myArr.length > 0) {
                for (var kl = 0; kl < nodeBlocks.length; kl++) {
                    var nextLeft = [];
                    for (var ol = 0; ol < myArr.length; ol++) {
                        nextLeft[ol] = false;
                    }
                    for (var ol = 0; ol < myArr.length; ol++) {
                        var index = nodeBlocks[kl].indexOf(myArr[ol]);
                        if (index > -1) {
                            if (index === nodeBlocks[kl].length - 1) {
                                // node is on rigth side
                                if (nextLeft[ol]) {
                                    nodeBlocks[kl].splice(index, 1);
                                    nodeBlocks[kl].splice(0, 0, myArr[ol]);
                                } else {
                                    // nothing to do here
                                }
                                nextLeft[ol] = !nextLeft[ol];
                            } else if (index === 0) {
                                // node is on left side
                                if (nextLeft[ol]) {
                                    // nothing to do here
                                } else {
                                    nodeBlocks[kl].splice(index, 1);
                                    nodeBlocks[kl].push(myArr[ol]);
                                }
                                nextLeft[ol] = !nextLeft[ol];
                            } else {
                                // node is in middle
                                if (nextLeft[ol]) {
                                    nodeBlocks[kl].splice(index, 1);
                                    nodeBlocks[kl].splice(0, 0, myArr[ol]);
                                } else {
                                    nodeBlocks[kl].splice(index, 1);
                                    nodeBlocks[kl].push(myArr[ol]);
                                }
                                nextLeft[ol] = !nextLeft[ol];
                            }
                        }
                    }
                }
            }



            // re-arrange the nodes, so that nodes in nodeBlock are neighbours
            for (var kl = 0; kl < nodeBlocks.length; kl++) {
                if (nodeBlocks[kl].length > 1) {
                    var index = this.getFirstIndexOf(nodeBlocks[kl], nodesCurrentLevel);
                    var nextInsertPos = index + 1;
                    var nextIndex = index + 1;
                    var missingNodes = [];

                    // add all nodes from nodeBlock to list of missing nodes
                    for (var tmp = 0; tmp < nodeBlocks[kl].length; tmp++) {
                        missingNodes.push(nodeBlocks[kl][tmp]);
                    }

                    // delete first found node from missing list
                    var delIndex = missingNodes.indexOf(nodesCurrentLevel[index]);
                    if (delIndex > -1) {
                        missingNodes.splice(delIndex, 1);
                    }

                    while (missingNodes.length !== 0) {
                        var node = nodesCurrentLevel[nextIndex];

                        if (nodeBlocks[kl].indexOf(node) > -1) {
                            // node is in block
                            if (nextIndex === nextInsertPos) {
                                var delIndex = missingNodes.indexOf(node);
                                if (delIndex > -1) {
                                    missingNodes.splice(delIndex, 1);
                                }
                                nextInsertPos++;
                            } else {
                                var delIndex = nodesCurrentLevel.indexOf(node);
                                if (delIndex > -1) {
                                    nodesCurrentLevel.splice(delIndex, 1);
                                }
                                nodesCurrentLevel.splice(nextInsertPos, 0, node);
                                var delIndex = missingNodes.indexOf(node);
                                if (delIndex > -1) {
                                    missingNodes.splice(delIndex, 1);
                                }
                                nextInsertPos++;
                            }

                        } else {
                            // node is not in block

                        }
                        nextIndex++;
                    }
                }
            }


        }

        // print result
        if (this.debug) {
            for (var i = 0; i < this.listOfNodesInLayerWithDummyNodes.length; i++) {
                var string = "";
                for (var j = 0; j < this.listOfNodesInLayerWithDummyNodes[i].length; j++) {
                    string = string + this.listOfNodesInLayerWithDummyNodes[i][j].label + ", ";
                }
                console.log(i + ": " + string);
            }
            console.log("");
            console.log("");
        }


        // von oben nach unten
        for (var i = 1; i <= this.maxLayerNumber; i++) {
            var nodesHigherLevel = this.listOfNodesInLayerWithDummyNodes[i - 1];
            var nodesCurrentLevel = this.listOfNodesInLayerWithDummyNodes[i];
            var nodeBlocks = [];
            var nodesSet = new Set();
            var overlappingNodesSet = new Set();
            for (var k = 0; k < nodesHigherLevel.length; k++) {
                var node = nodesHigherLevel[k];
                nodeBlocks[k] = [];
                for (var j = 0; j < node.dummySucsessors.length; j++) {
                    var pred = node.dummySucsessors[j];
                    nodeBlocks[k].push(pred);

                    if (nodesSet.has(pred)) {
                        overlappingNodesSet.add(pred);
                    }
                    nodesSet.add(pred);
                }
            }
            var myArr = Array.from(overlappingNodesSet);
            if (myArr.length > 0) {
                for (var kl = 0; kl < nodeBlocks.length; kl++) {
                    var nextLeft = [];
                    for (var ol = 0; ol < myArr.length; ol++) {
                        nextLeft[ol] = false;
                    }
                    for (var ol = 0; ol < myArr.length; ol++) {
                        var index = nodeBlocks[kl].indexOf(myArr[ol]);
                        if (index > -1) {
                            if (index === nodeBlocks[kl].length - 1) {
                                // node is on rigth side
                                if (nextLeft[ol]) {
                                    nodeBlocks[kl].splice(index, 1);
                                    nodeBlocks[kl].splice(0, 0, myArr[ol]);
                                } else {
                                    // nothing to do here
                                }
                                nextLeft[ol] = !nextLeft[ol];
                            } else if (index === 0) {
                                // node is on left side
                                if (nextLeft[ol]) {
                                    // nothing to do here
                                } else {
                                    nodeBlocks[kl].splice(index, 1);
                                    nodeBlocks[kl].push(myArr[ol]);
                                }
                                nextLeft[ol] = !nextLeft[ol];
                            } else {
                                // node is in middle
                                if (nextLeft[ol]) {
                                    nodeBlocks[kl].splice(index, 1);
                                    nodeBlocks[kl].splice(0, 0, myArr[ol]);
                                } else {
                                    nodeBlocks[kl].splice(index, 1);
                                    nodeBlocks[kl].push(myArr[ol]);
                                }
                                nextLeft[ol] = !nextLeft[ol];
                            }
                        }
                    }
                }
            }

            for (var kl = 0; kl < nodeBlocks.length; kl++) {
                if (nodeBlocks[kl].length > 1) {
                    var index = this.getFirstIndexOf(nodeBlocks[kl], nodesCurrentLevel);
                    var nextInsertPos = index + 1;
                    var nextIndex = index + 1;
                    var missingNodes = [];
                    for (var tmp = 0; tmp < nodeBlocks[kl].length; tmp++) {
                        missingNodes.push(nodeBlocks[kl][tmp]);
                    }
                    var delIndex = missingNodes.indexOf(nodesCurrentLevel[index]);
                    if (delIndex > -1) {
                        missingNodes.splice(delIndex, 1);
                    }
                    while (missingNodes.length !== 0) {
                        var node = nodesCurrentLevel[nextIndex];

                        if (nodeBlocks[kl].indexOf(node) > -1) {
                            // node is in block
                            if (nextIndex === nextInsertPos) {
                                var delIndex = missingNodes.indexOf(node);
                                if (delIndex > -1) {
                                    missingNodes.splice(delIndex, 1);
                                }
                                nextInsertPos++;
                            } else {
                                var delIndex = nodesCurrentLevel.indexOf(node);
                                if (delIndex > -1) {
                                    nodesCurrentLevel.splice(delIndex, 1);
                                }
                                nodesCurrentLevel.splice(nextInsertPos, 0, node);
                                var delIndex = missingNodes.indexOf(node);
                                if (delIndex > -1) {
                                    missingNodes.splice(delIndex, 1);
                                }
                                nextInsertPos++;
                            }

                        } else {
                            // node is not in block

                        }
                        nextIndex++;
                    }
                }
            }


            // try to reduce number of crossings
            // calculate a list containing the order of the predecessor nodes: Für jeden Knoten in der aktuellen Ebene, der genau einen Predecessor hat, wird die Position des Predecessors in der Ebene eins höher gespeichert

            // enthaelt die Position der Vorgaengerknoten in der uebergeordneten hoehe
            var predecessorPositionsList = [];
            // enthaelt die Position in der aktuellen Ebene
            var predecessorIndexes = [];

            for (var nn = 0; nn < nodesCurrentLevel.length; nn++) {
                var node = nodesCurrentLevel[nn];
                if (node.dummyPredecessors.length === 1) {
                    var index = nodesHigherLevel.indexOf(node.dummyPredecessors[0]);
                    if (index > -1) {
                        predecessorPositionsList.push(index);
                        predecessorIndexes.push(nn);
                    }
                }
            }

            // sort positions and update relating indexes
            var sortedPositions = [];
            var sortedIndexes = [];
            var length = predecessorPositionsList.length;
            while (length > 0) {
                var smallestNum = Number.MAX_VALUE;
                var corrIndex = 0;
                for (var c = 0; c < predecessorPositionsList.length; c++) {
                    var num = predecessorPositionsList[c];
                    var indexValue = sortedIndexes.indexOf(predecessorIndexes[c]);
                    if (smallestNum > num && !(indexValue > -1)) {
                        smallestNum = num;
                        corrIndex = predecessorIndexes[c];
                    }
                }
                sortedPositions.push(smallestNum);
                sortedIndexes.push(corrIndex);
                var delIndex = predecessorPositionsList.indexOf(smallestNum);
                if (delIndex > -1) {
                    length--;
                }
            }

            // this copy is needed to modify the order in the current layer
            var copy = [];
            for (var nn = 0; nn < nodesCurrentLevel.length; nn++) {
                copy.push(nodesCurrentLevel[nn]);
            }

            // modify order in current layer
            for (var pNum = 0; pNum < sortedPositions.length; pNum++) {
                var oldIndex = predecessorIndexes[pNum];
                var nexIndex = sortedIndexes[pNum];
                nodesCurrentLevel[oldIndex] = copy[nexIndex];
            }


        }
    }


    this.orderNodes();

    if (this.debug) {
        for (var i = 0; i < this.listOfNodesInLayerWithDummyNodes.length; i++) {
            var string = "";
            for (var j = 0; j < this.listOfNodesInLayerWithDummyNodes[i].length; j++) {
                string = string + this.listOfNodesInLayerWithDummyNodes[i][j].label + ", ";
            }
            console.log(i + ": " + string);
        }
    }

    this.calculateWidthOfLayer = function (layer, useGrid) {
        if (useGrid) {
            var size = 0;
            var sizeSattelite = 0;
            for (var j = 0; j < layer.length; j++) {
                size++;
                if (layer[j].assignedObjects.length > 0) {
                    sizeSattelite++;
                }
            }
            var w = (size) * this.x_Distance + sizeSattelite * this.x_Distance;
            return w;
        } else {
            var w = 0;
            for (var j = 0; j < layer.length; j++) {
                w = w + layer[j].width;
                if (j !== 0) {
                    w = w + this.nodeDistance;
                }
                if (layer[j].assignedObjects.length > 0) {
                    var max = -1;
                    for (var aon = 0; aon < layer[j].assignedObjects.length; aon++) {
                        if (layer[j].assignedObjects[aon].width > max) {
                            max = layer[j].assignedObjects[aon].width;
                        }
                    }
                    w = w + max + this.satelliteDistance;
                }
            }
            return w;
        }
    }

    this.layoutNodes = function (useGrid) {

        if (useGrid) {
            var highestLevelWidth = -1;
            var highestLevelNum = -1;
            for (var i = 0; i < this.listOfNodesInLayerWithDummyNodes.length; i++) {
                var w = this.calculateWidthOfLayer(this.listOfNodesInLayerWithDummyNodes[i], useGrid);
                if (w > highestLevelWidth) {
                    highestLevelWidth = w;
                    highestLevelNum = i;
                }
            }


            // layout all levels
            for (var i = 0; i < this.listOfNodesInLayerWithDummyNodes.length; i++) {
                var width = this.calculateWidthOfLayer(this.listOfNodesInLayerWithDummyNodes[i], useGrid);
                var widthDiff = highestLevelWidth - width;
                var startPos = widthDiff / 2;
                var gridXPos = 0;
                for (var j = 0; j < this.listOfNodesInLayerWithDummyNodes[i].length; j++) {
                    var node = this.listOfNodesInLayerWithDummyNodes[i][j];
                    node.y_inEPC = i * this.y_Distance;
                    node.x_inEPC = gridXPos * this.x_Distance + startPos;
                    node.y_inEPC_hierarchical = node.y_inEPC;
                    node.x_inEPC_hierarchical = node.x_inEPC;
                    if (node.assignedObjects.length > 0) {
                        gridXPos++;
                        for (var aon = 0; aon < node.assignedObjects.length; aon++) {
                            node.assignedObjects[aon].y_inEPC = (i + aon) * this.y_Distance;
                            node.assignedObjects[aon].x_inEPC = gridXPos * this.x_Distance + startPos;
                        }

                    }
                    gridXPos++;
                }
            }

        } else {
            var highestLevelWidth = -1;
            for (var i = 0; i < this.listOfNodesInLayerWithDummyNodes.length; i++) {
                var w = this.calculateWidthOfLayer(this.listOfNodesInLayerWithDummyNodes[i], useGrid);

                if (w > highestLevelWidth) {
                    highestLevelWidth = w;
                }
            }

            for (var i = 0; i < this.listOfNodesInLayerWithDummyNodes.length; i++) {
                var width = this.calculateWidthOfLayer(this.listOfNodesInLayerWithDummyNodes[i], useGrid);
                var widthDiff = highestLevelWidth - width;
                var startPos = widthDiff / 2;
                var x_Value = startPos;

                for (var j = 0; j < this.listOfNodesInLayerWithDummyNodes[i].length; j++) {
                    var node = this.listOfNodesInLayerWithDummyNodes[i][j];
                    var prevNode = undefined;
                    if (j !== 0) {
                        prevNode = this.listOfNodesInLayerWithDummyNodes[i][j - 1];
                    }
                    node.y_inEPC = i * this.y_Distance;
                    if (prevNode !== undefined) {
                        x_Value = x_Value + node.width / 2 + this.nodeDistance;
                    }
                    node.x_inEPC = x_Value;
                    x_Value = x_Value + node.width / 2;

                    node.y_inEPC_hierarchical = node.y_inEPC;
                    node.x_inEPC_hierarchical = node.x_inEPC;

                    // get satellite node with max width
                    if (node.assignedObjects.length > 0) {
                        var max = -1;
                        for (var aon = 0; aon < node.assignedObjects.length; aon++) {
                            if (node.assignedObjects[aon].width > max) {
                                max = node.assignedObjects[aon].width;
                            }
                        }
                    }

                    if (node.assignedObjects.length > 0) {
                        x_Value = x_Value + node.width / 2 + max / 2 + this.satelliteDistance;
                        for (var aon = 0; aon < node.assignedObjects.length; aon++) {
                            node.assignedObjects[aon].x_inEPC = x_Value;
                            node.assignedObjects[aon].y_inEPC = (i + aon) * this.y_Distance;
                        }
                        x_Value = x_Value + max / 2;

                    }
                }
            }
        }
    }

    this.orderOfSucsessorsForNode = [];
    this.orderOfPredecessorsForNode = [];

    this.getOrderOfPredecessors = function (node) {
        if (this.orderOfPredecessorsForNode[node.id] !== undefined) {
            return this.orderOfPredecessorsForNode[node.id];
        }

        if (node.level === 0) {
            return undefined;
        }

        var orderOfPredecessors = [];

        var remainingPredList = [];
        for (var j = 0; j < node.dummyPredecessors.length; j++) {
            remainingPredList.push(node.dummyPredecessors[j]);
        }

        for (var nodeNumInLevel = 0; nodeNumInLevel < this.listOfNodesInLayerWithDummyNodes[node.level - 1].length; nodeNumInLevel++) {
            var nodeInLevel = this.listOfNodesInLayerWithDummyNodes[node.level - 1][nodeNumInLevel];
            for (var i = 0; i < remainingPredList.length; i++) {
                var pred = remainingPredList[i];
                if (nodeInLevel.id === pred.id) {
                    orderOfPredecessors.push(pred);
                    var index = remainingPredList.indexOf(pred);
                    if (index > -1) {
                        remainingPredList.splice(index, 1);
                    }
                    if (remainingPredList.length === 0) {
                        this.orderOfPredecessorsForNode[node.id] = orderOfPredecessors;
                        return orderOfPredecessors;
                    }
                    break;
                }
            }
        }

    }

    this.getOrderOfSucsessors = function (node) {
        if (this.orderOfSucsessorsForNode[node.id] !== undefined) {
            return this.orderOfSucsessorsForNode[node.id];
        }

        if (node.level === this.maxLayerNumber) {
            return undefined;
        }

        var orderOfSucsesssors = [];

        var remainingSucList = [];
        for (var j = 0; j < node.dummySucsessors.length; j++) {
            remainingSucList.push(node.dummySucsessors[j]);
        }

        for (var nodeNumInLevel = 0; nodeNumInLevel < this.listOfNodesInLayerWithDummyNodes[node.level + 1].length; nodeNumInLevel++) {
            var nodeInLevel = this.listOfNodesInLayerWithDummyNodes[node.level + 1][nodeNumInLevel];
            for (var i = 0; i < remainingSucList.length; i++) {
                var suc = remainingSucList[i];
                if (nodeInLevel.id === suc.id) {
                    orderOfSucsesssors.push(suc);
                    var index = remainingSucList.indexOf(suc);
                    if (index > -1) {
                        remainingSucList.splice(index, 1);
                    }
                    if (remainingSucList.length === 0) {
                        this.orderOfSucsessorsForNode[node.id] = orderOfSucsesssors;
                        return orderOfSucsesssors;
                    }
                    break;
                }
            }
        }

    }

    this.layoutEdges = function (edges) {
        var l = edges.length;
        for (var i = 0; i < l; i++) {
            var edge = edges[i];

            if (edge.hasArrow) {

                var startNode = this.getNode(edge.source);
                var endNode = this.getNode(edge.target);

                var startX = startNode.x_inEPC;
                var startY = startNode.y_inEPC;
                var endX = endNode.x_inEPC;
                var endY = endNode.y_inEPC;

                var nextNode = endNode;
                var prevNode = startNode;
                if (edge.dummyNodes.length > 0) {
                    prevNode = edge.dummyNodes[edge.dummyNodes.length - 1];
                    nextNode = edge.dummyNodes[0];
                }

                var XposEdgeEnd = endX;
                if (endNode.dummyPredecessors.length > 1) {
                    var order = this.getOrderOfPredecessors(endNode);
                    var width = (endNode.dummyPredecessors.length - 1) * this.edgeWidth;
                    var startPos = endNode.x_inEPC - width / 2;
                    var index = order.indexOf(prevNode);
                    if (index > -1) {
                        XposEdgeEnd = startPos + index * this.edgeWidth;
                    } else {
                        alert("ERROR");
                    }
                }

                var XposEdgeStart = startX;
                if (startNode.dummySucsessors.length > 1) {
                    var order = this.getOrderOfSucsessors(startNode);
                    var width = (startNode.dummySucsessors.length - 1) * this.edgeWidth;
                    var startPos = startNode.x_inEPC - width / 2;
                    var index = order.indexOf(nextNode);
                    if (index > -1) {
                        XposEdgeStart = startPos + index * this.edgeWidth;
                    } else {
                        alert("ERROR");
                    }
                }


                if (startY > endY) {
                    // backward edge
                    startY = startY - startNode.height / 2;
                    endY = endY + endNode.height / 2;
                    var points = [{x: XposEdgeStart, y: startY}];
                    points.push({x: XposEdgeStart, y: startY - this.edgeStraightLengthDistance});

                    if (edge.dummyNodes.length > 0) {
                        for (var numDummyNode = 0; numDummyNode < edge.dummyNodes.length; numDummyNode++) {
                            var dummyNode = edge.dummyNodes[numDummyNode];
                            var y = dummyNode.y_inEPC;
                            var y2 = dummyNode.y_inEPC;
                            y = y + dummyNode.height / 2 + this.edgeStraightLengthDistance;
                            y2 = y2 - dummyNode.height / 2 - this.edgeStraightLengthDistance;
                            points.push({x: dummyNode.x_inEPC, y: y});
                            if (y2 !== undefined) {
                                points.push({x: dummyNode.x_inEPC, y: y2});
                            }
                        }
                    }

                    points.push({x: XposEdgeEnd, y: endY + this.edgeStraightLengthDistance});
                    points.push({x: XposEdgeEnd, y: endY});

                    // transform to old conversation
                    for (var pointNum = 0; pointNum < points.length; pointNum++) {
                        var point = points[pointNum];
                        edge.x_Positions.push(point.x);
                        edge.y_Positions.push(point.y);
                        edge.x_Positions_hierarchical.push(point.x);
                        edge.y_Positions_hierarchical.push(point.y);
                    }
                } else {
                    // forward edge
                    startY = startY + startNode.height / 2;
                    endY = endY - endNode.height / 2;
                    var points = [{x: XposEdgeStart, y: startY}];
                    points.push({x: XposEdgeStart, y: startY + this.edgeStraightLengthDistance});

                    if (edge.dummyNodes.length > 0) {
                        for (var numDummyNode = 0; numDummyNode < edge.dummyNodes.length; numDummyNode++) {
                            var dummyNode = edge.dummyNodes[numDummyNode];
                            var y = dummyNode.y_inEPC;
                            var y2 = dummyNode.y_inEPC;
                            y = y - dummyNode.height / 2 - this.edgeStraightLengthDistance;
                            y2 = y2 + dummyNode.height / 2 + this.edgeStraightLengthDistance;
                            points.push({x: dummyNode.x_inEPC, y: y});
                            if (y2 !== undefined) {
                                points.push({x: dummyNode.x_inEPC, y: y2});
                            }

                        }
                    }

                    points.push({x: XposEdgeEnd, y: endY - this.edgeStraightLengthDistance});
                    points.push({x: XposEdgeEnd, y: endY});

                    // transform to old conversation
                    for (var pointNum = 0; pointNum < points.length; pointNum++) {
                        var point = points[pointNum];
                        edge.x_Positions.push(point.x);
                        edge.y_Positions.push(point.y);
                        edge.x_Positions_hierarchical.push(point.x);
                        edge.y_Positions_hierarchical.push(point.y);
                    }
                }


                var startLabel = startNode.label;
                var endLabel = endNode.label;
                var edgeText = startLabel + " -> " + endLabel;

                var positionsText = "[";
                for (var edgePoint = 0; edgePoint < edge.x_Positions.length; edgePoint++) {
                    positionsText = positionsText + "(" + edge.x_Positions[edgePoint] + ", " + edge.y_Positions[edgePoint] + ")";
                }
                positionsText = positionsText + "]";

                this.textualLayout = this.textualLayout + edgeText + ": " + positionsText;
            } else {


                // relation

                var relation = edge;

                var startNode = this.getNode(edge.source);
                var endNode = this.getNode(edge.target);

                var startX = startNode.x_inEPC;
                var startY = startNode.y_inEPC;
                var endX = endNode.x_inEPC;
                var endY = endNode.y_inEPC;

                if (startY === endY) {
                    relation.x_Positions.push(startX + startNode.width / 2);
                    relation.x_Positions.push(endX - endNode.width / 2);
                    relation.y_Positions.push(startY);
                    relation.y_Positions.push(endY);
                } else {
                    // start is functionNode => in this case the assignmentNode is higher than the function node
                    if (startY < endY) {
                        relation.x_Positions.push(startX + startNode.width / 2);
                        relation.y_Positions.push(startY);

                        relation.x_Positions.push(startX + startNode.width / 2 + 15);
                        relation.y_Positions.push(startY);

                        relation.x_Positions.push(startX + startNode.width / 2 + 15);
                        relation.y_Positions.push(endY);

                        relation.x_Positions.push(endX - endNode.width / 2);
                        relation.y_Positions.push(endY);
                    } else {
                        relation.x_Positions.push(startX + startNode.width / 2);
                        relation.y_Positions.push(startY);

                        relation.x_Positions.push(startX + startNode.width / 2 + 15);
                        relation.y_Positions.push(startY);

                        relation.x_Positions.push(startX + startNode.width / 2 + 15);
                        relation.y_Positions.push(endY);

                        relation.x_Positions.push(endX - endNode.width / 2);
                        relation.y_Positions.push(endY);
                    }

                }



                var startLabel = startNode.label;
                var endLabel = endNode.label;
                var edgeText = startLabel + " -> " + endLabel;

                var positionsText = "[";
                for (var edgePoint = 0; edgePoint < relation.x_Positions.length; edgePoint++) {
                    positionsText = positionsText + "(" + relation.x_Positions[edgePoint] + ", " + relation.y_Positions[edgePoint] + ")";
                }
                positionsText = positionsText + "]";

                this.textualLayout = this.textualLayout + edgeText + ": " + positionsText;


            }
        }
    }




    this.layoutRelations = function (relations) {
        var l = relations.length;
        for (var i = 0; i < l; i++) {
            var relation = relations[i];

            var startNode = relation.source;
            var endNode = relation.target;

            var startX = startNode.x_inEPC;
            var startY = startNode.y_inEPC;
            var endX = endNode.x_inEPC;
            var endY = endNode.y_inEPC;

            if (startY === endY) {
                relation.x_Positions.push(startX + startNode.width / 2);
                relation.x_Positions.push(endX - endNode.width / 2);
                relation.y_Positions.push(startY);
                relation.y_Positions.push(endY);
            } else {
                // start is functionNode => in this case the assignmentNode is higher than the function node
                if (startY < endY) {
                    relation.x_Positions.push(startX + startNode.width / 2);
                    relation.y_Positions.push(startY);

                    relation.x_Positions.push(startX + startNode.width / 2 + 15);
                    relation.y_Positions.push(startY);

                    relation.x_Positions.push(startX + startNode.width / 2 + 15);
                    relation.y_Positions.push(endY);

                    relation.x_Positions.push(endX - endNode.width / 2);
                    relation.y_Positions.push(endY);
                } else {
                    relation.x_Positions.push(startX + startNode.width / 2);
                    relation.y_Positions.push(startY);

                    relation.x_Positions.push(startX + startNode.width / 2 + 15);
                    relation.y_Positions.push(startY);

                    relation.x_Positions.push(startX + startNode.width / 2 + 15);
                    relation.y_Positions.push(endY);

                    relation.x_Positions.push(endX - endNode.width / 2);
                    relation.y_Positions.push(endY);
                }

            }



            var startLabel = startNode.label;
            var endLabel = endNode.label;
            var edgeText = startLabel + " -> " + endLabel;

            var positionsText = "[";
            for (var edgePoint = 0; edgePoint < relation.x_Positions.length; edgePoint++) {
                positionsText = positionsText + "(" + relation.x_Positions[edgePoint] + ", " + relation.y_Positions[edgePoint] + ")";
            }
            positionsText = positionsText + "]"

            this.textualLayout = this.textualLayout + edgeText + ": " + positionsText;

        }
    }


    this.layout = function () {
        this.layoutNodes(this.useGrid);
        this.layoutEdges(this.genericEdges);
    }

    this.layout();




    this.setMatchViz = function (matchViz) {
        this.matchViz = matchViz;
    }

    this.drawEdges = function () {
        for (var i = 0; i < this.genericEdges.length; i++) {
            var edge = this.genericEdges[i];
            if (this.isRMMLayout === edge.useRMM) {
                edge.draw();
            }
        }
    }

    this.clearSVG = function () {
        this.panZoomInstance.destroy();
        delete this.panZoomInstance;

        $("#" + this.svg.id).empty();
    }

    this.createControl = function (containerID) {
        this.panZoomInstance = svgPanZoom('#svg_' + containerID, {
            zoomEnabled: true,
            zoomScaleSensitivity: 0.4,
            controlIconsEnabled: true,
            fit: true,
            center: true,
            minZoom: 0.1,
        });
    }

    this.drawNodes = function () {

        this.svgID = 0;
        for (var i = 0; i < this.genericNodes.length; i++) {
            var node = this.genericNodes[i];
            if (this.isRMMLayout === node.useRMM) {
                node.draw();
            }
        }



        if (this.matchViz !== undefined) {
            theThisObject = this;
            var nodes = $('#' + this.container.id).find('.node');
            thisObject = theThisObject;
            nodes.each(function () {
                var id = '#' + $(this).attr('id');
                $.contextMenu({
                    selector: id,
                    build: function ($trigger) {
                        if (thisObject.matchViz === undefined || thisObject.matchViz === null) {
                            return false;
                        }
                        var options = undefined;

                        var trigger = $trigger[0];
                        var id = trigger.id;
                        var epcVizName = trigger.ownerSVGElement.parentNode.id;
                        var epcViz = thisObject.matchViz.getViz(epcVizName);
                        var epc = epcViz;
                        var nodeObject = epc.mapSVGIDToNode[id];
                        var node = {modelID: epc.name, nodeID: nodeObject.id};

                        if ((thisObject.matchViz.getNumberOfMatchings() > 0) && thisObject.matchViz.getMatchingsContainingNode(node) !== undefined && thisObject.matchViz.getMatchingsContainingNode(node).length > 1) {
                            var options = {
                                callback: function (key, options) {
                                    var m = "clicked: " + key;

                                    if (key.startsWith("deleteWholeMatch_")) {
                                        var matches = thisObject.matchViz.getMatchingsContainingNode(node);
                                        var matchNum = parseInt(key.replace("deleteWholeMatch_", ""));
                                        thisObject.matchViz.deleteMatchContainingDefinedNode(node, "contextMenuNode", matches[matchNum].matchID, matches[matchNum].mappingID);
                                        document.getElementById("matchMetrics").innerHTML = thisObject.matchViz.getHTMLCodeForMatchMetrics();
                                    } else if (key.startsWith("deleteNodeFromMatch_")) {
                                        var matches = thisObject.matchViz.getMatchingsContainingNode(node);
                                        var matchNum = parseInt(key.replace("deleteNodeFromMatch_", ""));
                                        thisObject.matchViz.deleteNodeFromDefinedMatch(node, matches[matchNum].matchID, matches[matchNum].mappingID);
                                        document.getElementById("matchMetrics").innerHTML = thisObject.matchViz.getHTMLCodeForMatchMetrics();
                                    } else if (key.startsWith("addNodeToMatch_")) {
                                        var matches = thisObject.matchViz.getListOfMatches();
                                        var matchNum = parseInt(key.replace("addNodeToMatch_", ""));
                                        thisObject.matchViz.addNodeToMatch(node, matches[matchNum].matchID, matches[matchNum].mappingID);
                                        document.getElementById("matchMetrics").innerHTML = thisObject.matchViz.getHTMLCodeForMatchMetrics();
                                    } else {
                                        window.console && console.log(m) || alert(m);
                                    }
                                },
                                items: {
                                    "addNodeToMatch": {name: "Add Node to Match", icon: function ($element, key, item) {
                                            return 'context-menu-icon-1 context-menu-icon-add';
                                        }, items: {}},
                                    "deleteNodeFromMatch": {name: "Delete Node from Match", icon: "delete", items: {}},
                                    "deleteWholeMatch": {name: "Delete whole Match", icon: "delete", items: {}}
                                }
                            };
                            var matches = thisObject.matchViz.getMatchingsContainingNode(node);






                            for (var i = 0; i < matches.length; i++) {
                                var string = '<style>.newClassDeleteMatch' + i + '::before{  position: absolute;  top: 50%;  left: 0;  width: 2em;   font-family: "context-menu-icons";  font-size: 1em;  font-style: normal;  font-weight: normal;  line-height: 1;  color: ' + matches[i].color + ';  text-align: center;  -webkit-transform: translateY(-50%);      -ms-transform: translateY(-50%);       -o-transform: translateY(-50%);          transform: translateY(-50%);  -webkit-font-smoothing: antialiased;  -moz-osx-font-smoothing: grayscale;}</style>';
                                $(string).appendTo('head');


                                var matchName = "empty Match";
                                if (matches !== undefined && matches[i].name !== undefined) {
                                    matchName = matches[i].name;
                                    if (matchName.length > 17) {
                                        matchName = matchName.substring(0, 15);
                                        matchName = matchName + '...';
                                    }
                                    matchName = '"' + matchName + '", ...';
                                }

                                options.items.deleteNodeFromMatch.items["deleteNodeFromMatch_" + i] = {name: matchName,
                                    icon: function ($element, key, item, i) {
                                        var d = undefined;
                                        var num = parseInt(item.replace("deleteNodeFromMatch_", ""));
                                        var s = 'newClassDeleteMatch' + num;
                                        d = s + ' context-menu-icon-delete';



                                        return d;
                                    }};
                            }





                            for (var i = 0; i < matches.length; i++) {
                                options.items.deleteWholeMatch.items["deleteWholeMatch_" + i] = {name: matchName,
                                    icon: function ($element, key, item, i) {
                                        var d = undefined;
                                        var num = parseInt(item.replace("deleteWholeMatch_", ""));
                                        var s = 'newClassDeleteMatch' + num;
                                        d = s + ' context-menu-icon-delete';



                                        return d;
                                    }};
                            }
                        } else if ((thisObject.matchViz.getNumberOfMatchings() > 0) && thisObject.matchViz.getMatchingsContainingNode(node) !== undefined && thisObject.matchViz.getMatchingsContainingNode(node).length === 1) {
                            var style = document.createElement('style');
                            style.type = 'text/css';
                            style.innerHTML = '.cssClass { color: #F00; }';
                            document.getElementsByTagName('head')[0].appendChild(style);

                            var options = {
                                callback: function (key, options) {
                                    var m = "clicked: " + key;
                                    var trigger = $trigger[0];
                                    var id = trigger.id;
                                    var epcVizName = trigger.ownerSVGElement.parentNode.id;
                                    var epcViz = thisObject.matchViz.getViz(epcVizName);
                                    var epc = epcViz;
                                    var nodeObject = epc.mapSVGIDToNode[id];
                                    var node = {modelID: epc.name, nodeID: nodeObject.id};
                                    if (key === "deleteWholeMatch") {
                                        thisObject.matchViz.deleteMatchContainingNode(node, "contextMenuNode");
                                        document.getElementById("matchMetrics").innerHTML = thisObject.matchViz.getHTMLCodeForMatchMetrics();
                                    } else if (key === "deleteNodeFromMatch") {
                                        thisObject.matchViz.deleteNodeFromMatch(node);
                                        document.getElementById("matchMetrics").innerHTML = thisObject.matchViz.getHTMLCodeForMatchMetrics();
                                    } else if (key.startsWith("addNodeToMatch_")) {
                                        var matches = thisObject.matchViz.getListOfMatches();
                                        var matchNum = parseInt(key.replace("addNodeToMatch_", ""));
                                        thisObject.matchViz.addNodeToMatch(node, matches[matchNum].matchID, matches[matchNum].mappingID);
                                        document.getElementById("matchMetrics").innerHTML = thisObject.matchViz.getHTMLCodeForMatchMetrics();
                                    } else {
                                        window.console && console.log(m) || alert(m);
                                    }
                                },
                                items: {
                                    "addNodeToMatch": {name: "Add Node to Match", icon: function ($element, key, item) {
                                            return 'context-menu-icon-1 context-menu-icon-add';
                                        }, items: {}},
                                    "deleteNodeFromMatch": {name: "Delete Node from Match", icon: "delete"},
                                    "deleteWholeMatch": {name: "Delete whole Match", icon: "delete"}
                                }
                            };
                        } else {
                            var style = document.createElement('style');
                            style.type = 'text/css';
                            style.innerHTML = '.cssClass { color: #F00; }';
                            document.getElementsByTagName('head')[0].appendChild(style);

                            var options = {
                                callback: function (key, options) {
                                    var m = "clicked: " + key;
                                    var trigger = $trigger[0];
                                    var id = trigger.id;
                                    var epcVizName = trigger.ownerSVGElement.parentNode.id;
                                    var epcViz = thisObject.matchViz.getViz(epcVizName);
                                    var epc = epcViz;
                                    var nodeObject = epc.mapSVGIDToNode[id];
                                    var node = {modelID: epc.name, nodeID: nodeObject.id};
                                    if (key === "deleteWholeMatch") {
                                        thisObject.matchViz.deleteMatchContainingNode(node, "contextMenuNode");
                                        document.getElementById("matchMetrics").innerHTML = thisObject.matchViz.getHTMLCodeForMatchMetrics();
                                    } else if (key === "deleteNodeFromMatch") {
                                        thisObject.matchViz.deleteNodeFromMatch(node);
                                        document.getElementById("matchMetrics").innerHTML = thisObject.matchViz.getHTMLCodeForMatchMetrics();
                                    } else if (key.startsWith("addNodeToMatch_")) {
                                        var matches = thisObject.matchViz.getListOfMatches();
                                        var matchNum = parseInt(key.replace("addNodeToMatch_", ""));
                                        thisObject.matchViz.addNodeToMatch(node, matches[matchNum].matchID, matches[matchNum].mappingID);
                                        document.getElementById("matchMetrics").innerHTML = thisObject.matchViz.getHTMLCodeForMatchMetrics();
                                    } else {
                                        window.console && console.log(m) || alert(m);
                                    }
                                },
                                items: {
                                    "addNodeToMatch": {name: "Add Node to Match", icon: function ($element, key, item) {
                                            return 'context-menu-icon-1 context-menu-icon-add';
                                        }, items: {}}
                                }
                            };
                        }

                        var trigger = $trigger[0];
                        var id = trigger.id;

                        var matches = thisObject.matchViz.getListOfMatches();

                        for (var i = 0; i < matches.length; i++) {
                            var string = '<style>.newClass' + i + '::before{  position: absolute;  top: 50%;  left: 0;  width: 2em;   font-family: "context-menu-icons";  font-size: 1em;  font-style: normal;  font-weight: normal;  line-height: 1;  color: ' + matches[i].color + ';  text-align: center;  -webkit-transform: translateY(-50%);      -ms-transform: translateY(-50%);       -o-transform: translateY(-50%);          transform: translateY(-50%);  -webkit-font-smoothing: antialiased;  -moz-osx-font-smoothing: grayscale;}</style>';
                            $(string).appendTo('head');

                            var matchName = "empty Match";
                            if (matches !== undefined && matches[i].name !== undefined) {
                                matchName = matches[i].name;
                                if (matchName.length > 17) {
                                    matchName = matchName.substring(0, 15);
                                    matchName = matchName + '...';
                                }
                                matchName = '"' + matchName + '", ...';
                            }

                            options.items.addNodeToMatch.items["addNodeToMatch_" + i] = {name: matchName,
                                icon: function ($element, key, item, i) {
                                    var d = undefined;
                                    var num = parseInt(item.replace("addNodeToMatch_", ""));
                                    var s = 'newClass' + num;
                                    d = s + ' context-menu-icon-add';



                                    return d;
                                }};
                        }

                        return (thisObject.matchViz.getNumberOfMatchings() > 0) && thisObject.matchViz.currentMapping !== undefined && options;
                    }
                });

            });

        }



    }

    this.unselectAllNodes = function () {
        if (this.selectedNodes.length > 0) {
            for (var i = 0; i < this.selectedNodes.length; i++) {
                matchViz.removeSelectedNode(this.selectedNodesInThisEPC[i].nodeID, this.selectedNodesInThisEPC[i].modelID, this.selectedNodesInThisEPC[i].vizEPC);
                $(this.selectedNodes[i]).css({"strokeWidth": "1", "stroke": "black"});
            }
            this.selectedNodesInThisEPC = [];
            this.selectedNodes = [];
        }
    }

    function contains(a, obj) {
        for (var i = 0; i < a.length; i++) {
            if (a[i] === obj) {
                return true;
            }
        }
        return false;
    }

    this.drawEdges();
    this.drawNodes();

    this.createControl(this.container.id);
}

