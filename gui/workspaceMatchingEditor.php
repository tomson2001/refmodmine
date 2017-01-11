<?php
$file = null;
$mappingFile = null;

$workspace = new WorkspaceEPML(true);
$workspaceData = $workspace->getAvailableData();

// both needed for calculation of needed EPCs
$neededEPCNamesSet = array();
$neededEPCs = array();

if (isset($_REQUEST['file'])) {
    $file = $_REQUEST["file"];
    $mappingFile = $workspace->loadAndGetMatchingFile($workspaceData->getDownloadLink($file), $workspaceData->getFileType($file));

    // calculate the needed EPCs
    foreach ($mappingFile->matchings as $mapping) {
        foreach ($mapping->models as $modelName) {
            $neededEPCNamesSet[$modelName] = 1;
        }
    }
    foreach (array_keys($neededEPCNamesSet) as $index => $model) {
        $epc = $workspace->getEPCByName($model);
        array_push($neededEPCs, $epc);
    }
}

// epcIDs of the models for COntaining and Available list
$neededEPCNums = array();
$availableEPCNums = array();

$epcs = array();
$visualizers = array();
$count = 1;
foreach ($workspace->epcs as $epcID => $epc) {
    array_push($epcs, $epc);
    $visualizers[$epc->getEPCName()] = new EPCVisualizer($epc, $count, null);
    if (in_array($epc, $neededEPCs)) {
        array_push($neededEPCNums, $epcID);
    } else {
        array_push($availableEPCNums, $epcID);
    }
    $count++;
}

if (sizeof($neededEPCNums) == 0 && $mappingFile == null) {
    $neededEPCNums = $availableEPCNums;
    $availableEPCNums = array();
}

$matchViz = new MatchingVisualizerMultiple($visualizers, $mappingFile, $epcs);
$jsMatchViz = $matchViz->generateVisJSCode();
$_SESSION['workspace'] = $workspace;
?>


<div class="modal fade" id="introModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="overflow-y: scroll; max-height:90%;  margin-top: 50px; margin-bottom:50px;">
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Matching-Editor: Help</h4>
            </div>
            <div class="modal-body">
                <div id="textModalWelcome">
                    <h1>Introduction</h1>
                    The Matching-Editor supports binary and n-ary matching files, 1:1 and
                    N:M matches.<br>
                    In the first step you have to decide whether you would like to create a
                    binary matching or a n-ary Matching.
                    <br><br>
                    <h1>Supported Tasks</h1>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>show match details</td>
                                <td>You can expand the details of a match by clicking the
                                    expand icon in the match details.</td>
                            </tr>
                            <tr>
                                <td>create a new match</td>
                                <td>To create a new match select the nodes, which should be contained in the match, and press "add Match".</td>
                            </tr>
                            <tr>
                                <td>delete a match</td>
                                <td>There are several possibilities to delete a match:<br>
                                    <ul>
                                        <li>click the trash symbol of the match, which should be
                                            deleted, in the matching list on the right side</li>
                                        <li>right click in the matching list on the right side to
                                            open a context-menu</li>
                                        <li>right click on an EPC-node contained in the match to open a context-menu and click "Delete whole Match"</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td>add a node to a match</td>
                                <td>Right click on the EPC-node to open a context-menu and click "Add Node to Match" and select the Match from the list.</td>
                            </tr>
                            <tr>
                                <td>delete a node from a match</td>
                                <td>There are several possibilities to delete a match:<br>
                                    <ul>
                                        <li>right click on an EPC-node contained in the match to open a context-menu and click "Delete Node from
                                            Match". If the selected node is part of multiple matches you have to
                                            select the corresponding match from a list</li>
                                        <li>right click in the list of containing nodes in the
                                            matching details to open a context-menu</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td>edit details of a match</td>
                                <td>You can edit the details of a match by expanding the
                                    matching details of a match.</td>
                            </tr>
                            <tr>
                                <td>add a process model to the matching</td>
                                <td>Process models, which are available in the workspace,
                                    can be added to a matching by Drag&amp;Drop. To add a model to the
                                    matching you can drag it to the list of containing models.</td>
                            </tr>
                            <tr>
                                <td>delete a process model from the matching</td>
                                <td>Process models can be deleted from a matching by
                                    Drag&amp;Drop. To remove a model you can drag it to the
                                    list of available models.</td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <h1>Supported Shortcuts</h1>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Shortcut</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>STRG + left click</td>
                                <td>It is possible to select multiple nodes by clicking on
                                    them while pressing STRG&nbsp;</td>
                            </tr>
                            <tr>
                                <td>ENTER</td>
                                <td>Adds the selected nodes to the matching</td>
                            </tr>
                            <tr>
                                <td>STRG + S</td>
                                <td>Opens the save window</td>
                            </tr>
                            <tr>
                                <td>STRG + Z</td>
                                <td>Undo</td>
                            </tr>
                            <tr>
                                <td>STRG + Y</td>
                                <td>Redo</td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <h1>Supported File-Formats</h1>
                    A list of the supported file formats in the RMMaaS can be found here: <a target="_blank" href="index.php?site=fileTypeSpecifications">File Type Specifications</a>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<!-- Modal -->
