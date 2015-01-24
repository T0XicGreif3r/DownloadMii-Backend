<?php
	/*
		DownloadMii App List Page (all published apps)
	*/
	
	$title = 'Browse Apps';
	require_once('../common/uiheader.php');
?>

	<h1 class="text-center">Browse Apps</h1>
	<br />
		<div class="input-group">
		  <input type="text" class="form-control" id="searchtext" placeholder="App name...">
		  <span class="input-group-btn">
			<button class="btn btn-default" id="searchbutton" type="button">Search</button>
		  </span>
		</div>
	<br />
	
	<div id="appcontainer">

	</div>
	
	<script type="text/javascript">
	var populateAppContainer = function(dataSource) {
		$('#appcontainer').empty();
		dataSource.forEach(function(element) {
			$('#appcontainer').append('<div class="well clearfix">' +
										'<div class="app-vertical-center-outer pull-left">' +
											'<img class="app-icon" src="' + (element.largeicon !== '' ? element.largeicon : '/img/no_icon.png') + '" />' +
											'<div class="pull-right">' +
												'<h4 class="app-vertical-center-inner">' +
													element.name + ' ' + element.version + ' by <span style="font-style: italic;">' + element.publisher + '</span>' +
												'</h4>' +
											'</div>' +
										'</div>' +
										'<div class="app-vertical-center-outer pull-right btn-toolbar">' +
											'<div class="btn-toolbar app-vertical-center-inner">' +
												'<button class="btn btn-default disabled"><span class="glyphicon glyphicon-download"></span> ' + element.downloads + ' unique downloads</button>' +
											'</div>' +
										'</div>' +
										'<div class="clear-float" style="padding-top: 8px">' +
											element.description +
										'</div>' +
									'</div>');
		});
	}
	
	$(window).on('load', function() {
		$.getJSON('/api/apps/Applications', function(data) {
			populateAppContainer(data.Apps);
		});
	});
	
	$('#searchbutton').on('click', function() {
		$.getJSON('/api/apps/find/' + $('#searchtext').val(), function(data) {
			populateAppContainer(data.Search);
		});
	});
	</script>
<?php		
	require_once('../common/uifooter.php');
?>
