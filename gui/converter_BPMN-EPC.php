<?php
$reloadLink = "index.php?site=converter_BPMN-EPC&action=doConvertUploadedBPMNModelFile";
?>

<div class="jumbotron">
	<h1>BPMN to EPC Converter</h1>
	
	<p>This tool converts your .<a href="http://www.omg.org/spec/BPMN/" target="_blank">bpmn</a> files (BPMN) to .<a href="http://www.mendling.com/EPML/" target="_blank">epml</a> files. At the moment, only EPCs with the basic constructs are supported.</p>

	<h2></h2>
	<ol class="breadcrumb">
				  <li class="active"><span class="glyphicon glyphicon-cloud-upload" aria-hidden="true"></span> Drag &amp; drop files here</li>
				</ol>
	<span class="btn btn-success fileinput-button"> <i
		class="glyphicon glyphicon-plus"></i> <span>upload files ...</span> <!-- The file input field used as target for the file upload widget -->
		<input id="fileupload" type="file" name="files[]" multiple>
	</span> <br> <br>
	<div id="progress" class="progress">
		<div class="progress-bar progress-bar-success"></div>
	</div>

	<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> -->
	<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
	<script
		src="gui/lib/jQuery-File-Upload-9.9.3/js/vendor/jquery.ui.widget.js"></script>
	<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
	<script
		src="gui/lib/jQuery-File-Upload-9.9.3/js/jquery.iframe-transport.js"></script>
	<!-- The basic File Upload plugin -->
	<script src="gui/lib/jQuery-File-Upload-9.9.3/js/jquery.fileupload.js"></script>

	<script>
		$(function () {
		    'use strict';
		    // Change this to the location of your server-side upload handler:
		    var url = window.location.hostname === 'blueimp.github.io' ? '//jquery-file-upload.appspot.com/' : 'gui/php-handler/';
		    
		    $('#fileupload').fileupload({
		        url: url,
		        dataType: 'json',
		        done: function (e, data) {
		            $.each(data.result.files, function (index, file) {
		                $('<p/>').text(file.name).appendTo('#files');
		            });
		        },
		        progressall: function (e, data) {
		            var progress = parseInt(data.loaded / data.total * 100, 10);
		            $('#progress .progress-bar').css(
		                'width',
		                progress + '%'
		            );
		            if ( progress == 100 ) { 
		                location.href='<?php echo $reloadLink; ?>';
		            }
		        }
		    }).prop('disabled', !$.support.fileInput)
		        .parent().addClass($.support.fileInput ? undefined : 'disabled');
		});
		</script>

		<?php 
		if ( isset($_POST["msg"]) ) {
	
			?>
			<div class="alert alert-<?php echo $_POST["msgType"]; ?> alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			  <?php echo $_POST["msg"]; ?>
			</div>
			<?php 
		}
		?>
		
		<?php
		// load filepath
		$sessionID = session_id ();
		$path = Config::FILES_PATH . "/" . $sessionID;
		$files = array();

		// getting files
		if (is_dir ( $path )) {
			$files = scandir ( $path );
			// remove all entries which are no empl or xml
			foreach ( $files as $index => $entry ) {
				if (! (strtolower ( substr ( $entry, - 5 ) ) == ".bpmn" ) ) {
					unset ( $files[$index] );
				} else {
					$files[$index] = substr($entry, 0, -5);
				}
			}
		}
		$files = array_unique($files);
		
		if ( !empty($files) ) { ?>
			
			<h2>Available files <a href="index.php?site=converter_BPMN-EPC"><medium><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span></medium></a></h2>
			
		<?php
		
		foreach ( $files as $file ) { 
			$bpmnLabel = ".bpmn";
			$bpmn = $path."/".$file.".bpmn";
			$bpmnDownload = "download=\"".$file.".bpmn\"";
			$bpmnDisabled = is_file($bpmn) ? "" : "disabled";
			
			$epmlLabel = ".epml";
			$epml = $path."/".$file.".epml";
			$epmlDownload = "download=\"".$file.".epml\"";
			$epmlDisabled = is_file($epml) ? "" : "disabled";
			
			// conversion outdated, so offer to try again
			if ( !is_file($epml) ) {
				$filetime = filemtime($bpmn);
				$currentTime = time();
				if ( $currentTime - $filetime > 10 ) {
					$_SESSION['uploadedFilePath'] = $bpmn;
					$epml = "index.php?site=converter_BPMN-EPC&action=doConvertUploadedBPMNModelFile";
					$epmlDownload = "";
					$epmlDisabled = "";
					$epmlLabel = "convert to .epml";
				}
			}
			
			
			?>
		
			<div class="input-group">
			  <input type="text" class="form-control" aria-label="..." value="<?php echo $file; ?>" disabled>
			  <div class="input-group-btn">
			    <a class="btn btn-default" role="button" <?php echo $bpmnDisabled; ?> href="<?php echo $bpmn; ?>" <?php echo $bpmnDownload; ?>><?php echo $bpmnLabel; ?></a>
			    <a class="btn btn-default" role="button" <?php echo $epmlDisabled; ?> href="<?php echo $epml; ?>" <?php echo $epmlDownload; ?>><?php echo $epmlLabel; ?></a>
			    <a class="btn btn-danger" role="button" href="index.php?site=converter_BPMN-EPC&action=doRemoveConverterBPMNFile&file=<?php echo $file; ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
			  </div>
			</div>
			<!-- /input-group --><br />
			<?php } ?>
			
			<div class="alert alert-info" role="alert"><strong>Hint!</strong> The model files are automatically available in your (temporary) <a href="index.php?site=repository">repository</a>.</div>
			
		<?php }
		?>

</div>