<div id="loaderConfigModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Loader Options</h4>
            </div>
            <div class="modal-body">

                <div id="matchingtypeDiv">
                    <input type="radio" id="matchingtypebinarybinary" name="matchingtype" value="binarybinary" checked="checked"> Load Matchings as binary 1:1 Matchings<br>  
                    <input type="radio" id="matchingtypebinarynary" name="matchingtype" value="binarynary"> Merge Matches to binary N:M Matches<br>
                    <input type="radio" id="matchingtypenarynary" name="matchingtype" value="narynary"> Merge Matchings to n-ary N:M Matches<br>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick='createView()'>OK</button>
            </div>
        </div>

    </div>
</div>


<!-- Modal for Configuration -->
<div id="configModal" class="modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 id="modelHeader" class="modal-title">Settings</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="LAYOUTER" class="col-sm-4 control-label">Layouter</label>
                        <div class="col-sm-6">
                            <select class="form-control" name="LAYOUTER" id="LAYOUTER">
                                <option id="rmmaas" value="rmmaas">RMMaaS</option>
                                
                                <?php 
                                // check whether layouter RMM jar exists
                                if ( !empty(Config::REFMOD_MINER_JAVA_PATH_WITH_FILENAME_LAYOUT) && file_exists(Config::REFMOD_MINER_JAVA_PATH_WITH_FILENAME_LAYOUT) ) {
									$disabled = "";
								} else {
									$disabled = "disabled=\"disabled\"";
								}
                                ?>
                                
                                <option id="rmm" value="rmm" <?php echo $disabled; ?>>RMM</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" onclick='setLayouter()'>save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Saving Dialog -->
<div id="saveModal" class="modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" method="post">
                <div class="modal-header alert alert-info">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 id="modelHeader" class="modal-title">Save</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">File Name</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="filename" value="Matching">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="DATA_FORMAT" class="col-sm-4 control-label">Data Format</label>
                        <div class="col-sm-6">
                            <select class="form-control" name="DATA_FORMAT" id="DATA_FORMAT" onchange="if (this.selectedIndex != undefined)
                                        updateWarning();">
                                <option id="xmlOption" value="xml">XML</option>
                                <option id="rdfOption" value="rdf">RDF</option>
                            </select>
                        </div>
                    </div>
                    <div id="warningRDF" class="form-group alert alert-warning" style="visibility: hidden;">
                        <label class="col-sm-4 control-label">Warning</label>
                        <div class="col-sm-6">RDF doesn't support the Match-Details STATUS and INTERPRETATION!</div>
                    </div>

                    <div id="warningRDF2" class="form-group alert alert-warning" style="visibility: hidden;">
                        <label class="col-sm-4 control-label">Warning</label>
                        <div class="col-sm-6">RDF doesn't support n-ary Matchings and Matchings containing N:M Matches.<br>
                            Thus the Matching will be exported as binary Matchings with 1:1 Matches!</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick='save()'>save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Choosing Matching Type Dialog -->
<div id="matchingTypeModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Choose Matching Type</h4>
            </div>
            <div class="modal-body">
                <p>Do you want to create a binary or a n-ary Matching?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" onclick='createBinaryMatching()'>binary</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" onclick='createNAryMatching()'>n-ary</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- Modal for EPC Viz -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div id="epcVizContent" class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 id="modelHeaderViz" class="modal-title">Model</h4>
            </div>
            <div id='EPC'></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="loadingDialog" role="dialog" class="modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Loading</h4>
            </div>
            <div class="modal-body">
                <p>Page is loading. Please Wait...</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content panel-danger">
            <div class="modal-header panel-heading">
                <h4 class="modal-title">Error: Models missing!</h4>
            </div>
            <div class="modal-body">
                The workspace doesn't contain all models included in the matching!<br>
                Please add the missing models to the workspace.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick='history.back()'>Go back to workspace</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="errorModalMissingNodes" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content panel-danger">
            <div class="modal-header panel-heading">
                <h4 class="modal-title">Error: Matching is corrupted!</h4>
            </div>
            <div class="modal-body">
                At least one Matching is corrupted!<br>
                Please check the file and repair the Matching.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick='history.back()'>Go back to workspace</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="warningModal" tabindex="-1" role="dialog"aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content panel-warning">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Warning: Reached maximal number of visualizations!</h4>
            </div>
            <div class="modal-body">
                It is not possible to add more visualizations to the Matching Editor than models are contained in the matching!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>



