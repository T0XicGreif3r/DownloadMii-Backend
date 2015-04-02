<?php
	/*
		DownloadMii App List Page (all published apps)
	*/
	
	//TODO: Clean code...
	
	$title = 'Browse Apps';
	$page = 'AppView';
	require_once('../common/uiheader.php');
	
	$mysqlQuery = 'SELECT app.guid, app.name, app.description, app.downloads, app.publishstate, user.nick AS publisher, appver.number AS version, appver.largeIcon FROM apps app
					LEFT JOIN users user ON user.userId = app.publisher
					LEFT JOIN appversions appver ON appver.versionId = app.version
					LEFT JOIN categories maincat ON maincat.categoryId = app.category
					LEFT JOIN categories subcat ON subcat.categoryId = app.subcategory
					WHERE app.publishstate = 1 OR app.publishstate = 4 OR app.publishstate = 5';
	
	$mysqlQueryEnd = ' ORDER BY appver.versionId DESC';
	
	$bindParamTypes = '';
	$bindParamArgs = array();
	
	$requestUri = strtok(getenv('REQUEST_URI'), '?');
	$uriParams = explode('/', rtrim(substr($requestUri, strlen('/apps/')), '/')); //All URL "directories" after /apps/ -> array
	for ($i = 0; $i < count($uriParams) && $i < 2; $i++) {
		if (strlen($uriParams[$i]) > 0) {
			$mysqlQuery .= ' AND ' . ($i === 0 ? 'maincat' : 'subcat') . '.name = ?';
			$bindParamTypes .= 's';
			array_push($bindParamArgs, $uriParams[$i]);
		}
	}
	
	$mysqlQuery .= $mysqlQueryEnd;
	
	$mysqlConn = connectToDatabase();
	$allApps = getArrayFromSQLQuery($mysqlConn, $mysqlQuery, $bindParamTypes, $bindParamArgs);
?>
	
	<h1 class="animated bounceInDown text-center">Browse Apps</h1>
	<br />
	<div class="row">
	<div class="col-md-offset-2 col-md-8 col-md-offset-2">
		<div class="input-group">
		  <input type="search" class="form-control" id="searchtext" placeholder="App name...">
		  <span class="input-group-btn">
			<button class="btn btn-primary" id="searchbutton" type="button">Search</button>
			<button class="btn btn-danger" id="resetbutton" type="button">Reset</button>
		  </span>
		</div>
	</div>
	</div>
	<hr/>
	<div class="container-fluid">
	<div id="appcontainer">
	<div class="row">
<?php
	foreach ($allApps as $app) {
?>
	<a href="/apps/view/<?php echo $app['guid'] ?>" style="color: black;max-width:100%">
		<div itemscope itemtype="http://schema.org/SoftwareApplication" class="col-sm-2 col-xs-6 app-view" style="height:280px;margin-bottom:30px">
			<div style="max-width:100%;overflow:hidden;white-space:nowrap;">
				<img class="app-icon" alt="App logo" src="<?php if (!empty($app['largeIcon'])) echo $app['largeIcon']; else echo '/img/no_icon.png'; ?>"/>
				<div class="app-content app-vertical-center-outer pull-left" style="padding:0 10px;background:#f3f3f3;width:100%;">
					<div class="pull-left">
						<h4 class="app-vertical-center-inner">
							<span itemprop="name" style="float:left;overflow:hidden"> <div class="app-name"><?php echo escapeHTMLChars($app['name']); ?><div class="dimmer"/></div></span><br/>
							<span itemprop="publisher" itemscope itemtype="http://schema.org/Organization" style="width:100%;padding:2px;font-size:14px"><?php echo $app['publisher']; ?></span>
						</h4>
					</div>
				</div>
				<div class="app-content app-vertical-center-outer pull-right btn-toolbar" style="background:#f3f3f3;width:100%;padding:15px 10px">
					<div class="app-vertical-center-inner" style="text-align: center;">
						<div><span class="glyphicon glyphicon-download"></span> <?php
						$n = $app['downloads'];
						$formatted = '';

						if($n >= 1000 && $n < 1000000) //Account for values over 1k
						{
							if($n%1000 === 0)
							{
								$formatted = ($n/1000);
							}
							else
							{
								$formatted = substr($n, 0, -3).'.'.substr($n, -3, -2);

								if(substr($formatted, -1, 1) === '0')
								{
									$formatted = substr($formatted, 0, -2);
								}
							}

							$formatted.= 'k';
						}
						else
							$formatted = $n;

						echo $formatted ?> downloads</div>
						<button class="btn btn-default disabled" style="display: inline-block">3DS</button> <button class="btn btn-default disabled" style="display: inline-block">Wii U</button> <!-- TODO: Fetch the console(s) this app is published on -->
					</div>
				</div>
			</div>
		</div>
	</a>
<?php
	}
?>
	</div>
	</div>
	</div>
	
	<script type="text/javascript">
	function getRepString (rep) {
	  rep = rep+''; // coerce to string
	  if (rep < 1000) {
		return rep;
	  }
	  // divide and format
	  return (rep/1000).toFixed(rep % 1000 != 0)+'k';
	}
	window.onload = function() {
		var populateAppContainer = function(dataSource) {
			$('#appcontainer').empty();
			dataSource.forEach(function(element) {
				$('#appcontainer').append('<a href="/apps/view/' + element.guid + '" style="color: black"><div itemscope itemtype="http://schema.org/SoftwareApplication" class="col-md-2 col-sm-4 col-xs-6 app-view" style="height:280px;margin-bottom:30px"><div style="max-width:100%;overflow:hidden;white-space:nowrap;"><img class="app-icon" alt="App logo" src="' + (element.largeicon !== '' ? element.largeicon : '/img/no_icon.png') + '"/><div class="app-content app-vertical-center-outer pull-left" style="padding:0 10px;background:#f3f3f3;width:100%;"><div class="pull-left"><h4 class="app-vertical-center-inner"><span itemprop="name" style="float:left;overflow:hidden"> <div class="app-name">' + element.name + '<div class="dimmer"/></div></span><br/><span itemprop="publisher" itemscope itemtype="http://schema.org/Organization" style="width:100%;padding:2px;font-size:14px">' + element.publisher + '</span></h4></div></div><div class="app-content app-vertical-center-outer pull-right btn-toolbar" style="background:#f3f3f3;width:100%;padding:15px 10px"><div class="app-vertical-center-inner" style="text-align: center;"><div><span class="glyphicon glyphicon-download"></span> ' + getRepString(element.downloads) + ' downloads</div><button class="btn btn-default disabled" style="display: inline-block">3DS</button> <button class="btn btn-default disabled" style="display: inline-block">Wii U</button> </div></div></div></div></a>');
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
