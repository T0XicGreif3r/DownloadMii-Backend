<?php
	/*
		DownloadMii App List Page (all published apps)
	*/
	
	//TODO: Clean code...
	
	$title = 'Browse Apps';
	require_once('../common/uiheader.php');
	
	$mysqlConn = connectToDatabase();
	$allApps = getArrayFromSQLQuery($mysqlConn, 'SELECT app.guid, app.name, app.description, app.downloads, app.publishstate, user.nick AS publisher, appver.number AS version, appver.largeIcon FROM apps app
													LEFT JOIN users user ON user.userId = app.publisher
													LEFT JOIN appversions appver ON appver.versionId = app.version
													WHERE app.publishstate = 1 ORDER BY appver.versionId DESC');
?>

	<h1 class="text-center">Browse Apps</h1>
	<br />
		<div class="input-group">
		  <input type="text" class="form-control" id="searchtext" placeholder="App name...">
		  <span class="input-group-btn">
			<button class="btn btn-primary" id="searchbutton" type="button">Search</button>
			<button class="btn btn-danger" id="resetbutton" type="button">Reset</button>
		  </span>
		</div>
	<br />
	
	<div id="appcontainer">
<?php
	foreach ($allApps as $app) {
?>

		<div class="well clearfix">
			<div class="app-vertical-center-outer pull-left">
				<img class="app-icon" src="<?php if (!empty($app['largeIcon'])) echo $app['largeIcon']; else echo '/img/no_icon.png'; ?>" />
				<div class="pull-right">
					<h4 class="app-vertical-center-inner">
						<a href="/apps/<?php echo $app['guid'] ?>" style="color: black;">
<?php
		echo '<span itemprop="name">' . escapeHTMLChars($app['name']) . '</span> <span itemprop="softwareVersion">' . escapeHTMLChars($app['version']) . '</span> by <span itemprop="publisher" itemscope itemtype="http://schema.org/Organization" style="font-style: italic;">' . $app['publisher'] . '</span>';
?>

						</a>
					</h4>
				</div>
			</div>
			<div class="app-vertical-center-outer pull-right btn-toolbar">
				<div class="btn-toolbar app-vertical-center-inner">
					<button class="btn btn-default disabled"><span class="glyphicon glyphicon-download"></span> <?php echo $app['downloads']; ?> unique downloads</button>
				</div>
			</div>
			<div class="clear-float" style="padding-top: 8px">
<?php
		echo escapeHTMLChars($app['description']);
?>

			</div>
		</div>
<?php
	}
?>

	</div>
	
	<script type="text/javascript">
	window.onload = function() {
		var populateAppContainer = function(dataSource) {
			$('#appcontainer').empty();
			dataSource.forEach(function(element) {
				$('#appcontainer').append('<div class="well clearfix">' +
											'<div itemscope itemtype="http://schema.org/SoftwareApplication" class="app-vertical-center-outer pull-left">' +
												'<img itemprop="image" class="app-icon" src="' + (element.largeicon !== '' ? element.largeicon : '/img/no_icon.png') + '" />' +
												'<div class="pull-right">' +
													'<h4 class="app-vertical-center-inner">' +
														'<a href="/apps/' + element.guid + '" style="color: black;">' +
															element.name + ' ' + element.version + ' by <span style="font-style: italic;">' + element.publisher + '</span>' +
														'</a>' +
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
		
		var searchAndPrintApps = function(searchString) {
			$.getJSON('/newapi/apps?find=' + searchString, function(data) {
				populateAppContainer(data.Apps);
			});
		}
		
		var printDefaultApps = function() {
			$.getJSON('/newapi/apps', function(data) {
				populateAppContainer(data.Apps);
			});
		}
		
		$('#searchbutton').on('click', function() {
			if ($('#searchtext').val() !== '') {
				searchAndPrintApps($('#searchtext').val());
			}
			else {
				printDefaultApps();
				$('#searchtext').val('');
			}
		});
		
		$('#resetbutton').on('click', function() {
			printDefaultApps();
			$('#searchtext').val('');
		});
		
		$('#searchtext').on('keypress', function(e) {
			if (e.which === 13) {
				$('#searchbutton').click();
			}
		});
		
		var params = getURLParams();
		if ('find' in params) {
			$('#searchtext').val(params['find']);
			searchAndPrintApps(params['find']);
		}
		else {
			//printDefaultApps();
		}
	}
	</script>
<?php		
	require_once('../common/uifooter.php');
?>