<div id="rowElement" style="width: 520px; margin: 0 auto;">
    <div style="float: left; width: 250px;">



        <!-- generate EPC Vizualisations for on model click -->
        <?php
        foreach ($workspace->epcs as $epcID => $epc) {
            $visualizer = new EPCVisualizer($epc, $epcID, null);
            echo $visualizer->generateVisJSCodeEPCDiv();
        }
        ?>

        <h2 data-toggle="tooltip" title="EPCs can be added and removed by Drag&Drop">Containing Models</h2>
        <ul id="containing_ul" style="min-height: 40px" class="list-group">
            <?php foreach ($neededEPCNums as $key => $epcID) { ?>
                <a href="#" data-toggle="modal" data-id=<?php echo $epcID; ?> data-target="#myModal" class="list-group-item"><small><?php echo $workspace->epcs[$epcID]->name; ?></small></a>
            <?php } ?>
        </ul>


        <h2 data-toggle="tooltip" title="EPCs can be added and removed by Drag&Drop">Available Models</h2>
        <ul id="available_ul" style="min-height: 40px" class="list-group">
            <?php foreach ($availableEPCNums as $key => $epcID) { ?>
                <a href="#" data-toggle="modal" data-id=<?php echo $epcID; ?> data-target="#myModal" class="list-group-item"><small><?php echo $workspace->epcs[$epcID]->name; ?></small></a>
            <?php } ?>
        </ul>
    </div>

    <script type="text/javascript">
        epcVisualizersNew = [];
        dropDownNum = 0;
        rowWidth = 520;
        columnWidth = 485;
        betweenWidth = 35;
        buttons = [];
        visualizationsForDiv = [];
        useRMMLayout = false;
        rmmLayout = undefined;
        loadingIsShown = false;
        numberOfTabs = 0;

        display_none = 'display: none; border: thin solid #DDDDDD';
        display_block = 'display: block; border: thin solid #DDDDDD';

        var $containingListTmp = $("#containing_ul");
        var listElementsTmp = $containingListTmp.children();
        var $availableListTmp = $("#available_ul");
        var listElementsAvailableTmp = $availableListTmp.children();
        numOfModelsInWorkspace = listElementsAvailableTmp.length + listElementsTmp.length;

        function updateWarning() {
            var dataFormatElement = document.getElementById("DATA_FORMAT");
            var dataFormat = dataFormatElement.options[dataFormatElement.selectedIndex].value;
            var warning1 = document.getElementById("warningRDF");
            var warning2 = document.getElementById("warningRDF2");
            if (dataFormat === "rdf") {
                warning1.style.visibility = 'visible';
                if (!matchViz.isMatchingBinaryMatching() || !matchViz.areAllMappingsOneToOne()) {
                    warning2.style.visibility = 'visible';
                } else {
                    warning2.style.visibility = 'hidden';
                }

            } else {
                warning1.style.visibility = 'hidden';
                warning2.style.visibility = 'hidden';
            }
        }

        function createView() {

            if (document.getElementById('matchingtypenarynary').checked) {
                // merge matchings
                matchViz.calculateMergedMatching();
                createNaryView();
            } else if (document.getElementById('matchingtypebinarynary').checked) {
                // merge matchings
                matchViz.mergeMatches();
                createBinaryView();
            } else {
                // do nothing special
                createBinaryView();
            }

        }

        function createDefaultView() {
            addTab();
            addTab();
            addEmptyDiv();
        }

        function createBinaryView() {
            matchViz.setMatchingBinaryMatching(true);
            addTab();
            addTab();
            addEmptyDiv();
        }

        function createNaryView() {
            matchViz.setMatchingBinaryMatching(false);
            addTab();
            addTab();
            addEmptyDiv();
        }
        
        function loadRMMLayout(){
            $.ajax({
                method: "POST",
                url: "classes/layoutModel.php",
                data: {
                }
            })
                    .done(function (msg) {
                        rmmLayout = JSON.parse(msg);
                        $('#rmm').removeAttr('disabled');
                    });
        }

        function loadRMMLayoutBinary() {
            $.ajax({
                method: "POST",
                url: "classes/layoutModel.php",
                data: {
                }
            })
                    .done(function (msg) {
                        rmmLayout = JSON.parse(msg);
                        if (loadingIsShown) {
                            $('#loadingDialog').modal('hide');
                        }
                        matchViz.checkInput();
                    });
        }

        function loadRMMLayoutNary() {
            $.ajax({
                method: "POST",
                url: "classes/layoutModel.php",
                data: {
                }
            })
                    .done(function (msg) {
                        rmmLayout = JSON.parse(msg);

                        if (loadingIsShown) {
                            $('#loadingDialog').modal('hide');
                        }
                        matchViz.checkInput();
                    });
        }

        function changeLayout(layouter) {

            if (layouter === "rmm") {
                if (useRMMLayout) {
                    return;
                }
                useRMMLayout = true;
            } else {
                if (!useRMMLayout) {
                    return;
                }
                useRMMLayout = false;
            }

            var $containingListTmp = $("#containing_ul");
            var listElements = $containingListTmp.children();

            allModels = [];
            for (var i = 0; i < listElements.length; i++) {
                allModels.push(listElements[i].innerText);
            }

            var $availableListTmp = $("#available_ul");
            listElements = $availableListTmp.children();

            for (var i = 0; i < listElements.length; i++) {
                allModels.push(listElements[i].innerText);
            }



            if (rmmLayout === undefined) {
                
            } else {
                for (var visNum = 0; visNum < vises.length; visNum++) {
                    var vis = vises[visNum];
                    for (var modelNum = 0; modelNum < rmmLayout.length; modelNum++) {
                        var model = rmmLayout[modelNum];
                        if (model.name == vis.name) {
                            if (vis.container.getAttribute("style") === display_none) {
                                vis.container.setAttribute("style", display_block);
                                vis.setRMMLayout(model.nodes, model.edges);
                                vis.useRMMLayout(useRMMLayout);
                                vis.container.setAttribute("style", display_none);
                            } else {
                                vis.setRMMLayout(model.nodes, model.edges);
                                vis.useRMMLayout(useRMMLayout);
                            }
                        }
                    }
                }
                matchViz.removeSelectedNodes();
                matchViz.showMatching();
            }



        }


        function addAddButton() {
            console.log("add Add Button");
            rowWidth = rowWidth + betweenWidth;
            var row = document.getElementById('rowElement');
            row.setAttribute('style', 'width: ' + rowWidth + 'px; padding-left: 10px; padding-right: 10px; margin: 0 auto;');
            var div = document.createElement('div');
            div.id = "addButtonDiv";
            div.setAttribute('style', 'float: left; width: ' + betweenWidth + 'px; text-align: center;');
            var headline = document.createElement('h2');
            var linkElement = document.createElement('a');
            linkElement.href = '#';
            var span = document.createElement('span');
            span.className = "glyphicon glyphicon-plus";
            span.setAttribute('onClick', 'addTab(); return false;');
            linkElement.appendChild(span);
            headline.appendChild(linkElement);
            div.appendChild(headline);
            row.insertBefore(div, document.getElementById('matchDetailsDiv'));
        }

        function addEmptyDiv() {
            console.log("add Add Button");
            rowWidth = rowWidth + betweenWidth;
            var row = document.getElementById('rowElement');
            row.setAttribute('style', 'width: ' + rowWidth + 'px; padding-left: 10px; padding-right: 10px; margin: 0 auto;');
            var div = document.createElement('div');
            div.setAttribute('style', 'float: left; width: ' + betweenWidth + 'px; height: 50px');
            row.insertBefore(div, document.getElementById('matchDetailsDiv'));
        }


        function createBinaryMatching() {
            $('#matchingTypeModal').modal('hide');
            $('#introModal').modal('show');
            matchViz.checkInput();
        }

        function createNAryMatching() {
            $('#matchingTypeModal').modal('hide');
            $('#introModal').modal('show');
            matchViz.checkInput();
        }

        $(window).load(function () {
            var $containingListTmp = $("#containing_ul");
            var listElementsContaining = $containingListTmp.children();

            allModels = [];
            for (var i = 0; i < listElementsContaining.length; i++) {
                allModels.push(listElementsContaining[i].innerText);
            }

            var $availableListTmp = $("#available_ul");
            listElements = $availableListTmp.children();

            for (var i = 0; i < listElements.length; i++) {
                allModels.push(listElements[i].innerText);
            }
            if (allModels.length < 2) {
                $('#errorModal').modal('show');
                return;
            }
            if (listElementsContaining.length < 2) {
                $('#errorModal').modal('show');
                return;
            }
            
            loadRMMLayout();

            if (matchViz.getNumberOfMatchings() == 0) {
                $('#matchingTypeModal').modal('show');
            } else {
                matchViz.checkInput();
//                if (matchViz.isMatchingBinaryMatching()) {
//                    createBinaryMatching();
//                } else {
//                    createNAryMatching();
//                }
            }

        });

        $(function () {
            $.contextMenu({
                selector: '.matchList-menu',
                callback: function (key, options) {
                    var m = "clicked: " + key;

                    if (key === "deleteMatch") {
                        var trigger = options.$trigger[0];
                        matchViz.deleteMatch(trigger.id);
                        document.getElementById("matchMetrics").innerHTML = matchViz.getHTMLCodeForMatchMetrics();
                    } else {
                        window.console && console.log(m) || alert(m);
                    }
                },
                items: {
                    "deleteMatch": {name: "Delete Match", icon: "delete"}
                }
            });

        });

        //http://stackoverflow.com/questions/31730363/select-table-row-and-keep-highlighted-using-twitter-bootstrap
        $('#matchesTable').on('click', '.clickable-row', function (event) {
            $(this).addClass('active').siblings().removeClass('active');
        });

        function removeTab(div) {
            numberOfTabs--;
            rowWidth = rowWidth - betweenWidth;
            rowWidth = rowWidth - columnWidth;
            var row = document.getElementById('rowElement');
            row.setAttribute('style', 'width: ' + rowWidth + 'px; padding-left: 10px; padding-right: 10px; margin: 0 auto;');

            var prev = div.previousSibling;
            prev.parentNode.removeChild(prev);
            div.parentNode.removeChild(div);

            visDivs = document.getElementsByClassName("visu");
            vises = [];
            for (var i = 0; i < visDivs.length; i++) {
                var id = visDivs[i].id;
                if (id.startsWith("visu")) {
                    var tmpList = epcVisualizersNew[id];
                    for (var j = 0; j < tmpList.length; j++) {
                        vises.push(tmpList[j]);
                    }
                }
            }
            matchViz.removeActiveContainer(div);
            matchViz.setVisualizations(vises);
        }

        function addTab(inserAfterDiv) {
            var $containingList = $("#containing_ul");
            var listElements = $containingList.children();
            if (numberOfTabs >= listElements.length) {
                $('#warningModal').modal('show');
                return;
            }
            numberOfTabs++;
            if (inserAfterDiv !== undefined) {

            } else {
                rowWidth = rowWidth + betweenWidth;
                var row = document.getElementById('rowElement');
                row.setAttribute('style', 'width: ' + rowWidth + 'px; padding-left: 10px; padding-right: 10px; margin: 0 auto;');
                var div = document.createElement('div');
                div.setAttribute('style', 'float: left; width: ' + betweenWidth + 'px; height: 50px');
                var element = document.getElementById('addButtonDiv');
                if (element === null) {
                    element = document.getElementById('matchDetailsDiv');
                }

                row.insertBefore(div, element);
            }

            rowWidth = rowWidth + columnWidth;
            console.log("add Tab");
            var row = document.getElementById('rowElement');
            row.setAttribute('style', 'width: ' + rowWidth + 'px; padding-left: 10px; padding-right: 10px; margin: 0 auto;');
            var div = document.createElement('div');
            div.id = "divModelView_" + dropDownNum;
            div.setAttribute('style', 'float: left; width: ' + columnWidth + 'px');


            var dropDownDiv = document.createElement('div');
            dropDownDiv.className = "dropdown";
            dropDownDiv.id = "dropdown_" + dropDownNum;
            var headline = document.createElement('h2');
            var button = document.createElement('button');
            button.id = "dropdown_button_" + dropDownNum;
            button.className = "btn btn-default dropdown-toggle";
            button.setAttribute('type', 'button');
            if (!matchViz.isMatchingBinaryMatching()) {
                button.setAttribute('style', 'width: 83.4%');
            } else {
                button.setAttribute('style', 'width: 100%');
            }
            button.setAttribute('data-toggle', 'dropdown');
            button.appendChild(document.createTextNode("Select a model"));

            buttons.push(button);

            var caret = document.createElement('span');
            caret.className = "caret";
            if (!matchViz.isMatchingBinaryMatching()) {
                caret.setAttribute('style', 'position: absolute; right: 0; top: 50%; margin-right: 95px;');
            } else {
                caret.setAttribute('style', 'position: absolute; right: 0; top: 50%; margin-right: 10px;');
            }
            button.appendChild(caret);

            var linkElement2 = document.createElement('a');
            linkElement2.href = '#';
            var span2 = document.createElement('span');
            span2.className = "glyphicon glyphicon-remove";
            span2.setAttribute('style', 'vertical-align: middle;')


            var button2 = document.createElement('button');
            button2.id = "s";
            button2.className = "btn btn-md btn-default";
            button2.setAttribute('type', 'button');
            button2.setAttribute('onClick', 'removeTab(' + div.id + '); return false;');
            button2.setAttribute('data-toggle', 'tooltip');
            button2.setAttribute('title', 'removes this visualization');
            button2.appendChild(span2);
            linkElement2.appendChild(button2);

            var linkElementAdd = document.createElement('a');
            linkElementAdd.href = '#';
            var spanAdd = document.createElement('span');
            spanAdd.className = "glyphicon glyphicon-plus";
            spanAdd.setAttribute('style', 'vertical-align: middle;')



            var buttonAdd = document.createElement('button');
            buttonAdd.id = "s";
            buttonAdd.className = "btn btn-md btn-default";
            buttonAdd.setAttribute('type', 'button');
            buttonAdd.setAttribute('onClick', 'addTab(' + div.id + '); return false;');
            buttonAdd.setAttribute('data-toggle', 'tooltip');
            buttonAdd.setAttribute('title', 'adds a new visualization after this element');
            buttonAdd.appendChild(spanAdd);
            linkElementAdd.appendChild(buttonAdd);



            var list = document.createElement('ul');
            list.className = 'dropdown-menu';
            list.setAttribute('style', 'width: 100%');

            var $containingList = $("#containing_ul");
            var listElements = $containingList.children();
            for (var i = 0; i < listElements.length; i++) {
                var listElement = document.createElement('li');
                var linkElement = document.createElement('a');
                linkElement.href = '#';
                var string = 'showEPCVisualization(event, ' + 'visu_epc' + listElements[i].dataset.id + '_dropDownNum' + dropDownNum + ')';
                linkElement.setAttribute('onClick', string);
                linkElement.appendChild(document.createTextNode(listElements[i].innerText));
                listElement.appendChild(linkElement);
                list.appendChild(listElement);
            }

            headline.appendChild(button);

            if (!matchViz.isMatchingBinaryMatching()) {
                headline.appendChild(linkElement2);
                headline.appendChild(linkElementAdd);
            }

            headline.appendChild(list);
            dropDownDiv.appendChild(headline);

            div.appendChild(dropDownDiv);

            var element = document.getElementById('addButtonDiv');
            if (element === null) {
                element = document.getElementById('matchDetailsDiv');
            }

            if (inserAfterDiv !== undefined) {
                element = inserAfterDiv;
                element.parentNode.insertBefore(div, element.nextSibling);
            } else {
                row.insertBefore(div, element);
            }
            if (inserAfterDiv !== undefined) {
                rowWidth = rowWidth + betweenWidth;
                var row = document.getElementById('rowElement');
                row.setAttribute('style', 'width: ' + rowWidth + 'px; padding-left: 10px; padding-right: 10px; margin: 0 auto;');
                var divEmpty = document.createElement('div');
                divEmpty.setAttribute('style', 'float: left; width: ' + betweenWidth + 'px; height: 50px');

                row.insertBefore(divEmpty, div);
            }



            var firstModel = undefined;
            var divsInThisTab = [];
            var vizesInThisTab = [];
            for (var i = 0; i < listElements.length; i++) {
                var divModel = document.createElement('div');
                divModel.className = 'visu';
                divModel.id = 'visu_epc' + listElements[i].dataset.id + '_dropDownNum' + dropDownNum;
                div.appendChild(divModel);



                var callMethod = 'drawEPC' + listElements[i].dataset.id;
                console.log(callMethod);
                window[callMethod](divModel, matchViz);

//                divModel.style.display = "none";
                divModel.setAttribute("style", display_none);

                if (firstModel === undefined) {
                    firstModel = divModel;
                }

                divsInThisTab.push(divModel);
                var v = 'epcViz_' + listElements[i].dataset.id;
                var vv = window[v];
                vizesInThisTab.push(vv);
            }

            if (rmmLayout !== undefined){
            for (var visNum = 0; visNum < vizesInThisTab.length; visNum++) {
                var vis = vizesInThisTab[visNum];
                for (var modelNum = 0; modelNum < rmmLayout.length; modelNum++) {
                    var model = rmmLayout[modelNum];
                    if (model.name == vis.name) {
                        if (vis.container.getAttribute("style") === display_none) {
                            vis.container.setAttribute("style", display_block);
                            vis.setRMMLayout(model.nodes, model.edges);
                            vis.useRMMLayout(useRMMLayout);
                            vis.container.setAttribute("style", display_none);
                        } else {
                            vis.setRMMLayout(model.nodes, model.edges);
                            vis.useRMMLayout(useRMMLayout);
                        }
                    }
                }
            }
            }

            visDivs = document.getElementsByClassName("visu");
            vises = [];
            for (var i = 0; i < visDivs.length; i++) {
                var id = visDivs[i].id;
                if (id.startsWith("visu")) {
                    var tmpList = epcVisualizersNew[id];
                    for (var j = 0; j < tmpList.length; j++) {
                        vises.push(tmpList[j]);
                    }
                }
            }

            matchViz.setVisualizations(vises);

            var nextName = matchViz.getNextModelName();
            var nextVizNum = -1;
            for (var i = 0; i < vizesInThisTab.length; i++) {
                if (vizesInThisTab[i].name === nextName) {
                    nextVizNum = i;
                    break;
                }
            }

//            var nextViz = matchViz.getNextViz(dropDownNum);

            var conti = divsInThisTab[nextVizNum];
            if (conti === undefined) {
                conti = divsInThisTab[0];
            }

            matchViz.setActiveContainer(conti);
//            conti.style.display = "block";
            conti.setAttribute("style", display_block);

            var buttonText = document.getElementById("dropdown_button_" + conti.id.charAt(firstModel.id.length - 1)).firstChild;
//            var t = matchViz.getViz(firstModel.id);

            var nextVizObj = vizesInThisTab[nextVizNum];
            if (nextVizObj === undefined) {
                nextVizObj = vizesInThisTab[0];
            }
            buttonText.data = nextVizObj.name;

            dropDownNum++;
        }

        function showEPCVisualization(evt, epcViz) {
            console.log('epcVisualization');
// Declare all variables
            var i, tabcontent, tablinks;

// Get all elements with class="tabcontent" and hide them
            tabcontent = document.getElementsByClassName("visu");
            for (i = 0; i < tabcontent.length; i++) {
                if (tabcontent[i].id.endsWith(epcViz.id.charAt(epcViz.id.length - 1))) {
//                    tabcontent[i].style.display = "none";
                    tabcontent[i].setAttribute("style", display_none);
                }

            }

            matchViz.setActiveContainer(epcViz);

// Show the current tab, and add an "active" class to the link that opened the tab
//            epcViz.style.display = "block";
            epcViz.setAttribute("style", display_block);

            var index = epcViz.id.lastIndexOf("_") + 1;
            var string = epcViz.id.substring(index);
            var num = string.replace("dropDownNum", "");
            var button = document.getElementById("dropdown_button_" + num);
            var buttonText = button.firstChild;
            buttonText.data = evt.target.innerText;


        }
    </script>



    <?php echo $jsMatchViz; ?>


    <script type="text/javascript">
        prevSelectedModelLeft = undefined;
        prevSelectedModelRight = undefined;
        firstModelLeft = undefined;
        firstModelRight = undefined;

        function KeyPress(e) {
            var evtobj = window.event ? event : e
            if (evtobj.keyCode == 90 && evtobj.ctrlKey) {
                matchViz.undo();
            } else if (evtobj.keyCode == 89 && evtobj.ctrlKey) {
                matchViz.redo();
            } else if (evtobj.keyCode == 83 && evtobj.ctrlKey) {
                $('#saveModal').modal('show');
                return false;
            } else if (evtobj.keyCode == 13) {
                matchViz.addSelectedNodesToMatch();
                return false;
            }
        }
        document.onkeydown = KeyPress;

        $('#myModal').on('show.bs.modal', function (e) {
            document.getElementById("modelHeaderViz").innerText = e.relatedTarget.innerText;

            $("#EPC").html("");
        });

        // source: http://stackoverflow.com/questions/5320194/get-order-of-list-items-in-a-jquery-sortable-list-after-resort
        var $availableList = $("#available_ul");
        var $containingList = $("#containing_ul");

        prevList = [];

        var sortEventHandler = function (event, ui) {
            console.log("New sort order!");

            var listElements = $containingList.children();

            var modelIDs = [];
            for (var i = 0; i < listElements.length; i++) {
                modelIDs.push(listElements[i].innerText);
            }

            for (var k = 0; k < buttons.length; k++) {
                var button = buttons[k];
                var sibling = button.nextElementSibling.nextElementSibling;
                if (sibling === null) {
                    sibling = button.nextElementSibling;
                } else {
                    sibling = sibling.nextElementSibling;
                }
                sibling.innerHTML = "";
                if (listElements.length > 0) {




                    for (var i = 0; i < listElements.length; i++) {
                        var li = document.createElement("li");
                        var a = document.createElement("a");
                        var index = button.id.lastIndexOf("_") + 1;
                        var num = button.id.substring(index);

                        var string = 'showEPCVisualization(event, ' + 'visu_epc' + listElements[i].dataset.id + '_dropDownNum' + num + ')';
                        a.setAttribute("onClick", string);
                        a.setAttribute("href", "#");

                        a.appendChild(document.createTextNode(listElements[i].innerText));

                        li.appendChild(a);

                        sibling.appendChild(li);
                    }

                } else {
                    var buttonTextLeft = document.getElementById("left_dropdown_button").firstChild;
                    buttonTextLeft.data = "Select a model";
                    var buttonTextRight = document.getElementById("right_dropdown_button").firstChild;
                    buttonTextRight.data = "Select a model";
                }
            }
            var action = "addedModelToMatching";

            var difference = [];


            if (modelIDs.length < prevList.length) {
                action = "removedModelFromMatching";
                // source: http://jsfiddle.net/u9xES/
                jQuery.grep(prevList, function (el) {
                    if (jQuery.inArray(el, modelIDs) == -1)
                        difference.push(el);
                });
            }
            if (modelIDs.length > prevList.length) {
                action = "addedModelToMatching";
                // source: http://jsfiddle.net/u9xES/
                jQuery.grep(modelIDs, function (el) {
                    if (jQuery.inArray(el, prevList) == -1)
                        difference.push(el);
                });
            }
            if (modelIDs.length === prevList.length) {
                action = "changedOrderOfModelsInMatching";
            }
            var actionObject = difference.toString();
            var innvocationObject = "ModelList";

            var error = "";
            var status = "started";
            matchViz.log.push({Timestamp: matchViz.getTimeString(), Action: action, InMatchViz: true, InvocationObject: innvocationObject, Error: error, ActionObject: actionObject, Status: status});
            var status = "finished";

            matchViz.setModels(modelIDs);

            prevList = [];
            for (var nu = 0; nu < modelIDs.length; nu++) {
                prevList[nu] = modelIDs[nu];
            }



            var action = "selectedLeftModel";
            var innvocationObject = "";
//                var actionObject = previousLeftEPC.name;

            var error = "";
            var status = "called";
//                $(prevActiveLeft).tab('show');
//                matchViz.setLeftEPCViz(previousLeftEPC);
            matchViz.log.push({Timestamp: matchViz.getTimeString(), Action: action, InMatchViz: true, InvocationObject: innvocationObject, Error: error, ActionObject: actionObject, Status: status});

//                $(prevActiveRight).tab('show');
//                matchViz.setRigthEPCViz(previousRightEPC);

            action = "selectedRightModel";
            innvocationObject = "Auto";
//                actionObject = previousRightEPC.name;

            matchViz.log.push({Timestamp: matchViz.getTimeString(), Action: action, InMatchViz: true, InvocationObject: innvocationObject, Error: error, ActionObject: actionObject, Status: status});





            console.log(listElements); // [ <li>, <li>, ... ]
        };

        $containingList.sortable({
            stop: sortEventHandler,
            connectWith: $availableList
        });

        $availableList.sortable({
            stop: sortEventHandler,
            connectWith: $containingList
        });




        $('#myModal').on('shown.bs.modal', function (e) {

            var action = "showModelInModalWindow";
            var innvocationObject = "ModelList";
            var error = "";
            var status = "started";


            var id = e.relatedTarget.dataset.id;
            var actionObject = e.relatedTarget.childNodes[0].innerText;
            matchViz.log.push({Timestamp: matchViz.getTimeString(), Action: action, InMatchViz: true, InvocationObject: innvocationObject, Error: error, ActionObject: actionObject, Status: status});
            var status = "finished";

            $("#EPC").html("");
            var a = 'drawEPC' + id;
            window[a]();

            var a = 'onClickViewEpcViz_' + id;
            var vis = window[a];
            if (useRMMLayout) {
                for (var modelNum = 0; modelNum < rmmLayout.length; modelNum++) {
                    var model = rmmLayout[modelNum];
                    if (model.name == vis.name) {
                        if (vis.container.getAttribute("style") === "display: none;") {
                            vis.container.setAttribute("style", "display: block;");
                            vis.setRMMLayout(model.nodes, model.edges);
                            vis.useRMMLayout(true);
                            vis.container.setAttribute("style", "display: none;");
                        } else {
                            vis.setRMMLayout(model.nodes, model.edges);
                            vis.useRMMLayout(true);
                        }
                    }
                }
            }

            matchViz.log.push({Timestamp: matchViz.getTimeString(), Action: action, InMatchViz: true, InvocationObject: innvocationObject, Error: error, ActionObject: actionObject, Status: status});


        });

    </script>

    <div id="matchDetailsDiv" style="float: left; width: 250px;"><h2>Matching Details</h2>
        <div id='matchMetrics'></div>
        <script type="text/javascript">
            function addNodesToMatching() {
                matchViz.addSelectedNodesToMatch();
                document.getElementById("matchMetrics").innerHTML = matchViz.getHTMLCodeForMatchMetrics();
            }
            ;
            function save() {
                var dataFormatElement = document.getElementById("DATA_FORMAT");
                var dataFormat = dataFormatElement.options[dataFormatElement.selectedIndex].value;
                var mappingType = "binary";
                var fileName = document.getElementById("filename").value;
                matchViz.saveChanges(fileName, dataFormat, mappingType);
            }
            ;

            function setLayouter() {
                var layouterElement = document.getElementById("LAYOUTER");
                var layouter = layouterElement.options[layouterElement.selectedIndex].value;
                changeLayout(layouter);
            }
            ;

        </script>


        <div class="form-group">
            <button type="button" class="btn btn-default" onclick="addNodesToMatching('addMatchButton');" title="adds the selectes Nodes to a new Match">add Match</button>
            <button id="saveButton" type="button" class="btn btn-default" data-toggle="modal" data-target="#saveModal" onclick="updateWarning()" title="opens the save window">Save</button>
            <button id="configButton" type="button" class="btn btn-default" data-toggle="modal" data-target="#configModal" title="opens the settings window"><span class="glyphicon glyphicon-cog"></span>&nbsp;</button>
            <button id="helpButton" type="button" class="btn btn-default" data-toggle="modal" data-target="#introModal" title="opens the help window">?</button>
        </div>
        <!--<button type="button" class="btn btn-default" onclick="matchViz.saveChanges();">save</button>-->
        <script type="text/javascript">
            document.getElementById("matchMetrics").innerHTML = "";
        </script>
        <div class="form-group">
            <table class="table table-bordered" id="matchesTable">
    <!--            <tr>
                    <th colspan="2">visible</th>
                    <th>node name</th>
                    <th>node name</th>
                    <th><i class="glyphicon glyphicon-unchecked" </i></th>
                </tr>-->
                <script type="text/javascript">
                    //matchViz.getHTMLCodeForMatchMetrics();
                </script>
            </table>
        </div>

        <script type="text/javascript">
            drawMatching();
            tabcontent = document.getElementsByClassName("visu");
            for (i = 0; i < tabcontent.length; i++) {

//                tabcontent[i].style.display = "none";
                tabcontent[i].setAttribute("style", display_none);

            }

            $(function () {
                $.contextMenu({
                    selector: '.matchList-menu',
                    callback: function (key, options) {
                        var m = "clicked: " + key;

                        if (key === "deleteMatch") {
                            var trigger = options.$trigger[0];
                            matchViz.deleteMatch(trigger.id);
                            document.getElementById("matchMetrics").innerHTML = matchViz.getHTMLCodeForMatchMetrics();
                        } else {
                            window.console && console.log(m) || alert(m);
                        }
                    },
                    items: {
                        "deleteMatch": {name: "Delete Match", icon: "delete"},
                        "edit": {name: "Edit", icon: "edit"},
                        "cut": {name: "Cut", icon: "cut"},
                        copy: {name: "Copy", icon: "copy"},
                        "paste": {name: "Paste", icon: "paste"},
                        "sep1": "---------",
                        "quit": {name: "Quit", icon: function () {
                                return 'context-menu-icon context-menu-icon-quit';
                            }}
                    }
                });


            });

            $(function () {
                $.contextMenu({
                    selector: '.context-menu-matches-node',
                    callback: function (key, options) {
                        var da = $(this).data();
                        var matchID = da.matchid;
                        var nodeid = da.nodeid;
                        var modelName = da.modelid;
                        var node = {modelID: modelName, nodeID: nodeid};
                        matchViz.deleteNodeFromMatch(node, matchID, undefined);
                    },
                    items: {
                        "delete": {name: "Delete Node from Match", icon: "delete"}
                    }
                });

            });

            //http://stackoverflow.com/questions/31730363/select-table-row-and-keep-highlighted-using-twitter-bootstrap
            $('#matchesTable').on('click', '.clickable-row', function (event) {
                $(this).addClass('active').siblings().removeClass('active');
            });

            var availableModels = [];
            var $availableList = $("#containing_ul");
            var availableElements = $availableList.children();
            for (var i = 0; i < availableElements.length; i++) {
                availableModels.push(availableElements[i].innerText);
            }
            matchViz.setModels(availableModels);
        </script>

    </div>
</div>