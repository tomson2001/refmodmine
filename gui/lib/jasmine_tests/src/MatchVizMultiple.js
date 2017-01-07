
function MatchVizMultiple(visualizations, mappings) {

    // all available visualizations
    this.visualizations = visualizations;

    this.isBinaryMatching = true;
    this.mergedMapping = undefined;

    if (mappings !== undefined && mappings.length === 1 && mappings[0].models.length > 2) {
        // matching is n-ary
        this.mergedMapping = mappings[0];
        this.isBinaryMatching = false;
    } else {
        // all available mappings
        this.mappings = mappings;
        this.isBinaryMatching = true;
    }


    // current epc visualizations
    this.rightEPCViz = null;
    this.leftEPCViz = null;

    // distinct colors for matches
    this.distinctColors = [[240, 163, 255], [0, 117, 220], [153, 63, 0], [0, 92, 49], [43, 206, 72], [255, 204, 153], [128, 128, 128], [148, 255, 181], [143, 124, 0], [157, 204, 0], [194, 0, 136], [0, 51, 128], [255, 164, 5], [255, 168, 187], [66, 102, 0], [255, 0, 16], [94, 241, 242], [0, 153, 143], [224, 255, 102], [116, 10, 255], [153, 0, 0], [255, 255, 128], [255, 255, 0], [255, 80, 5], [65, 184, 72], [153, 90, 207], [116, 189, 60], [185, 57, 159], [63, 201, 130], [225, 112, 208], [165, 186, 46], [87, 109, 215], [221, 176, 38], [101, 139, 208], [231, 141, 15], [78, 165, 212], [240, 162, 36], [113, 87, 155], [187, 170, 45], [182, 143, 222], [116, 196, 106], [223, 66, 130], [58, 149, 76], [208, 58, 76], [88, 194, 159], [188, 47, 27], [58, 190, 200], [228, 93, 40], [51, 127, 90], [234, 87, 65], [87, 130, 42], [158, 67, 123], [148, 180, 87], [164, 59, 90], [174, 176, 82], [202, 132, 182], [228, 146, 51], [227, 117, 155], [145, 178, 117], [172, 75, 25], [95, 108, 43], [229, 113, 94], [142, 129, 44], [228, 139, 140], [217, 172, 75], [168, 86, 90], [171, 118, 28], [233, 155, 111], [129, 99, 43], [219, 116, 45], [208, 171, 111], [153, 79, 44], [232, 154, 79], [184, 121, 75]];
    // counter indicating which color should be chosen next
    this.colorCounter = [];
    this.colorCounterMergedMatches = 0;

    // Zugeordnete Farbe für jeden Knoten im Falle eines gemergten Matches
    this.colorsNodes = [];
    // Zuordnung von Farben für jeden gemergten Match
    this.colorsMergedMatches = [];

    // counter used for visualization of merged mappings
    // jedem Knoten aus einem gemergten Matching wird die gleiche Farbe zugewiesen
    this.colorCounterNodes = 0;

    // Zuordnung von Farben für jeden binary Match
    this.colorsMatches = [];


    // Speicherung aller aktuell ausgewählten Knoten 
    this.selectedNodes = [];
    this.lastMatchingID = -1;
    this.unsavedChanges = false;


    // mapping between "MappingID_MatchID" to map
    this.mapMappingIDAndMatchIDToMap = [];
    this.listContainingAllMatches = [];

    this.currentMapping;

    this.isOneToOneMatching = true;

    this.matchedNodesPerMatching = [];

    this.tmpMappings = [];

    // needed for visibility management
    this.isMatchVisible = [];
    this.mergedMatchVisibilityList = [];


    // all states for redo and undo
    this.states = [];
    this.historyLimit = 20;
    this.historyPos = -1;


    this.log = [];
    logFile = this.log;
    matchVizObject = this;

    this.conditionsForRDFfullfilled = true;


    this.binaryMatchesContainMatch = function (matches, match) {
        for (var i = 0; i < matches.length; i++) {
            var tmpMatch = matches[i];
            if (match.nodeIDs.length === tmpMatch.nodeIDs.length && match.nodeIDs.length === 2) {
                var nodeId = match.nodeIDs[0];
                var modelId = match.modelIDs[0];
                var tmpNodeId = tmpMatch.nodeIDs[0];
                var tmpModelId = tmpMatch.modelIDs[0];

                var nodeId2 = match.nodeIDs[1];
                var modelId2 = match.modelIDs[1];
                var tmpNodeId2 = tmpMatch.nodeIDs[1];
                var tmpModelId2 = tmpMatch.modelIDs[1];

                if ((modelId === tmpModelId && modelId2 === tmpModelId2) || (modelId === tmpModelId2 && modelId2 === tmpModelId)) {
                    if ((nodeId === tmpNodeId && nodeId2 === tmpNodeId2) || (nodeId === tmpNodeId2 && nodeId2 === tmpNodeId)) {
                        return true;
                    }
                }

            }
        }
        return false;
    }

    this.getSplitMatches = function (mapping) {

        var newMaps = [];
        var iDofMatch = 0;

        for (var i = 0; i < mapping.maps.length; i++) {
            var map = mapping.maps[i];
            var nodes = [];
            for (var j = 0; j < map.modelIDs.length; j++) {
                var node = {modelID: map.modelIDs[j], nodeID: map.nodeIDs[j]};
                nodes.push(node);
            }

            if (nodes.length > 2) {
                // berechne alle 2-elementige Teilmengen
                for (var k = 0; k < nodes.length; k++) {
                    var node = nodes[k];
                    for (var j = k + 1; j < nodes.length; j++) {
                        var node2 = nodes[j];
                        var newModelIDs = [];
                        var newNodeIDs = [];
                        newModelIDs.push(node.modelID);
                        newModelIDs.push(node2.modelID);
                        newNodeIDs.push(node.nodeID);
                        newNodeIDs.push(node2.nodelID);
                        var newMatch = {modelIDs: newModelIDs, nodeIDs: newNodeIDs, matchID: iDofMatch, status: "OPEN", interpretation: "SIMILAR", value: "1"};
                        if (this.binaryMatchesContainMatch(newMaps, newMatch)) {
                            newMaps.push(newMatch);
                            iDofMatch++;
                        }
                    }
                }

            } else {
                var newMatch = {modelIDs: map.modelIDs, nodeIDs: map.nodeIDs, matchID: iDofMatch, status: "OPEN", interpretation: "SIMILAR", value: "1"};
                newMaps.push(newMatch);
                iDofMatch++;
            }
        }

        return newMaps;
    }

    this.getMatchingsWithSplitMatches = function () {
        var mappingsTMP = [];
        for (var i = 0; i < this.mappings.length; i++) {
            var mappingTMP = {"filename": this.mappings[i].filename, "id": this.mappings[i].id, "name": this.mappings[i].name, "maps": [], "models": this.mappings[i].models};
            mappingTMP.maps = this.getSplitMatches(this.mappings[i]);
            mappingsTMP.push(mappingTMP);
        }
        return mappingsTMP;
    }


    this.getCorrespondingMatchByID = function (matches, id) {
        for (var i = 0; i < matches.length; i++) {
            if (matches[i].matchID === id) {
                return matches[i];
            }
        }
    }

    this.getMergedMatches = function (mapping) {

        // result
        var newMaps = [];

        // needed for getting the corresponding match for a node
        var matchOfNode = [];

        // needed for getting all nodes for a match
        var nodesOfMatch = [];

        var iDofMatch = 0;

        for (var i = 0; i < mapping.maps.length; i++) {
            // iterate over each match
            var map = mapping.maps[i];
            var correspondingMatch = undefined;
            for (var j = 0; j < map.nodeIDs.length; j++) {
                var node = {modelID: map.modelIDs[j], nodeID: map.nodeIDs[j]};
                var nodeString = map.modelIDs[j] + ";" + map.nodeIDs[j];
                if (matchOfNode[nodeString] !== undefined) {
                    correspondingMatch = matchOfNode[nodeString];
                }
            }
            if (correspondingMatch !== undefined) {
                // add match nodes to corresponding match
                for (var j = 0; j < map.nodeIDs.length; j++) {
                    var node = {modelID: map.modelIDs[j], nodeID: map.nodeIDs[j]};
                    var nodeString = map.modelIDs[j] + ";" + map.nodeIDs[j];

                    // add node if it is not contained
                    if (!(nodesOfMatch[correspondingMatch.matchID].has(nodeString))) {
                        correspondingMatch.modelIDs.push(map.modelIDs[j]);
                        correspondingMatch.nodeIDs.push(map.nodeIDs[j]);
                        matchOfNode[nodeString] = correspondingMatch;
                    }

                }

            } else {
                // add match nodes to new match
                var newMatch = {modelIDs: map.modelIDs, nodeIDs: map.nodeIDs, matchID: iDofMatch, status: "OPEN", interpretation: "SIMILAR", value: "1"};
                nodesOfMatch[newMatch.matchID] = new Set();
                for (var j = 0; j < map.nodeIDs.length; j++) {
                    var node = {modelID: map.modelIDs[j], nodeID: map.nodeIDs[j]};
                    var nodeString = map.modelIDs[j] + ";" + map.nodeIDs[j];
                    nodesOfMatch[newMatch.matchID].add(nodeString);
                    matchOfNode[nodeString] = newMatch;
                }
                newMaps.push(newMatch);
                iDofMatch++;
            }
        }

        return newMaps;
    }

    this.mergeMatches = function () {
        for (var i = 0; i < this.mappings.length; i++) {
            this.mappings[i].maps = this.getMergedMatches(this.mappings[i]);
        }
    }


    this.areMappingsRDF = function () {
        if (this.mappings === undefined) {
            return false;
        }
        for (var i = 0; i < this.mappings.length; i++) {
            var mapping = this.mappings[i];
            if (mapping.filename.includes(".rdf")) {
                return true;
            }
        }
        return false;
    }


    this.checkInput = function () {
        if (this.areMappingsRDF()) {
            // show import options
            $('#loaderConfigModal').modal('show');
        } else {
            createDefaultView();
        }

    }


    var mappingForState = this.mappings;
    if (!this.isBinaryMatching) {
        mappingForState = this.mergedMapping;
    }

    if (mappingForState !== undefined) {
        this.states.push(JSON.parse(JSON.stringify(mappingForState)));
        this.historyPos = this.states.length;
    } else {
        this.states.push(undefined);
        this.historyPos = this.states.length;
    }

    this.setVisualizations = function (visualizations) {
        this.visualizations = visualizations;
        var notFoundNodes = [];
        for (var i = 0; i < this.selectedNodes.length; i++) {
            notFoundNodes.push(this.selectedNodes[i]);
        }
        for (var num = 0; num < this.visualizations.length; num++) {
            var vis = this.visualizations[num];
            this.visualizations[num].setNodesWhite();
            var delIndexes = [];

            for (var i = 0; i < notFoundNodes.length; i++) {
                if (notFoundNodes[i].vizEPC.container.id === vis.container.id) {
                    delIndexes.push(i);
                }
            }
            for (var i = delIndexes.length - 1; i >= 0; i--) {
                if (delIndexes[i] > -1) {
                    notFoundNodes.splice(delIndexes[i], 1);
                }
            }
        }
        for (var i = 0; i < notFoundNodes.length; i++) {
            for (var j = 0; j < this.selectedNodes.length; j++) {
                if (notFoundNodes[i] === this.selectedNodes[j]) {
                    this.selectedNodes.splice(i, 1);
                    break;
                }
            }
        }

        this.showMatching(false);

    }

    this.activeVizes = [];

    this.getVizByContainer = function (container) {
        for (var i = 0; i < this.visualizations.length; i++) {
            var vis = this.visualizations[i];
            if (vis.container.id === container.id) {
                return vis;
            }
        }
    }



    this.isMatchingBinaryMatching = function () {
        return this.isBinaryMatching;
    }

    this.setMatchingBinaryMatching = function (isBinary) {
        this.isBinaryMatching = isBinary;
    }

    this.isThisModelNext = function (modelName) {
        for (var j = 0; j < this.activeVizes.length; j++) {
            if (this.activeVizes[j] !== undefined && modelName === this.activeVizes[j].name) {
                return false;
            }
        }
        return true;
    }

    this.getNextModelName = function () {
        var vizSet = new Set();
        for (var vizNum = 0; vizNum < this.visualizations.length; vizNum++) {
            var visualization = this.visualizations[vizNum];
            var name = visualization.name;
            vizSet.add(name);
        }
        var array = Array.from(vizSet);
        for (var i = 0; i < array.length; i++) {
            var name = array[i];
            if (this.isThisModelNext(name)) {
                return name;
            }
        }
    }

    this.getVizesByContainerNum = function (containerNum) {
        var vizes = [];
        for (var i = 0; i < this.visualizations.length; i++) {
            var vis = this.visualizations[i];
            var name = vis.container.id;
            if (name.endsWith(containerNum)) {
                vizes.push(vis);
            }
        }
        return vizes;
    }

    this.removeActiveContainer = function (container) {
        var num = container.id.charAt(container.id.length - 1);
        this.activeVizes[num] = undefined;
    }

    this.setActiveContainer = function (container) {
        var num = container.id.charAt(container.id.length - 1);
        this.activeVizes[num] = (this.getVizByContainer(container));
        this.showMatching();

        if (num == "0") {
            this.leftEPCViz = this.getVizByContainer(container);
            this.showMatching();
        } else if (num == "1") {
            this.rightEPCViz = this.getVizByContainer(container);
            this.showMatching();
        }

        var vizes = this.getVizesByContainerNum(num);
        var deleteIndexes = [];
        for (var i = 0; i < vizes.length; i++) {
            vizes[i].unselectAllNodes();
            for (var j = 0; j < this.selectedNodes.length; j++) {
                if (this.selectedNodes[j].vizEPC.container.id === vizes[i].container.id) {
                    deleteIndexes.push(j);
                }
            }
        }
        for (var i = deleteIndexes.length - 1; i >= 0; i--) {
            if (deleteIndexes[i] > -1) {
                this.selectedNodes.splice(deleteIndexes[i], 1);
            }
        }

    }

    this.getViz = function (vizName) {
        for (var vizNum = 0; vizNum < this.visualizations.length; vizNum++) {
            var visualization = this.visualizations[vizNum];
            var container = visualization.container;
            var id = container.id;
            if (id === vizName) {
                return visualization;
            }
        }
    }


    // checks whether all mappings are binary mappings
    this.areAllMappingsBinary = function () {
        if (this.mappings === undefined) {
            return true;
        }
        // check for every mapping whether it contains a match with more than two models
        for (var mappingNum = 0; mappingNum < this.mappings.length; mappingNum++) {
            var nodeSet = new Set();
            var mapping = this.mappings[mappingNum];
            if (mapping.models.length !== 2) {
                return false;
            }

        }
        return true;
    }

    this.areAllMappingsOneToOne = function () {
        if (this.isBinaryMatching) {
            if (this.mappings === undefined) {
                return true;
            }
            // check for every mapping whether it contains a match with more than two models
            for (var mappingNum = 0; mappingNum < this.mappings.length; mappingNum++) {
                var nodeSet = new Set();
                var mapping = this.mappings[mappingNum];
                if (mapping.models.length !== 2) {
                    return false;
                }
                for (var k = 0; k < mapping.maps.length; k++) {
                    var map = mapping.maps[k];
                    if (map.nodeIDs.length !== 2) {
                        return false;
                    }
                }
            }
            return true;
        } else {
            if (this.mergedMapping === undefined) {
                return true;
            }


            var mapping = this.mergedMapping;
            if (mapping.models.length !== 2) {
                return false;
            }
            for (var k = 0; k < mapping.maps.length; k++) {
                var map = mapping.maps[k];
                if (map.nodeIDs.length !== 2) {
                    return false;
                }
            }

        }

    }

    this.getNumberOfMatchings = function () {
        if (!this.isBinaryMatching) {
            if (this.mergedMapping === undefined) {
                return 0;
            }
            return 1;
        }
        if (this.mappings === undefined) {
            return 0;
        }
        return this.mappings.length;
    }

    this.updateModalIfNecessary = function () {
        this.isBinaryMatching = this.isBinaryMatching && this.areAllMappingsBinary();
        this.isOneToOneMatching = this.areAllMappingsOneToOne();

        this.conditionsForRDFfullfilled = true; //this.isBinaryMatching && this.isOneToOneMatching && (this.getNumberOfMatchings() === 1);
        if (this.conditionsForRDFfullfilled) {
            $('#rdfOption').removeAttr('disabled');
        } else {
            $('#rdfOption').attr('disabled', 'disabled');
            $('#DATA_FORMAT').val("xml");
        }
    }

    this.updateModalIfNecessary();



    // checks whether a given mapping contains a given match
    this.mappingContainsMatch = function (mapping, match) {
        var matchArray = [];
        for (var j = 0; j < match.modelIDs.length; j++) {
            matchArray[j] = match.modelIDs[j] + "_" + match.nodeIDs[j];
        }
        for (var i = 0; i < mapping.maps.length; i++) {
            var map = mapping.maps[i];
            var mapArray = [];
            for (var k = 0; k < map.modelIDs.length; k++) {
                mapArray[k] = map.modelIDs[k] + "_" + map.nodeIDs[k];
            }

            // check for every match in mapping whether it contains all nodes given in match
            var success = matchArray.every(function (v, i) {
                return mapArray.indexOf(v) !== -1;
            });
            if (success) {
                return true;
            }

        }
        return false;
    }

    // method for getting the matching between two models identified by model names
    this.getBinaryMatchingBetweenModelsMappings = function (mappings, modelname1, modelname2) {
        if (mappings === undefined) {
            return undefined;
        }
        for (var i = 0; i < mappings.length; i++) {
            if ((mappings[i].models[0] === modelname1 && mappings[i].models[1] === modelname2) || (mappings[i].models[1] === modelname1 && mappings[i].models[0] === modelname2)) {
                this.currentMappingNum = i;
                return mappings[i];
            }
        }
    }

    // this method is needed for loading a matching file. If a n-ary mapping is loaded, the mapping in this.mappings has to be split to be consistent to the case of creating a completely new mapping
    this.getSplitMappings = function (mapping) {

        var lastMatchingID = 0;

        // create temporary mapping file
        var mappingsTMP = [];
        for (var matchNum = 0; matchNum < mapping.maps.length; matchNum++) {
            var match = mapping.maps[matchNum];
            if (match.modelIDs.length <= 2) {
                // match is binary match so add it to mapping list
                newMatch = {"matchID": 0, "nodeIDs": match.nodeIDs, "modelIDs": match.modelIDs, "status": match.status, "interpretation": match.interpretation, "value": match.value, "refEpcID": null};
                if (mappingsTMP === undefined) {
                    mappingsTMP = [];
                }

                var searchedMapping = this.getBinaryMatchingBetweenModelsMappings(mappingsTMP, match.modelIDs[0], match.modelIDs[1]);
                if (searchedMapping === undefined) {
                    lastMatchingID++;
                    var models = [];
                    models[0] = match.modelIDs[0];
                    models[1] = match.modelIDs[1];
                    var mappingTMP = {"filename": "newFileName", "id": lastMatchingID, "name": models[0] + " - " + models[1], "maps": [], "models": models};
                    mappingsTMP.push(mappingTMP);
                    searchedMapping = mappingTMP;
                } else {
                    // get and set next ID
                    var lastID = -1;
                    for (var i = 0; i < searchedMapping.maps.length; i++) {
                        var id = searchedMapping.maps[i].matchID;
                        if (id > lastID) {
                            lastID = id;
                        }
                    }
                    lastID++;
                    newMatch.matchID = lastID;
                }
                if (!this.mappingContainsMatch(searchedMapping, newMatch)) {
                    searchedMapping.maps.push(newMatch);
                }
            } else {
                // match is n-ary
                var result = [];

                // get all combinations of models in match
                for (var i = 0; i < match.nodeIDs.length; i++) {
                    var node = {nodeID: match.nodeIDs[i], modelID: match.modelIDs[i]};
                    for (var j = i + 1; j < match.nodeIDs.length; j++) {
                        var res = [];
                        res[0] = node;
                        res[1] = {nodeID: match.nodeIDs[j], modelID: match.modelIDs[j]};
                        result.push(res);
                    }
                }

                // create a match for every combination
                for (var k = 0; k < result.length; k++) {
                    var currentResult = result[k];
                    var arrayNodeIDs = [];
                    var arrayModelIDs = [];

                    // create lists of nodeIDs and modelIDs for new match
                    for (var nodeNum = 0; nodeNum < currentResult.length; nodeNum++) {
                        arrayNodeIDs.push(currentResult[nodeNum].nodeID);
                        arrayModelIDs.push(currentResult[nodeNum].modelID);
                    }

                    if (arrayModelIDs[0] !== arrayModelIDs[1]) {
                        // create new match
                        newMatch = {"matchID": 0, "nodeIDs": arrayNodeIDs, "modelIDs": arrayModelIDs, "status": match.status, "interpretation": match.interpretation, "value": match.value, "refEpcID": null};

                        // serach matching between the two models
                        var searchedMapping = this.getBinaryMatchingBetweenModelsMappings(mappingsTMP, arrayModelIDs[0], arrayModelIDs[1]);
                        if (searchedMapping === undefined) {
                            // matching doesn't exist so create it
                            lastMatchingID++;
                            var models = [];
                            models[0] = arrayModelIDs[0];
                            models[1] = arrayModelIDs[1];
                            var mappingTMP = {"filename": "newFileName", "id": lastMatchingID, "name": models[0] + " - " + models[1], "maps": [], "models": models};
                            mappingsTMP.push(mappingTMP);
                            searchedMapping = mappingTMP;
                        } else {
                            // get and set next ID
                            var lastID = -1;
                            for (var i = 0; i < searchedMapping.maps.length; i++) {
                                var id = searchedMapping.maps[i].matchID;
                                if (id > lastID) {
                                    lastID = id;
                                }
                            }
                            lastID++;
                            newMatch.matchID = lastID;
                        }

                        if (!this.mappingContainsMatch(searchedMapping, newMatch)) {
                            searchedMapping.maps.push(newMatch);
                        }
                    }



                }
            }
        }
        return mappingsTMP;
    }

    if (this.mappings !== undefined && this.mappings !== null) {
        for (var i = 0; i < this.mappings.length; i++) {
            var mapping = this.mappings[i];
            if (mapping.models.length > 2) {
                this.isBinaryMatching = false;
            }
            for (var m = 0; m < mapping.maps.length; m++) {
                var map = mapping.maps[m];
                for (var matchNodeNum = 0; matchNodeNum < map.nodeIDs.length; matchNodeNum++) {
                    var code = map.nodeIDs[matchNodeNum] + " _ " + map.modelIDs[matchNodeNum];
                    if (this.matchedNodesPerMatching[mapping.id] !== undefined) {
                        if (this.matchedNodesPerMatching[mapping.id][code] === undefined) {
                            this.matchedNodesPerMatching[mapping.id][code] = 1;
                        } else {
                            this.matchedNodesPerMatching[mapping.id][code] = this.matchedNodesPerMatching[mapping.id][code] + 1;
                        }
                    } else {
                        this.matchedNodesPerMatching[mapping.id] = [];
                        this.matchedNodesPerMatching[mapping.id][code] = 1;
                    }
                }
            }
        }
    }

    this.setRigthEPCViz = function (rightViz) {
        this.rightEPCViz = rightViz;
        this.showMatching();
    }

    this.setLeftEPCViz = function (leftViz) {
        this.leftEPCViz = leftViz;
        this.showMatching();
    }

    this.contains = function (a, obj) {
        for (var i = 0; i < a.length; i++) {
            if (a[i] === obj) {
                return true;
            }
        }
        return false;
    }

    function contains(a, obj) {
        for (var i = 0; i < a.length; i++) {
            if (a[i] === obj) {
                return true;
            }
        }
        return false;
    }

    this.setModels = function (models) {
        var deletedModels = [];
        if (this.oldModels === undefined) {
            this.oldModels = models;
            return;
        }
        for (var i = 0; i < this.oldModels.length; i++) {
            var oldModelName = this.oldModels[i];
            var stayed = false;
            for (var j = 0; j < models.length; j++) {
                if (models[j] === oldModelName) {
                    stayed = true;
                }
            }
            if (!stayed) {
                deletedModels.push(oldModelName);
            }
        }

        var activeVizes = [];
        for (var j = 0; j < deletedModels.length; j++) {
            var vizes = this.getVisualizationsForModel(deletedModels[j]);
            for (var k = 0; k < vizes.length; k++) {
                var tmpViz = vizes[k];
                if (tmpViz.container.getAttribute("style") === "display: block;") {
                    activeVizes.push(tmpViz);
                }
            }
        }

        for (var j = 0; j < activeVizes.length; j++) {
            var activeViz = activeVizes[j];
            activeViz.container.setAttribute("style", "display: none");
            activeViz.unselectAllNodes();
            var index = activeViz.container.id.lastIndexOf("_") + 1;
            var ending = activeViz.container.id.substring(index);
            var num = ending.replace("dropDownNum", "");
            var dropdownButton = document.getElementById('dropdown_button_' + num);
            dropdownButton.firstChild.data = "Select a model";
        }



        if (this.isBinaryMatching && this.mappings !== undefined) {
            for (var l = this.mappings.length - 1; l >= 0; l--) {
                for (var j = 0; j < deletedModels.length; j++) {
                    var deletedModel = deletedModels[j];
                    var mapping = this.mappings[l];
                    if (contains(mapping.models, deletedModel)) {
                        this.mappings.splice(l, 1);
                    }
                    if (this.currentMapping !== undefined && mapping.id === this.currentMapping.id) {
                        this.currentMapping = undefined;
                        this.showMatching(false);
                    }


                }

            }
        }


        if (!this.isBinaryMatching && this.mergedMapping !== undefined) {
            var deleteMatchEntries = [];
            var mapsTmp = this.mergedMapping.maps;
            for (var i = 0; i < mapsTmp.length; i++) {
                var deleteNodeEntries = [];
                var numOfNodes = 0;
                var nodeIDsTmp = this.mergedMapping.maps[i].nodeIDs;
                for (var j = 0; j < nodeIDsTmp.length; j++) {
                    if (!contains(deletedModels, this.mergedMapping.maps[i].modelIDs[j])) {
                        numOfNodes++;
                    } else {
                        deleteNodeEntries.push(j);
                    }
                }
                for (var t = deleteNodeEntries.length - 1; t >= 0; t--) {
                    this.mergedMapping.maps[i].nodeIDs.splice(deleteNodeEntries[t], 1);
                    this.mergedMapping.maps[i].modelIDs.splice(deleteNodeEntries[t], 1);
                }
                if (numOfNodes < 2) {
                    deleteMatchEntries.push(i);
                }
            }

            for (var t = deleteMatchEntries.length - 1; t >= 0; t--) {
                this.mergedMapping.maps.splice(deleteMatchEntries[t], 1);
            }

            this.mergedMapping.models = models;
            this.showMatching(false);
        }


        this.oldModels = models;
    }



    this.showMatching = function (withSave) {
        if (withSave === undefined) {
            withSave = true;
        }
        for (var num = 0; num < this.visualizations.length; num++) {
            this.visualizations[num].setNodesWhite();
        }
        if (this.rightEPCViz === null || this.leftEPCViz === null) {
            return;
        }

        if (withSave) {
            // save potential changes to a matching
            var table = document.getElementById('matchesTable');
            if (table !== null) {
                var tbody = table.tBodies[0];
                if (tbody !== undefined) {
                    for (var i = 0; i < tbody.children.length - 1; i++) {

                        var row = tbody.children[i];
                        var id = row.id;
                        var value = document.getElementById('inputValue' + id).value;
                        var status = document.getElementById('statusSelection' + id).value;
                        var type = document.getElementById('typeSelection' + id).value;

                        if (!this.isBinaryMatching) {
                            var match = this.getMatchingNotByID(this.mergedMapping, parseInt(id));

                            if (match !== undefined) {
                                match.status = status;
                                match.value = value;
                                match.interpretation = type;
                            }
                        } else {
                            var match = this.getMatching(this.currentMapping.id, parseInt(id));
                            if (match !== undefined) {
                                match.status = status;
                                match.value = value;
                                match.interpretation = type;
                            }
                        }

                        // skip details
                        i++;
                    }
                }
            }
        }


        // check whether the selected models are the same
        if (this.rightEPCViz.name === this.leftEPCViz.name) {
            this.currentMapping = undefined;
            var table = document.getElementById('matchesTable');
            var new_tbody = document.createElement('tbody');
            var tbody = table.children[0];
            tbody.parentNode.replaceChild(new_tbody, tbody);
            document.getElementById("matchMetrics").innerHTML = this.getHTMLCodeForMatchMetrics();
            return;
        }


        // get current mapping

        if (this.isBinaryMatching) {
            if (this.mappings !== undefined) {
                // get current binary mapping
                this.currentMapping = this.getMatchingBetweenEPCS(this.rightEPCViz, this.leftEPCViz);
            } else {
                this.currentMapping = undefined;
            }
        } else {
            this.currentMapping = this.mergedMapping;
        }



        // visualize all matches of current mapping
        if (this.currentMapping !== undefined) {
            var numMatches = this.currentMapping.maps.length;
            for (var i = 0; i < numMatches; i++) {
                // visualize match
                if (!this.isBinaryMatching) {

                    // get visibility of match
                    var isVisible = false;
                    if (this.mergedMatchVisibilityList[this.currentMapping.maps[i].matchID] === undefined) {
                        isVisible = true;
                    } else {
                        isVisible = this.mergedMatchVisibilityList[this.currentMapping.maps[i].matchID];
                    }

                    // show match
                    this.showCurrentMatch(this.currentMapping.maps[i], !isVisible);
                } else {

                    // get visibility of match
                    var isVisible = false;
                    if (this.isMatchVisible[this.currentMapping.id] === undefined) {
                        isVisible = true;
                    } else if (this.isMatchVisible[this.currentMapping.id][this.currentMapping.maps[i].matchID] === undefined) {
                        isVisible = true;
                    } else {
                        isVisible = this.isMatchVisible[this.currentMapping.id][this.currentMapping.maps[i].matchID];
                    }

                    // show match
                    this.showMatch(this.currentMapping.id, this.currentMapping.maps[i].matchID, !isVisible);
                }
            }
        }


        // update match metrics
        document.getElementById("matchMetrics").innerHTML = this.getHTMLCodeForMatchMetrics();
        var table = document.getElementById('matchesTable');
        if (table !== null) {
            var new_tbody = document.createElement('tbody');
            var tbody = table.children[0];
            tbody.parentNode.replaceChild(new_tbody, tbody);
            this.createTable();
        }
    }

    this.getNodeLabel = function (modelID, nodeID) {
        for (var visNum = 0; visNum < this.visualizations.length; visNum++) {
            var vis = this.visualizations[visNum];
            if (vis.name === modelID) {
                for (var nNum = 0; nNum < vis.genericNodes.length; nNum++) {
                    var node = vis.genericNodes[nNum];
                    if (node.id == nodeID) {
                        return node.label;
                    }
                }
            }
        }
        return undefined;

    }

    // method for calculating a merged mapping. This is needed for representing non binary matches
    this.calculateMergedMatching = function () {
        if (this.mappings === undefined || this.mappings === null || this.mappings.length === 0) {
            return;
        }

        // set containing all epcs of the merged matching
        var epcSet = new Set();

        // result
        this.mergedMapping = [];


        this.mappingMapModels = [];
        this.mapMappingIDAndMatchIDToMap = [];
        this.listContainingAllMatches = [];

        for (var i = 0; i < this.mappings.length; i++) {
            // iterate over each mapping
            var mapping = this.mappings[i];
            var models = mapping.models;

            // add epcs to epcSet
            for (var j = 0; j < models.length; j++) {
                // iterate over each Model
                var modelName = this.mappings[i].models[j];
                epcSet.add(modelName);
            }

            // for each match
            for (var len = 0; len < mapping.maps.length; len++) {
                var map = mapping.maps[len];

                // initialize number of corresponding matching
                var numOfMatching = -1;

                // try to get number of corresponding merged match
                // iteriere über alle bereits existierenden gemergten Matches
                for (var exNum = 0; exNum < this.listContainingAllMatches.length; exNum++) {

                    // get merged match
                    var mappingIDAndMatchIDList = this.listContainingAllMatches[exNum];

                    // iteriere über alle Komponenten des gemergten Matches
                    for (var exNum2 = 0; exNum2 < mappingIDAndMatchIDList.length; exNum2++) {

                        // get component of merged match
                        var mappingIDAndMatchID = mappingIDAndMatchIDList[exNum2];

                        // frage zugehörige map ab
                        var existingMap = this.mapMappingIDAndMatchIDToMap[mappingIDAndMatchID].map;

                        // if first match node is contained in merged match OR if second match node is contained in merged match
                        if ((map.modelIDs[0] === existingMap.modelIDs[0] && map.nodeIDs[0] === existingMap.nodeIDs[0]) || (map.modelIDs[1] === existingMap.modelIDs[1] && map.nodeIDs[1] === existingMap.nodeIDs[1]) || (map.modelIDs[0] === existingMap.modelIDs[1] && map.nodeIDs[0] === existingMap.nodeIDs[1]) || (map.modelIDs[1] === existingMap.modelIDs[0] && map.nodeIDs[1] === existingMap.nodeIDs[0])) {

                            // set corresponding merged match
                            numOfMatching = exNum;
                        }
                    }
                }

                // add match to list of merged matches
                if (numOfMatching !== -1) {
                    var tmp = this.listContainingAllMatches[numOfMatching];
                    tmp.push(mapping.id + "_" + map.matchID);
                    this.listContainingAllMatches[numOfMatching] = tmp;
                    this.mapMappingIDAndMatchIDToMap[mapping.id + "_" + map.matchID] = {map: map, mapping: mapping};
                } else {
                    // a new Match must be created
                    // list containing this match can be added, because this is outer the loop
                    this.listContainingAllMatches.push([mapping.id + "_" + map.matchID]);
                    this.mapMappingIDAndMatchIDToMap[mapping.id + "_" + map.matchID] = {map: map, mapping: mapping};
                }

            }

        }

        var newMaps = [];

        // create merged matching

        // export every merged match
        for (var mergedMatchNum = 0; mergedMatchNum < this.listContainingAllMatches.length; mergedMatchNum++) {

            // necessarray to prevent added a node twice
            var codesSet = new Set();

            var nodesList = [];
            var modelList = [];

            // the following 3 lists are needed for calculating the status, interpretation and value of the merged match
            var statusList = [];
            var interpretationList = [];
            var valueList = [];

            var mergedMatch = this.listContainingAllMatches[mergedMatchNum];
            for (var matchNum = 0; matchNum < mergedMatch.length; matchNum++) {
                var match = mergedMatch[matchNum];
                var map = this.mapMappingIDAndMatchIDToMap[match].map;

                // store status, interpretation and value of every match
                statusList.push(map.status);
                interpretationList.push(map.interpretation);
                valueList.push(map.value);

                for (var nodeNum = 0; nodeNum < map.nodeIDs.length; nodeNum++) {

                    // es soll kein Knoten doppelt hinzugefuegt werden, daher Set mit allen bereits eingefügten Knoten
                    if (!codesSet.has(map.modelIDs[nodeNum] + " _ " + map.nodeIDs[nodeNum])) {
                        codesSet.add(map.modelIDs[nodeNum] + " _ " + map.nodeIDs[nodeNum]);
                        nodesList.push(map.nodeIDs[nodeNum]);
                        modelList.push(map.modelIDs[nodeNum]);
                    }
                    // for debugging: nodesSet.add(this.getNodeLabel(map.modelIDs[nodeNum], map.nodeIDs[nodeNum]));
                }
            }

            // count number of each status, interpretation and value
            // source: http://stackoverflow.com/questions/5667888/counting-the-occurrences-of-javascript-array-elements
            var countsStatus = {};
            var countsInterpretation = {};
            var countsValue = {};

            for (var c = 0; c < statusList.length; c++) {
                var num = statusList[c];
                countsStatus[num] = countsStatus[num] ? countsStatus[num] + 1 : 1;

                var num = interpretationList[c];
                countsInterpretation[num] = countsInterpretation[num] ? countsInterpretation[num] + 1 : 1;

                var num = valueList[c];
                countsValue[num] = countsValue[num] ? countsValue[num] + 1 : 1;
            }

            var status = "";
            var statusOccurances = -1;
            var interpretation = "";
            var interpretationOccurences = -1;
            var value = "";
            var valueOccurrences = -1;

            // set status, interpretation and value with most occurrences
            Object.keys(countsStatus).forEach(function (key) {
                if (countsStatus[key] > statusOccurances) {
                    status = key;
                }
            });
            Object.keys(countsInterpretation).forEach(function (key) {
                if (countsInterpretation[key] > interpretationOccurences) {
                    interpretation = key;
                }
            });
            Object.keys(countsValue).forEach(function (key) {
                if (countsValue[key] > valueOccurrences) {
                    value = key;
                }
            });

            var id = newMaps.length;
            newMaps.push({modelIDs: modelList, nodeIDs: nodesList, matchID: id, status: status, interpretation: interpretation, value: value});
        }

        this.mergedMapping = {id: "mergedMatchingID", models: Array.from(epcSet), maps: newMaps};
        this.isBinaryMatching = false;
        this.currentMapping = this.mergedMapping;
    }


    this.getMatchingNotByID = function (mapping, matchID) {

        for (var j = 0; j < mapping.maps.length; j++) {
            if (mapping.maps[j].matchID == matchID) {
                return mapping.maps[j];
            }
        }

        return undefined;
    }

    this.getMatching = function (mappingID, matchID) {
        if (this.mappings === undefined) {
            return undefined;
        }
        for (var i = 0; i < this.mappings.length; i++) {
            if (this.mappings[i].id == mappingID) {
                for (var j = 0; j < this.mappings[i].maps.length; j++) {
                    if (this.mappings[i].maps[j].matchID == matchID) {
                        return this.mappings[i].maps[j];
                    }
                }

            }
        }
        return undefined;
    }

    this.getVisualizationsForModel = function (modelID) {
        var result = [];
        for (var i = 0; i < this.visualizations.length; i++) {
            if (this.visualizations[i].name === modelID) {
                result.push(this.visualizations[i]);
            }
        }

        return result;
    }

    this.removeMatchInViz = function (matchOrMatchId) {
        var match;
        if (typeof matchOrMatchId === 'string' || typeof matchOrMatchId === 'number') {
            // get Match by id
            if (!this.isBinaryMatching) {
                match = this.getMatchingNotByID(this.mergedMapping, matchOrMatchId);
            } else {
                match = this.getMatchingNotByID(this.currentMapping, matchOrMatchId);
            }
        } else {
            match = matchOrMatchId;
        }

        // remove all nodes contained in match from all corresponding visualizations
        for (var num = 0; num < match.modelIDs.length; num++) {
            var vis = this.getVisualizationsForModel(match.modelIDs[num]);
            for (var visNum = 0; visNum < vis.length; visNum++) {
                vis[visNum].setColor(match.nodeIDs[num], null, null);
            }

        }

    }


    this.getColorForMergedMatch = function (match) {


        if (!(match.matchID in this.colorsMergedMatches)) {
            this.colorsMergedMatches[match.matchID] = rgbToHex(this.distinctColors[this.colorCounterMergedMatches]);
            this.colorCounterMergedMatches++;
        }
        return this.colorsMergedMatches[match.matchID];

    }

    // possible BUG: isMatchVisible is not updated
    this.showCurrentMatch = function (match, hide) {
        var color = undefined;
        if (hide) {
            color = null;
        } else {
            if (this.getColorForMergedMatch(match) === undefined) {
                color = rgbToHex(this.distinctColors[this.colorCounterNodes]);
                this.colorCounterNodes++;
            } else {
                color = this.getColorForMergedMatch(match);
            }

            for (var i = 0; i < match.nodeIDs.length; i++) {
                var nodeCode = match.nodeIDs[i] + " _ " + match.modelIDs[i];
                this.colorsNodes[nodeCode] = color;
            }

            this.colorsMergedMatches[match.id] = color;

        }

        // color node in every affected visualization
        for (var i = 0; i < match.modelIDs.length; i++) {
            var vises = this.getVisualizationsForModel(match.modelIDs[i]);
            for (var j = 0; j < vises.length; j++) {
                vises[j].setColor(match.nodeIDs[i], color, match.matchID);
            }
        }

    }

    this.showMatch = function (mappingID, matchOrMatchId, hide) {

        var match;
        if (typeof matchOrMatchId === 'string' || typeof matchOrMatchId === 'number') {
            // get Match by id
            if (!this.isBinaryMatching) {
                match = this.getMatchingNotByID(this.mergedMapping, matchOrMatchId);
                this.mergedMatchVisibilityList[match.matchID] = !hide;
            } else {
                match = this.getMatching(mappingID, matchOrMatchId);
                if (this.isMatchVisible[mappingID] === undefined) {
                    this.isMatchVisible[mappingID] = [];
                }
                this.isMatchVisible[mappingID][match.matchID] = !hide;
            }
        } else {
            match = matchOrMatchId;
        }

        var color = undefined;

        if (hide) {
            color = null;
        } else {
            if (this.isBinaryMatching) {
                // check if match has already an assigned color
                var mapping = this.getMatchingBetweenEPCS(this.leftEPCViz, this.rightEPCViz);
                if (!(mapping.id in this.colorsMatches)) {
                    this.colorsMatches[mapping.id] = [];
                    this.colorCounter[mapping.id] = 0;
                }
                if (!(match.matchID in this.colorsMatches[mapping.id])) {
                    this.colorsMatches[mapping.id][match.matchID] = rgbToHex(this.distinctColors[this.colorCounter[mapping.id]]);
                    this.colorCounter[mapping.id]++;
                }

                color = this.colorsMatches[mapping.id][match.matchID];
            } else {
                if (this.getColorForMergedMatch(match) === undefined) {
                    color = rgbToHex(this.distinctColors[this.colorCounterNodes]);
                    this.colorCounterNodes++;
                } else {
                    color = this.getColorForMergedMatch(match);
                }

                for (var i = 0; i < match.nodeIDs.length; i++) {
                    var nodeCode = match.nodeIDs[i] + " _ " + match.modelIDs[i];
                    this.colorsNodes[nodeCode] = color;
                }
                this.colorsMergedMatches[match.id] = color;

            }
        }


        // color each affected node
        for (var i = 0; i < match.modelIDs.length; i++) {
            var vises = this.getVisualizationsForModel(match.modelIDs[i]);
            for (var j = 0; j < vises.length; j++) {
                vises[j].setColor(match.nodeIDs[i], color, match.matchID);
            }
        }

    }

    this.getMatchColor = function (matchID) {
        if (this.isBinaryMatching) {
            var mapping = this.getMatchingBetweenEPCS(this.leftEPCViz, this.rightEPCViz);
            return this.colorsMatches[mapping.id][matchID];
        } else {
            return this.getColorForMergedMatch(this.getMatchingNotByID(this.mergedMapping, matchID));
        }
    }

    // source: http://stackoverflow.com/questions/6473111/add-delete-table-rows-dynamically-using-javascript
    this.addMatchToMatchesListStringConcatenation = function (match) {
        var tr = document.createElement('tr');

        // current visibility state must be taken into account
        var checkedString = " ";
        if (this.isBinaryMatching) {
            if (this.isMatchVisible[this.currentMapping.id] === undefined || this.isMatchVisible[this.currentMapping.id][match.matchID] === undefined || this.isMatchVisible[this.currentMapping.id][match.matchID]) {
                checkedString = ' checked="checked"';
            }
        } else {
            if (this.mergedMatchVisibilityList[match.matchID] === undefined || this.mergedMatchVisibilityList[match.matchID]) {
                checkedString = ' checked="checked"';
            }
        }



        var rowContent = '<tr class="clickable-row matchList-menu active" id="' + match.matchID + '">                     <td id="colorCell' + match.matchID + '" style="background-color: ' + this.getMatchColor(match.matchID) + '"></td><td><input id="checkbox' + match.matchID + '"' + checkedString + ' type="checkbox"></td>  <td>' + match.nodeIDs.length + ' nodes</td>       <td><a href="#" onclick = "matchViz.deleteMatch(' + match.matchID + '); return false;" ><span class="glyphicon glyphicon-trash"></span></a></td>            <td><a href="#" onclick = "return false;" ><i class="glyphicon glyphicon-collapse-down collapsed" data-toggle="collapse" data-target="#accordionTable' + match.matchID + '" id="collapseButton' + match.matchID + '" aria-expanded="false"></i></a></td> </tr>';
        tr.innerHTML = rowContent;
        tr.className = "clickable-row matchList-menu active";
        tr.id = match.matchID;




        var trDetails = document.createElement('tr');
        var rowContentDetails = '<tr id="details_' + match.matchID + '">             <td colspan="5" class="hiddenRow">                 <div id="accordionTable' + match.matchID + '" class="collapse">                     <div class="row"> <div class="col-sm-3">       Status:     </div>     <div class="col-sm-9">       <select id="statusSelection' + match.matchID + '" class="form-control">   <option>OPEN</option>   <option>CLOSED</option> </select> </div>  </div> <div class="row"> <div class="col-sm-3">       value:     </div>     <div class="col-sm-9">       <input id="inputValue' + match.matchID + '" value="' + match.value + '" step="0.01" min="0" max="1" class="form-control" type="number">           </div>  </div> <div class="row"> <div class="col-sm-3">       Type:     </div>     <div class="col-sm-9">       <select id="typeSelection' + match.matchID + '" class="form-control">   <option>SIMILAR</option>   <option>SPECIALIZATION</option>   <option>EQUAL</option>   <option>CONCATENATION</option>   <option>PART_OF</option> <option>ANALOGUE</option> </select>       </div>                    </div>   <hr> <div class="row"><div class="col-sm-12">       Matched Nodes:     </div> </div> <div class="row"> <div class="col-sm-12"><table class="table"> <tbody><tr><th>EPC Name</th><th>Node Label</th> </tr>';

        // add name of epc and label for each matched node to table
        for (var mn = 0; mn < match.nodeIDs.length; mn++) {
            rowContentDetails = rowContentDetails + '<tr class="context-menu-matches-node" data-matchid="' + match.matchID + '" data-modelid="' + match.modelIDs[mn] + '" data-nodeid="' + match.nodeIDs[mn] + '">';
            var col1 = '<td class="col-sm-6">' + match.modelIDs[mn] + "</td>";
            var col2 = '<td class="col-sm-6">' + this.getNodeLabel(match.modelIDs[mn], match.nodeIDs[mn]) + "</td>";
            rowContentDetails = rowContentDetails + col1;
            rowContentDetails = rowContentDetails + col2;
            rowContentDetails = rowContentDetails + "</tr>";
        }

        trDetails.innerHTML = rowContentDetails;
        trDetails.id = 'details_' + match.matchID;

        var table = document.getElementById('matchesTable');
        var tbody = table.children[0];
        var new_row = tr;

        handleVisibilityFunction = this.handleVisibilitySelection;
        oldThis = this;
        new_row.cells[1].getElementsByTagName('input')[0].onclick = function () {
            handleVisibilityFunction(this.checked, match.matchID, oldThis);
        };

        var new_row_info = trDetails;

        // add rows
        tbody.appendChild(new_row);
        tbody.appendChild(new_row_info);

        // set selected status and interpretation
        var idType = 'typeSelection' + match.matchID;
        document.getElementById(idType).value = match.interpretation;
        var idStatus = 'statusSelection' + match.matchID;
        document.getElementById(idStatus).value = match.status;

    }

    this.getMatchingBetweenEPCS = function (epc1, epc2) {
        if (this.mappings === undefined) {
            return undefined;
        }
        for (var i = 0; i < this.mappings.length; i++) {
            if ((this.mappings[i].models[0] === epc1.name && this.mappings[i].models[1] === epc2.name) || (this.mappings[i].models[1] === epc1.name && this.mappings[i].models[0] === epc2.name)) {
                this.currentMappingNum = i;
                return this.mappings[i];
            }
        }
    }


    for (var num = 0; num < this.visualizations.length; num++) {
        this.visualizations[num].setNodesWhite();
    }




    this.getNumberOfMatchesInCurrentMatching = function () {
        if (this.currentMapping !== undefined && this.currentMapping !== null) {
            return (this.currentMapping.maps.length);
        }
        return 0;
    }


    this.getNumberOfMatches = function () {
        if (!this.isBinaryMatching) {
            if (this.mergedMapping === undefined) {
                return 0;
            }
            return this.mergedMapping.maps.length;
        }

        if (this.mappings === undefined) {
            return 0;
        }
        var num = 0;
        for (var i = 0; i < this.mappings.length; i++) {
            num = num + this.mappings[i].maps.length;
        }
        return num;
    }


    this.setSelectedNode = function (nodeID, vizEPC) {
        this.selectedNodes = [];
        this.selectedNodes[0] = {nodeID: nodeID, modelID: vizEPC.name, vizEPC: vizEPC};
        for (var i = 0; i < this.visualizations.length; i++) {
            if (this.visualizations[i].container.id !== vizEPC.container.id) {
                this.visualizations[i].unselectAllNodes();
            }
        }
    }

    this.removeSelectedNode = function (nodeID, modelID, vizEPC) {
        var deleteIndex = -1;
        for (var i = 0; i < this.selectedNodes.length; i++) {
            if (this.selectedNodes[i].nodeID === nodeID && this.selectedNodes[i].modelID === modelID && this.selectedNodes[i].vizEPC.container.id === vizEPC.container.id) {
                deleteIndex = i;
                break;
            }
        }
        if (deleteIndex > -1) {
            this.selectedNodes.splice(deleteIndex, 1);
        }
    }

    this.removeSelectedNodes = function () {
        this.selectedNodes = [];
    }

    this.addSelectedNode = function (nodeID, modelID, vizEPC) {
        this.selectedNodes.push({nodeID: nodeID, modelID: modelID, vizEPC: vizEPC});
    }


    this.haveSelectedNodesAtLeatsTwoDifferentModels = function (selectedNodes) {
        var modelsSet = new Set();
        for (var i = 0; i < selectedNodes.length; i++) {
            modelsSet.add(selectedNodes[i].modelID);
        }

        return modelsSet.size >= 2;
    }

    this.getNumberOfDifferentModels = function (models) {
        var modelsSet = new Set();
        for (var i = 0; i < models.length; i++) {
            modelsSet.add(models[i].modelID);
        }

        return modelsSet.size;
    }


    this.potentialMatchResultsInNtoM = function (selectedNodes) {
        var modelSet = new Set();
        for (var i = 0; i < selectedNodes.length; i++) {
            modelSet.add(selectedNodes[i].modelID);
        }
        return selectedNodes.length > modelSet.size;
    }

    this.potentialMatchResultsInNtoMForGivenMatch = function (selectedNodes, match) {
        var modelSet = new Set();
        for (var i = 0; i < selectedNodes.length; i++) {
            modelSet.add(selectedNodes[i].modelID);
        }
        for (var j = 0; j < match.modelIDs.length; j++) {
            modelSet.add(match.modelIDs[j]);
        }
        var length = selectedNodes.length + match.modelIDs.length;

        return length > modelSet.size;
    }

    this.potentialMatchResultsInNary = function (selectedNodes) {
        var modelSet = new Set();
        for (var i = 0; i < selectedNodes.length; i++) {
            modelSet.add(selectedNodes[i].modelID);
        }
        return modelSet.size > 2;
    }

    this.potentialMatchResultsInNaryForGivenMatch = function (selectedNodes, match) {
        var modelSet = new Set();
        for (var i = 0; i < selectedNodes.length; i++) {
            modelSet.add(selectedNodes[i].modelID);
        }
        for (var j = 0; j < match.modelIDs.length; j++) {
            modelSet.add(match.modelIDs[j]);
        }
        return modelSet.size > 2;
    }

    this.getListOfMatches = function () {
        var result = [];
        if (!this.isBinaryMatching) {
            for (var i = 0; i < this.mergedMapping.maps.length; i++) {
                var map = this.mergedMapping.maps[i];
                var match = {name: this.getNodeLabel(map.modelIDs[0], map.nodeIDs[0]), color: this.getColorForMergedMatch(map), matchID: map.matchID, mappingID: this.mergedMapping.id};
                result.push(match);
            }
            return result;
        } else {
            var matching = this.getMatchingBetweenEPCS(this.leftEPCViz, this.rightEPCViz);
            if (matching !== undefined) {
                var id = matching.id;
                for (var j = 0; j < this.mappings.length; j++) {
                    if (this.mappings[j].id == id) {
                        for (var i = 0; i < this.mappings[j].maps.length; i++) {
                            var map = this.mappings[j].maps[i];
                            var match = {name: this.getNodeLabel(map.modelIDs[0], map.nodeIDs[0]), color: this.colorsMatches[id][map.matchID], matchID: map.matchID, mappingID: this.mappings[j].id};
                            result.push(match);
                        }
                    }
                }
            }
            return result;
        }

    }

    this.getTimeString = function () {
        return new Date().toLocaleString();
    }

    this.undo = function () {
        if (!((this.historyPos - 1) >= 0 && (this.historyPos - 1) < this.states.length)) {
            return;
        }
        this.historyPos--;
        if (this.isBinaryMatching) {
            this.mappings = this.states[this.historyPos];
            this.showMatching(false);
        } else {
            this.mergedMapping = this.states[this.historyPos];
            this.showMatching(false);
        }
    }

    this.redo = function () {
        if (!((this.historyPos + 1) >= 0 && (this.historyPos + 1) < this.states.length)) {
            return;
        }
        this.historyPos++;
        if (this.isBinaryMatching) {
            this.mappings = this.states[this.historyPos];
            this.showMatching(false);
        } else {
            this.mergedMapping = this.states[this.historyPos];
            this.showMatching(false);
        }

    }

    this.addSelectedNodesToMatch = function (context) {
        var action = "addSelectedNodesToMatch";
        var innvocationObject = "contextMenu";
        var numOfMappings = 0;
        var numOfMatches = 0;
        if (context !== undefined) {
            innvocationObject = context;
        }
        var mappingID = "mergedMapping";
        if (this.isBinaryMatching) {
            if (this.currentMapping !== undefined) {
                mappingID = this.currentMapping.id;
            }
        }
        if (this.isBinaryMatching) {
            if (this.mappings !== undefined) {
                numOfMappings = this.mappings.length;
                for (var mapNum = 0; mapNum < this.mappings.length; mapNum++) {
                    numOfMatches = numOfMatches + this.mappings[mapNum].maps.length;
                }
            }
        } else {
            numOfMappings = 1;
            if (this.mergedMapping === undefined) {
                numOfMatches = 0;
            } else {
                numOfMatches = this.mergedMapping.maps.length;
            }

        }
        var error = "";
        var status = "started";
        this.log.push({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, NumberOfNodes: this.selectedNodes.length});
        var status = "finished";

        this.showMatching(true);

        var length = this.selectedNodes.length;
        if (length < 2) {
            var error = "selected less than two nodes";
            this.log.push({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, NumberOfNodes: this.selectedNodes.length});
            alert("You have to select at least two nodes!");
            return;
        }
        if (!this.haveSelectedNodesAtLeatsTwoDifferentModels(this.selectedNodes)) {
            var error = "selected less than two models";
            this.log.push({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, NumberOfNodes: this.selectedNodes.length});
            alert("You have to select at least two nodes from different models!");
            return;
        }

        var insertedMatchIDForLog = "";

        var b2 = this.isBinaryMatching;
        var b = this.potentialMatchResultsInNary(this.selectedNodes);
        var b3 = b2 && b;

        if (b3) {
            if (this.getNumberOfMatchings() > 1) {
                alert("Match is no binary Match!");
                var error = "user tried to add a n-ary match to a binary matching";
                this.log.push({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, NumberOfNodes: this.selectedNodes.length});
                return;
            } else {
                var conf = confirm("Matching will be no binary Matching anymore!");
                if (!conf) {
                    var error = "user doesn't want n-ary Matching";
                    this.log.push({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, NumberOfNodes: this.selectedNodes.length});
                    return;
                }
                this.isBinaryMatching = false;
            }
        }

        b2 = this.isOneToOneMatching;
        b = this.potentialMatchResultsInNtoM(this.selectedNodes);
        b3 = b2 && b;

        if (b3) {
            var conf = confirm("Matching will be no 1:1 Matching anymore!");
            if (!conf) {
                var error = "user doesn't want n:m Matching";
                this.log.push({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, NumberOfNodes: this.selectedNodes.length});
                return;
            }
            this.isOneToOneMatching = false;
        }

        var newMatch;

        var arrayNodeIDs = [];
        var arrayModelIDs = [];
        var modelSet = new Set();

        for (var nodeNum = 0; nodeNum < this.selectedNodes.length; nodeNum++) {
            arrayNodeIDs.push(this.selectedNodes[nodeNum].nodeID);
            arrayModelIDs.push(this.selectedNodes[nodeNum].modelID);
            modelSet.add(this.selectedNodes[nodeNum].modelID);
        }

        if (modelSet.size !== 2 && this.isBinaryMatching) {
            alert("Number of affected Models !== 2");
            return;
        }

        this.unsavedChanges = true;
        var modelsForMatchArray = Array.from(modelSet);

        newMatch = {"matchID": 0, "nodeIDs": arrayNodeIDs, "modelIDs": arrayModelIDs, "status": "OPEN", "interpretation": "SIMILAR", "value": 1, "refEpcID": null};

        if (this.mappings === undefined) {
            this.mappings = [];
        }

        if (this.isBinaryMatching) {
            var searchedMapping = this.getBinaryMatchingBetweenModelsMappings(this.mappings, modelsForMatchArray[0], modelsForMatchArray[1]);
            if (searchedMapping === undefined) {
                this.lastMatchingID++;
                var models = [];
                models[0] = modelsForMatchArray[0];
                models[1] = modelsForMatchArray[1];
                var mapping = {"filename": "newFileName", "id": this.lastMatchingID, "name": models[0] + " - " + models[1], "maps": [], "models": models};
                this.mappings.push(mapping);
                searchedMapping = mapping;
                this.currentMapping = mapping;
            } else {
                var lastID = -1;
                for (var i = 0; i < searchedMapping.maps.length; i++) {
                    var id = searchedMapping.maps[i].matchID;
                    if (id > lastID) {
                        lastID = id;
                    }
                }
                lastID++;
                this.isMatchVisible[searchedMapping.id][lastID] = true;
                newMatch.matchID = lastID;
                insertedMatchIDForLog = lastID;
            }
            for (var matchNodeNum = 0; matchNodeNum < arrayNodeIDs.length; matchNodeNum++) {
                var code = arrayNodeIDs[matchNodeNum] + " _ " + arrayModelIDs[matchNodeNum];
                if (this.matchedNodesPerMatching[searchedMapping.id] !== undefined) {
                    if (this.matchedNodesPerMatching[searchedMapping.id][code] === undefined) {
                        this.matchedNodesPerMatching[searchedMapping.id][code] = 1;
                    } else {
                        this.matchedNodesPerMatching[searchedMapping.id][code] = this.matchedNodesPerMatching[searchedMapping.id][code] + 1;
                    }
                } else {
                    this.matchedNodesPerMatching[searchedMapping.id] = [];
                    this.matchedNodesPerMatching[searchedMapping.id][code] = 1;

                }
            }

            if (!this.mappingContainsMatch(searchedMapping, newMatch)) {
                searchedMapping.maps.push(newMatch);
                this.showMatch(this.lastMatchingID, newMatch, false);
                this.addMatchToMatchesListStringConcatenation(newMatch);
            } else {
                var conf = confirm("The match is already contained in the matching. It will be added one more time");
                if (conf) {
                    searchedMapping.maps.push(newMatch);
                    this.showMatch(this.lastMatchingID, newMatch, false);
                    this.addMatchToMatchesListStringConcatenation(newMatch);
                }
            }
        } else {
            var searchedMapping = this.mergedMapping;
            if (searchedMapping === undefined) {
                var models = modelsForMatchArray;
                var mapping = {"filename": "newFileName", "id": this.lastMatchingID, "name": "n-ary", "maps": [], "models": this.oldModels};
                this.mergedMapping = mapping;
                searchedMapping = mapping;
                this.currentMapping = mapping;
            } else {
                var models = modelsForMatchArray;
                var modelsSet = new Set();
                for (var c = 0; c < models.length; c++) {
                    modelsSet.add(models[c]);
                }
                for (var c = 0; c < this.mergedMapping.models.length; c++) {
                    modelsSet.add(this.mergedMapping.models[c]);
                }
                searchedMapping.models = this.oldModels; //Array.from(modelsSet);
                var lastID = -1;
                for (var i = 0; i < searchedMapping.maps.length; i++) {
                    var id = searchedMapping.maps[i].matchID;
                    if (id > lastID) {
                        lastID = id;
                    }
                }
                lastID++;
                this.mergedMatchVisibilityList[lastID] = true;
                newMatch.matchID = lastID;
                insertedMatchIDForLog = lastID;
            }
            for (var matchNodeNum = 0; matchNodeNum < arrayNodeIDs.length; matchNodeNum++) {
                var code = arrayNodeIDs[matchNodeNum] + " _ " + arrayModelIDs[matchNodeNum];
                if (this.matchedNodesPerMatching[searchedMapping.id] !== undefined) {
                    if (this.matchedNodesPerMatching[searchedMapping.id][code] === undefined) {
                        this.matchedNodesPerMatching[searchedMapping.id][code] = 1;
                    } else {
                        this.matchedNodesPerMatching[searchedMapping.id][code] = this.matchedNodesPerMatching[searchedMapping.id][code] + 1;
                    }
                } else {
                    this.matchedNodesPerMatching[searchedMapping.id] = [];
                    this.matchedNodesPerMatching[searchedMapping.id][code] = 1;

                }
            }

            if (!this.mappingContainsMatch(searchedMapping, newMatch)) {
                searchedMapping.maps.push(newMatch);
            } else {
                var conf = confirm("The match is already contained in the matching. It will be added one more time");
                if (conf) {
                    searchedMapping.maps.push(newMatch);
                }
            }
        }


        if (this.isBinaryMatching) {

        } else {

            this.showMatching(false);
        }



        while (this.selectedNodes.length > 0) {
            var selectedNode = this.selectedNodes[0];
            selectedNode.vizEPC.unselectAllNodes();
        }

        var error = "";
        var numOfMappings = 0;
        var numOfMatches = 0;
        if (this.isBinaryMatching) {
            if (this.mappings !== undefined) {
                numOfMappings = this.mappings.length;
                for (var mapNum = 0; mapNum < this.mappings.length; mapNum++) {
                    numOfMatches = numOfMatches + this.mappings[mapNum].maps.length;
                }
            }
        } else {
            numOfMappings = 1;
            numOfMatches = this.mergedMapping.maps.length;
        }

        this.log.push({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, MatchID: insertedMatchIDForLog, NumberOfNodes: this.selectedNodes.length});

        this.conditionsForRDFfullfilled = true; //this.isBinaryMatching && this.isOneToOneMatching && (this.getNumberOfMatchings() === 1);
        if (this.conditionsForRDFfullfilled) {
            $('#rdfOption').removeAttr('disabled');
        } else {
            $('#rdfOption').attr('disabled', 'disabled');
            $('#DATA_FORMAT').val("xml");
        }

        var mappingForState = this.mappings;
        if (!this.isBinaryMatching) {
            mappingForState = this.mergedMapping;
        }


        this.states.push(JSON.parse(JSON.stringify(mappingForState)));

        if (this.states.length > this.historyLimit) {
            this.states.splice(0, 1);
        }
        this.historyPos = this.states.length - 1;

    }

    this.handleVisibilitySelection = function (isVisible, matchID, oldThis) {
        var action = "changeVisibility to " + isVisible;
        var innvocationObject = "MatchesList";
        var numOfMappings = 0;
        var numOfMatches = 0;
        var mappingID = "mergedMapping";
        if (oldThis.isBinaryMatching) {
            if (oldThis.currentMapping !== undefined) {
                mappingID = oldThis.currentMapping;
            }
        }
        if (oldThis.isBinaryMatching) {
            if (oldThis.mappings !== undefined) {
                numOfMappings = oldThis.mappings.length;
                for (var mapNum = 0; mapNum < oldThis.mappings.length; mapNum++) {
                    numOfMatches = numOfMatches + oldThis.mappings[mapNum].maps.length;
                }
            }
        } else {
            numOfMappings = 1;
            numOfMatches = oldThis.mergedMapping.maps.length;
        }
        var error = "";
        var status = "started";
        oldThis.log.push({Timestamp: oldThis.getTimeString(), Action: action, InMatchViz: true, IsBinary: oldThis.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, MatchingID: matchID});
        var status = "finished";

        var mapping = oldThis.mergedMapping;
        if (oldThis.isBinaryMatching) {
            mapping = oldThis.getMatchingBetweenEPCS(oldThis.leftEPCViz, oldThis.rightEPCViz);
        }
        if (isVisible) {
            oldThis.showMatch(mapping.id, matchID, false);
        } else {
            oldThis.showMatch(mapping.id, matchID, true);
        }
        oldThis.log.push({Timestamp: oldThis.getTimeString(), Action: action, InMatchViz: true, IsBinary: oldThis.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, MatchingID: matchID});

    }

    this.createTable = function () {
        if (this.currentMapping !== undefined) {
            for (var i = 0; i < this.currentMapping.maps.length; i++) {
                this.addMatchToMatchesListStringConcatenation(this.currentMapping.maps[i]);
            }
        }
    }

    this.hasUnsavedChanges = function () {
        if (this.unsavedChanges) {
            return true;
        }
        var table = document.getElementById('matchesTable');
        if (table === undefined) {
            return false;
        }
        if (table.children.length === 0) {
            return false;
        }
        var tbody = table.children[0];
        for (var i = 0; i < tbody.children.length - 1; i++) {
            var row = tbody.children[i];
            var id = row.id;

            var value = document.getElementById('inputValue' + id).value;
            var status = document.getElementById('statusSelection' + id).value;
            var type = document.getElementById('typeSelection' + id).value;

            var match = this.getMatching(parseInt(id));
            if (match !== undefined) {
                if (match.status !== status) {
                    return true;
                }
                if (match.value != value) {
                    return true;
                }
                if (match.interpretation !== type) {
                    return true;
                }
            } else {
                console.log("Match with ID " + id + " was not found!");
            }

            // skip details
            i++;
        }
        return false;
    }

    this.changeLayout = function () {
        $.ajax({
            method: "POST",
            url: "classes/layoutmodel.php",
            data: {
            }
        })
                .done(function (msg) {
                    var objs = JSON.parse(msg);





                    alert("Received");

                });
    }

    this.saveChanges = function (filename, fileType, mappingType) {
        var action = "save";
        var innvocationObject = "Button";
        var numOfMappings = 0;
        var numOfMatches = 0;
        if (this.isBinaryMatching) {
            if (this.mappings !== undefined) {
                numOfMappings = this.mappings.length;
                for (var mapNum = 0; mapNum < this.mappings.length; mapNum++) {
                    numOfMatches = numOfMatches + this.mappings[mapNum].maps.length;
                }
            }
        } else {
            numOfMappings = 1;
            numOfMatches = this.mergedMapping.maps.length;
        }
        var error = "";
        var status = "started";
        this.log.push({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, Status: status, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches});
        var status = "finished";

        var table = document.getElementById('matchesTable');
        var tbody = table.children[0];
        for (var i = 0; i < tbody.children.length - 1; i++) {
            var row = tbody.children[i];
            var id = row.id;

            var value = document.getElementById('inputValue' + id).value;
            var status = document.getElementById('statusSelection' + id).value;
            var type = document.getElementById('typeSelection' + id).value;

            if (!this.isBinaryMatching) {
                var match = this.getMatchingNotByID(this.currentMapping, parseInt(id));
                if (match !== undefined) {
                    match.status = status;
                    match.value = value;
                    match.interpretation = type;
                }
            } else {

                var match = this.getMatching(this.currentMapping.id, parseInt(id));
                if (match !== undefined) {
                    match.status = status;
                    match.value = value;
                    match.interpretation = type;
                }
            }

            // skip details
            i++;
        }


        oldThis = this;
        exportMatching = undefined;
        if (mappingType === "nary" || this.isBinaryMatching === false) {
            exportMatching = [];
            if (fileType === "rdf") {
                exportMatching = this.getSplitMappings(this.mergedMapping);
            } else {
                exportMatching[0] = this.mergedMapping;
            }
        } else {
            if (fileType === "rdf") {
                if (!this.areAllMappingsOneToOne()) {
                    exportMatching = this.getMatchingsWithSplitMatches(this.mappings);
                } else {
                    exportMatching = this.mappings;
                }
            } else {
                exportMatching = this.mappings;
            }

        }


        $.ajax({
            method: "POST",
            url: "classes/getMatching.php",
            data: {action: "update",
                fileName: filename,
                fileType: fileType,
                mappingType: mappingType,
                matching: JSON.stringify(exportMatching)
            }
        })
                .done(function (msg) {
                    alert("Saved");
                    oldThis.unsavedChanges = false;
                    var status = "finished";
                    oldThis.log.push({Timestamp: oldThis.getTimeString(), Action: action, InMatchViz: true, IsBinary: oldThis.isBinaryMatching, Status: status, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches});
                });

    }


    this.getHTMLCodeForMatchMetrics = function () {
        var code = '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">' +
                ' <div class="panel panel-default">' +
                '					    <div class="panel-heading" role="tab" id="headingOne">' +
                '<h4 class="panel-title">' +
                '<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">' +
                'Some metrics' +
                '</a>' +
                ' </h4>' +
                ' </div>' +
                ' <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">' +
                ' <table class="table table-hover">' +
                ' <tr>' +
                '<td>#matchings</td>' +
                '<td align="right" id="numMatchings"><b>' +
                this.getNumberOfMatchings() +
                '</b></td>' +
                ' </tr>' +
                ' <tr>' +
                '<td>#matches</td>' +
                '<td align="right" id="numMatches"><b>' +
                this.getNumberOfMatches() +
                '</b></td>' +
                ' </tr>' +
                '</table>' +
                ' </div>' +
                '</div>' +
                '</div>';

        return code;
    }




    this.deleteMatch = function (matchID, context) {
        var action = "deleteMatch";
        var innvocationObject = "contextMenuMatchesList";
        var numOfMappings = 0;
        var numOfMatches = 0;
        var mappingID = "mergedMapping";
        if (this.isBinaryMatching) {
            if (this.currentMapping !== undefined) {
                mappingID = this.currentMapping.id;
            }
        }
        if (context !== undefined) {
            innvocationObject = context;
        }
        if (this.isBinaryMatching) {
            if (this.mappings !== undefined) {
                numOfMappings = this.mappings.length;
                for (var mapNum = 0; mapNum < this.mappings.length; mapNum++) {
                    numOfMatches = numOfMatches + this.mappings[mapNum].maps.length;
                }
            }
        } else {
            numOfMappings = 1;
            numOfMatches = this.mergedMapping.maps.length;
        }
        var error = "";
        var status = "started";
        this.log.push({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, MatchID: matchID});
        var status = "finished";

        this.showMatching(true);

        this.unsavedChanges = true;
        this.removeMatchInViz(matchID);
        if (!this.isBinaryMatching) {
            for (var i = 0; i < this.mergedMapping.maps.length; i++) {
                if (this.mergedMapping.maps[i].matchID == matchID) {
                    this.mergedMapping.maps.splice(i, 1);
                }
            }

        } else {

            for (var i = 0; i < this.currentMapping.maps.length; i++) {

                if (this.currentMapping.maps[i].matchID == matchID) {
                    for (var matchNodeNum = 0; matchNodeNum < this.currentMapping.maps[i].nodeIDs.length; matchNodeNum++) {
                        var code = this.currentMapping.maps[i].nodeIDs[matchNodeNum] + " _ " + this.currentMapping.maps[i].modelIDs[matchNodeNum];
                        if (this.matchedNodesPerMatching[this.currentMapping.id] !== undefined) {
                            if (this.matchedNodesPerMatching[this.currentMapping.id][code] === undefined) {

                            } else {
                                this.matchedNodesPerMatching[this.currentMapping.id][code] = this.matchedNodesPerMatching[this.currentMapping.id][code] - 1;
                            }
                        } else {

                        }

                    }

                    this.currentMapping.maps.splice(i, 1);
                    break;
                }
            }

        }

        if (this.getMatchingBetweenEPCS(this.rightEPCViz, this.leftEPCViz) !== undefined) {
            this.currentMapping = this.getMatchingBetweenEPCS(this.rightEPCViz, this.leftEPCViz);
        } else {
            this.currentMapping = undefined;
        }


        if (!this.isBinaryMatching) {
            this.currentMapping = this.mergedMapping;
        }
        this.unsavedChanges = true;

        document.getElementById("matchMetrics").innerHTML = this.getHTMLCodeForMatchMetrics();
        $('#' + matchID).remove();
        $('#details_' + matchID).remove();

        this.showMatching(false);

        if (this.isBinaryMatching) {
            if (this.mappings !== undefined) {
                numOfMappings = this.mappings.length;
                for (var mapNum = 0; mapNum < this.mappings.length; mapNum++) {
                    numOfMatches = numOfMatches + this.mappings[mapNum].maps.length;
                }
            }
        } else {
            numOfMappings = 1;
            numOfMatches = this.mergedMapping.maps.length;
        }
        this.log.push({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, MatchID: matchID});
        this.updateModalIfNecessary();

        var mappingForState = this.mappings;
        if (!this.isBinaryMatching) {
            mappingForState = this.mergedMapping;
        }


        this.states.push(JSON.parse(JSON.stringify(mappingForState)));

        if (this.states.length > this.historyLimit) {
            this.states.splice(0, 1);
        }
        this.historyPos = this.states.length - 1;
    }


    this.addNodeToMatch = function (node, matchID, mappingID) {
        var action = "addNodeToMatch";
        var innvocationObject = "contextMenuNode";
        var numOfMappings = 0;
        var numOfMatches = 0;
        var mappingIDForLog = "mergedMapping";
        if (this.isBinaryMatching) {
            mappingIDForLog = mappingID;
        }
        if (this.isBinaryMatching) {
            if (this.mappings !== undefined) {
                numOfMappings = this.mappings.length;
                for (var mapNum = 0; mapNum < this.mappings.length; mapNum++) {
                    numOfMatches = numOfMatches + this.mappings[mapNum].maps.length;
                }
            }
        } else {
            numOfMappings = 1;
            numOfMatches = this.mergedMapping.maps.length;
        }
        var error = "";
        var status = "started";
        this.log.push({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingIDForLog, MatchingID: matchID});
        var status = "finished";

        var code = node.nodeID + " _ " + node.modelID;

        var concreteMatch = undefined;
        if (this.isBinaryMatching) {
            concreteMatch = this.getMatching(mappingID, matchID);
        } else {
            concreteMatch = this.getMatchingNotByID(this.mergedMapping, matchID);
        }
        var b2 = this.isBinaryMatching;
        var b = this.potentialMatchResultsInNaryForGivenMatch(this.selectedNodes, concreteMatch);
        var b3 = b2 && b;

        if (b3) {
            if (this.getNumberOfMatchings() > 1) {
                alert("Match is no binary Match!");
                var error = "user tried to add a n-ary match to a binary matching";
                this.log.push({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, NumberOfNodes: this.selectedNodes.length});
                return;
            } else {
                var conf = confirm("Matching will be no binary Matching anymore!");
                if (!conf) {
                    var error = "user doesn't want n-ary Matching";
                    this.log.push({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, NumberOfNodes: this.selectedNodes.length});
                    return;
                }
                this.isBinaryMatching = false;
            }
        }

        b2 = this.isOneToOneMatching;
        b = this.potentialMatchResultsInNtoMForGivenMatch(this.selectedNodes, concreteMatch);
        b3 = b2 && b;

        if (b3) {
            var conf = confirm("Matching will be no 1:1 Matching anymore!");
            if (!conf) {
                var error = "user doesn't want n:m Matching";
                this.log.push({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, NumberOfNodes: this.selectedNodes.length});
                return;
            }
            this.isOneToOneMatching = false;
        }


        this.showMatching(true);

        if (this.isBinaryMatching) {
            for (var i = 0; i < this.mappings.length; i++) {
                if (this.mappings[i].id == mappingID) {
                    for (var j = 0; j < this.mappings[i].maps.length; j++) {
                        if (this.mappings[i].maps[j].matchID == matchID) {
                            this.mappings[i].maps[j].modelIDs.push(node.modelID);
                            this.mappings[i].maps[j].nodeIDs.push(node.nodeID);
//                            this.calculateMergedMatching();
                            this.showMatching();
                            this.unsavedChanges = true;

                        }
                    }

                }
            }

        } else {

            for (var j = 0; j < this.mergedMapping.maps.length; j++) {
                if (this.mergedMapping.maps[j].matchID == matchID) {
                    this.mergedMapping.maps[j].modelIDs.push(node.modelID);
                    this.mergedMapping.maps[j].nodeIDs.push(node.nodeID);
//                    this.calculateMergedMatching();
                    this.showMatching();
                    this.unsavedChanges = true;

                }
            }



        }

        this.showMatching(false);
        this.log.push({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, MatchingID: matchID});

        var mappingForState = this.mappings;
        if (!this.isBinaryMatching) {
            mappingForState = this.mergedMapping;
        }


        this.states.push(JSON.parse(JSON.stringify(mappingForState)));

        if (this.states.length > this.historyLimit) {
            this.states.splice(0, 1);
        }
        this.historyPos = this.states.length - 1;
    }

    this.deleteMatchContainingDefinedNode = function (node, context, matchID, mappingID) {
        this.deleteMatch(matchID, context);
//        this.calculateMergedMatching();
        this.showMatching(false);
        this.updateModalIfNecessary();
    }


    this.deleteMatchContainingNode = function (node, context) {
        var match = this.getMatchingContainingNode(node);
        this.deleteMatch(match.match.matchID, context);
//        this.calculateMergedMatching();
        this.showMatching(false);
        this.updateModalIfNecessary();
    }


    this.deleteNodeFromDefinedMatch = function (node, matchID, mappingID) {
        var action = "deleteNodeFromMatch";
        var innvocationObject = "contextMenuNode";
        var numOfMappings = 0;
        var numOfMatches = 0;
        if (this.isBinaryMatching) {
            if (this.mappings !== undefined) {
                numOfMappings = this.mappings.length;
                for (var mapNum = 0; mapNum < this.mappings.length; mapNum++) {
                    numOfMatches = numOfMatches + this.mappings[mapNum].maps.length;
                }
            }
        } else {
            numOfMappings = 1;
            numOfMatches = this.mergedMapping.maps.length;
        }
        var error = "";
        var status = "started";
        var logEntry = ({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, MatchID: matchID});
        var status = "finished";

        this.showMatching(true);

        if (this.isBinaryMatching) {

            var matching = undefined;
            if (mappingID === undefined) {
                matching = this.currentMapping;
            } else {
                matching = this.getMatching(mappingID, matchID);
            }


            var nodeNum = 0;
            while (!deleted && nodeNum < matching.nodeIDs.length) {
                var d1 = matching.nodeIDs[nodeNum];
                var d2 = matching.modelIDs[nodeNum];
                if (matching.nodeIDs[nodeNum] == node.nodeID && matching.modelIDs[nodeNum] === node.modelID) {
                    logEntry.MappingID = mappingID;
                    logEntry.MatchID = matching.matchID;
                    this.log.push(logEntry);
                    var code = node.nodeID + " _ " + node.modelID;
                    if (this.matchedNodesPerMatching[mappingID] !== undefined) {
                        if (this.matchedNodesPerMatching[mappingID][code] === undefined) {
                        } else {
                            this.matchedNodesPerMatching[mappingID][code] = this.matchedNodesPerMatching[mappingID][code] - 1;
                        }
                    }
                    matching.nodeIDs.splice(nodeNum, 1);
                    matching.modelIDs.splice(nodeNum, 1);
                    deleted = true;
                }
                nodeNum++;
            }






        } else {

            for (var mappingNumber = 0; mappingNumber < this.mappings.length; mappingNumber++) {
                var mapping = this.mappings[mappingNumber];
                for (var k = 0; k < mapping.maps.length; k++) {
                    var nodeSet = new Set();
                    for (var nodeNum = 0; nodeNum < mapping.maps[k].nodeIDs.length; nodeNum++) {
                        var code = mapping.maps[k].nodeIDs[nodeNum] + " _ " + mapping.maps[k].modelIDs[nodeNum];
                        nodeSet.add(code);
                    }
                    var nodeString = node.nodeID + " _ " + node.modelID;
                    if (nodeSet.has(nodeString)) {
                        var deleted = false;
                        var nodeNum = 0;

                        while (!deleted && nodeNum < mapping.maps[k].nodeIDs.length) {
                            var d1 = mapping.maps[k].nodeIDs[nodeNum];
                            var d2 = mapping.maps[k].modelIDs[nodeNum];
                            if (mapping.maps[k].nodeIDs[nodeNum] == node.nodeID && mapping.maps[k].modelIDs[nodeNum] === node.modelID) {
                                logEntry.MappingID = mapping.id;
                                logEntry.MatchID = mapping.maps[k].matchID;
//                            this.logs.push(logEntry);
                                var code = node.nodeID + " _ " + node.modelID;
                                if (this.matchedNodesPerMatching[mapping.id] !== undefined) {
                                    if (this.matchedNodesPerMatching[mapping.id][code] === undefined) {
                                    } else {
                                        this.matchedNodesPerMatching[mapping.id][code] = this.matchedNodesPerMatching[mapping.id][code] - 1;
                                    }
                                }
                                mapping.maps[k].nodeIDs.splice(nodeNum, 1);
                                mapping.maps[k].modelIDs.splice(nodeNum, 1);
                                deleted = true;
                            }
                            nodeNum++;
                        }


                    }
                }
            }


        }
        this.unsavedChanges = true;
//        this.calculateMergedMatching();
        this.showMatching(false);
        this.log.push({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, MatchID: matchID});
        this.updateModalIfNecessary();

        var mappingForState = this.mappings;
        if (!this.isBinaryMatching) {
            mappingForState = this.mergedMapping;
        }


        this.states.push(JSON.parse(JSON.stringify(mappingForState)));

        if (this.states.length > this.historyLimit) {
            this.states.splice(0, 1);
        }
        this.historyPos = this.states.length - 1;
    }



    this.deleteNodeFromMatch = function (node) {
        var action = "delteNodeFromMatch";
        var innvocationObject = "contextMenuNode";
        var numOfMappings = 0;
        var numOfMatches = 0;
        var mappingID = "mergedMapping";
        var matchID = "";
        if (this.isBinaryMatching) {
            if (this.mappings !== undefined) {
                numOfMappings = this.mappings.length;
                for (var mapNum = 0; mapNum < this.mappings.length; mapNum++) {
                    numOfMatches = numOfMatches + this.mappings[mapNum].maps.length;
                }
            }
        } else {
            numOfMappings = 1;
            numOfMatches = this.mergedMapping.maps.length;
        }
        var error = "";
        var status = "started";
        var logEntry = ({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, MatchID: matchID});
        var status = "finished";

        this.showMatching(true);

        if (this.isBinaryMatching) {
            var mapping = this.getMatchingBetweenEPCS(this.leftEPCViz, this.rightEPCViz);
            for (var k = 0; k < mapping.maps.length; k++) {
                var nodeSet = new Set();
                for (var nodeNum = 0; nodeNum < mapping.maps[k].nodeIDs.length; nodeNum++) {
                    var code = mapping.maps[k].nodeIDs[nodeNum] + " _ " + mapping.maps[k].modelIDs[nodeNum];
                    nodeSet.add(code);
                }
                var nodeString = node.nodeID + " _ " + node.modelID;
                if (nodeSet.has(nodeString)) {
                    var deleted = false;
                    var nodeNum = 0;

                    while (!deleted && nodeNum < mapping.maps[k].nodeIDs.length) {
                        var d1 = mapping.maps[k].nodeIDs[nodeNum];
                        var d2 = mapping.maps[k].modelIDs[nodeNum];
                        if (mapping.maps[k].nodeIDs[nodeNum] == node.nodeID && mapping.maps[k].modelIDs[nodeNum] === node.modelID) {
                            logEntry.MappingID = mapping.id;
                            logEntry.MatchID = mapping.maps[k].matchID;
                            this.log.push(logEntry);
                            var code = node.nodeID + " _ " + node.modelID;
                            if (this.matchedNodesPerMatching[mapping.id] !== undefined) {
                                if (this.matchedNodesPerMatching[mapping.id][code] === undefined) {
                                } else {
                                    this.matchedNodesPerMatching[mapping.id][code] = this.matchedNodesPerMatching[mapping.id][code] - 1;
                                }
                            }
                            mapping.maps[k].nodeIDs.splice(nodeNum, 1);
                            mapping.maps[k].modelIDs.splice(nodeNum, 1);
                            deleted = true;
                        }
                        nodeNum++;
                    }


                }
            }


        } else {


            var mapping = this.mergedMapping;
            for (var k = 0; k < mapping.maps.length; k++) {
                var nodeSet = new Set();
                for (var nodeNum = 0; nodeNum < mapping.maps[k].nodeIDs.length; nodeNum++) {
                    var code = mapping.maps[k].nodeIDs[nodeNum] + " _ " + mapping.maps[k].modelIDs[nodeNum];
                    nodeSet.add(code);
                }
                var nodeString = node.nodeID + " _ " + node.modelID;
                if (nodeSet.has(nodeString)) {
                    var deleted = false;
                    var nodeNum = 0;

                    while (!deleted && nodeNum < mapping.maps[k].nodeIDs.length) {
                        var d1 = mapping.maps[k].nodeIDs[nodeNum];
                        var d2 = mapping.maps[k].modelIDs[nodeNum];
                        if (mapping.maps[k].nodeIDs[nodeNum] == node.nodeID && mapping.maps[k].modelIDs[nodeNum] === node.modelID) {
                            logEntry.MappingID = mapping.id;
                            logEntry.MatchID = mapping.maps[k].matchID;
//                            this.logs.push(logEntry);
                            var code = node.nodeID + " _ " + node.modelID;
                            if (this.matchedNodesPerMatching[mapping.id] !== undefined) {
                                if (this.matchedNodesPerMatching[mapping.id][code] === undefined) {
                                } else {
                                    this.matchedNodesPerMatching[mapping.id][code] = this.matchedNodesPerMatching[mapping.id][code] - 1;
                                }
                            }
                            mapping.maps[k].nodeIDs.splice(nodeNum, 1);
                            mapping.maps[k].modelIDs.splice(nodeNum, 1);
                            deleted = true;
                        }
                        nodeNum++;
                    }


                }
            }



        }
        this.unsavedChanges = true;
//        this.calculateMergedMatching();
        this.showMatching(false);
        this.log.push({Timestamp: this.getTimeString(), Action: action, InMatchViz: true, IsBinary: this.isBinaryMatching, InvocationObject: innvocationObject, Error: error, numOfTotalMappings: numOfMappings, numOfTotalMatches: numOfMatches, MappingID: mappingID, MatchID: matchID});
        this.updateModalIfNecessary();

        var mappingForState = this.mappings;
        if (!this.isBinaryMatching) {
            mappingForState = this.mergedMapping;
        }


        this.states.push(JSON.parse(JSON.stringify(mappingForState)));

        if (this.states.length > this.historyLimit) {
            this.states.splice(0, 1);
        }
        this.historyPos = this.states.length - 1;
    }


    this.getMatchingsContainingNode = function (node) {

        var result = [];

        if (this.isBinaryMatching) {



            var mapping = this.getMatchingBetweenEPCS(this.leftEPCViz, this.rightEPCViz);
            if (mapping === undefined) {
                return undefined;
            }
            for (var k = 0; k < mapping.maps.length; k++) {
                var nodeSet = new Set();
                for (var nodeNum = 0; nodeNum < mapping.maps[k].nodeIDs.length; nodeNum++) {
                    var code = mapping.maps[k].nodeIDs[nodeNum] + " _ " + mapping.maps[k].modelIDs[nodeNum];
                    nodeSet.add(code);
                }
                var nodeString = node.nodeID + " _ " + node.modelID;
                if (nodeSet.has(nodeString)) {
                    var map = mapping.maps[k];
                    var match = {name: this.getNodeLabel(map.modelIDs[0], map.nodeIDs[0]), color: this.colorsMatches[mapping.id][map.matchID], matchID: map.matchID, mappingID: mapping.id};
                    result.push(match);
                }
            }
            return result;


        } else {


            var mapping = this.mergedMapping;
            for (var k = 0; k < mapping.maps.length; k++) {
                var nodeSet = new Set();
                for (var nodeNum = 0; nodeNum < mapping.maps[k].nodeIDs.length; nodeNum++) {
                    var code = mapping.maps[k].nodeIDs[nodeNum] + " _ " + mapping.maps[k].modelIDs[nodeNum];
                    nodeSet.add(code);
                }
                var nodeString = node.nodeID + " _ " + node.modelID;
                if (nodeSet.has(nodeString)) {
                    var map = this.mergedMapping.maps[k];
                    var match = {name: this.getNodeLabel(map.modelIDs[0], map.nodeIDs[0]), color: this.colorsMergedMatches[map.matchID], matchID: map.matchID, mappingID: this.mergedMapping.id};
                    result.push(match);
                }
            }

            return result;
        }
    }


    this.getMatchingContainingNode = function (node) {
        if (this.isBinaryMatching) {

            var mapping = this.getMatchingBetweenEPCS(this.leftEPCViz, this.rightEPCViz);
            for (var k = 0; k < mapping.maps.length; k++) {
                var nodeSet = new Set();
                for (var nodeNum = 0; nodeNum < mapping.maps[k].nodeIDs.length; nodeNum++) {
                    var code = mapping.maps[k].nodeIDs[nodeNum] + " _ " + mapping.maps[k].modelIDs[nodeNum];
                    nodeSet.add(code);
                }
                var nodeString = node.nodeID + " _ " + node.modelID;
                if (nodeSet.has(nodeString)) {
                    return {match: mapping.maps[k], mapping: mapping};
                }
            }

        } else {

            var nodeSet = new Set();
            var mapping = this.mergedMapping;
            for (var k = 0; k < mapping.maps.length; k++) {
                for (var nodeNum = 0; nodeNum < mapping.maps[k].nodeIDs.length; nodeNum++) {
                    var code = mapping.maps[k].nodeIDs[nodeNum] + " _ " + mapping.maps[k].modelIDs[nodeNum];
                    nodeSet.add(code);
                }
                var nodeString = node.nodeID + " _ " + node.modelID;
                if (nodeSet.has(nodeString)) {
                    return {match: mapping.maps[k], mapping: this.mergedMapping};
                }
            }


        }
    }

    //source: http://stackoverflow.com/questions/22784802/how-does-one-convert-an-object-into-csv-using-javascript
    this.getLogAsCSV = function (array) {
        // Returns a csv from an array of objects with
        // values separated by tabs and rows separated by newlines

        // Use first element to choose the keys and the order
        var keys = Object.keys(array[0]);

        var set = new Set();
        for (var n = 0; n < array.length; n++) {
            var keys = Object.keys(array[n]);
            for (var k = 0; k < keys.length; k++) {
                set.add(keys[k]);
            }
        }
        var keyArr = Array.from(set);

        // Build header
        var result = keyArr.join("\t") + "\n";

        // Add the rows
        array.forEach(function (obj) {
            keyArr.forEach(function (k, ix) {
                if (ix)
                    result += "\t";
                result += obj[k];
            });
            result += "\n";
        });

        return result;

    }

    this.deleteMatchNode = function (node, epcViz) {
        this.unsavedChanges = true;
        console.log("remove node " + node.label);

        $.ajax({
            method: "POST",
            url: "classes/getMatching.php",
            data: {action: "delete",
                matchingID: this.mapping.matchingID,
                nodeID: node.id
            }
        })
                .done(function (msg) {
                    epcViz.epc.setColor(node.id, null);
                    alert("Data Saved: " + msg);
                });
    }



    window.onbeforeunload = function (e) {

        var array = logFile;

        if (logFile === undefined || array.length === 0) {
            return;
        }

        // Use first element to choose the keys and the order
        var keys = Object.keys(array[0]);

        var set = new Set();
        for (var n = 0; n < array.length; n++) {
            var keys = Object.keys(array[n]);
            for (var k = 0; k < keys.length; k++) {
                set.add(keys[k]);
            }
        }
        var keyArr = Array.from(set);

        // Build header
        var result = keyArr.join(";") + "\n";

        // Add the rows
        array.forEach(function (obj) {
            keyArr.forEach(function (k, ix) {
                if (ix)
                    result += ";";
                result += obj[k];
            });
            result += "\n";
        });

        console.log("logged");

        finishedSavingLog = false;



        // save log
        $.ajax({
            method: "POST",
            async: false,
            url: "classes/saveLog.php",
            data: {log: result
            }
        })
                .done(function (msg) {
                    finishedSavingLog = true;

                });

        while (!finishedSavingLog) {

        }
        if (matchVizObject.hasUnsavedChanges()) {
            return "the page has unsaved changes!"
        }
        return;
    };

}

// source: http://stackoverflow.com/questions/5623838/rgb-to-hex-and-hex-to-rgb
function componentToHex(c) {
    var hex = c.toString(16);
    return hex.length == 1 ? "0" + hex : hex;
}

// source: http://stackoverflow.com/questions/5623838/rgb-to-hex-and-hex-to-rgb
function rgbToHex(rgb) {
    return "#" + componentToHex(rgb[0]) + componentToHex(rgb[1]) + componentToHex(rgb[2]);
}

