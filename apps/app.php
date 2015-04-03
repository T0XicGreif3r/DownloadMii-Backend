<?php
	/*
		DownloadMii App Information Page
	*/
	
	//TODO: Add more information (screenshots, reviews, etc.)
	
	require_once('../common/functions.php');
	
	$requestUri = strtok(getenv('REQUEST_URI'), '?');
	$appGuid = rtrim(substr($requestUri, strlen('/apps/view/')), '/');
	
	$mysqlConn = connectToDatabase();
	$matchingApps = getArrayFromSQLQuery($mysqlConn, 'SELECT app.name, app.description, app.downloads, app.publishstate, app.failpublishmessage,
														user.nick AS publisher, appver.number AS version, appver.largeIcon, maincat.name AS category, subcat.name AS subcategory, group_concat(scr.url) AS screenshots FROM apps app
														LEFT JOIN users user ON user.userId = app.publisher
														LEFT JOIN appversions appver ON appver.versionId = app.version
														LEFT JOIN categories maincat ON maincat.categoryId = app.category
														LEFT JOIN categories subcat ON subcat.categoryId = app.subcategory
														LEFT JOIN screenshots scr ON scr.appGuid = app.guid
														WHERE (app.publishstate = 1 OR app.publishstate = 4 OR app.publishstate = 5) AND app.guid = ?
														GROUP BY app.guid', 's', [$appGuid]);
	
	printAndExitIfTrue(count($matchingApps) !== 1, 'Invalid app GUID.');
	$app = $matchingApps[0];
	
	$title = $app['name'];
	$page = 'SingleAppViewPage';
	require_once('../common/uiheader.php');
?>

	<h1 class="animated bounceInDown text-center"><?php echo $app['name']; ?></h1><br />
	<h3 class="text-center">
		<span id="maincat" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"<?php if ($app['subcategory'] !== null) echo ' itemref="subcat"'; ?>>
			<a itemprop="url" href="https://www.downloadmii.com/apps/<?php echo $app['category']; ?>" style="color: black;">
				<span itemprop="title"><?php echo $app['category']; ?></span>
			</a>
		</span>
<?php
	if ($app['subcategory'] !== null) {
		echo
		'<span class="glyphicon glyphicon-chevron-right" style="font-size: 20px;"></span>
		<span id="subcat" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" itemprop="child">
			<a itemprop="url" href="https://www.downloadmii.com/apps/' . $app['category'] . '/' . $app['subcategory'] . '" style="color: black;">
				<span itemprop="title">' . $app['subcategory'] . '</span>
			</a>
		</span>';
	}
	
	$desc = preg_replace("~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~","<a href=\"\\0\">\\0</a>",  
              escapeHTMLChars($app['description']));
?>

	</h3>
	<br />
	<div id="appcontainer">
		<div vocab="http://schema.org/" typeof="SoftwareApplication">
			<div class="app-header">
				<div class="app-vertical-center-outer pull-left">
					<img class="app-icon" src="<?php if (!empty($app['largeIcon'])) echo $app['largeIcon']; else echo '/img/no_icon.png'; ?>" />
					<div class="pull-right">
						<h4 class="app-name app-vertical-center-inner">
							<span itemprop="name">
								<?php echo escapeHTMLChars($app['name']); ?>
							</span>
							<span class="app-version" itemprop="softwareVersion">
								<?php echo escapeHTMLChars($app['version']); ?>
							</span>
							<br/>
							<a href="/user/<?php echo $app['publisher']; ?>" style="color:black"><span class="app-publisher" itemprop="publisher" itemscope itemtype="http://schema.org/Organization">
								<?php echo escapeHTMLChars($app['publisher']); ?>
							</span></a>
						</h4>
					</div>
				</div>
				<div class="app-vertical-center-outer pull-right btn-toolbar">
					<div class="app-vertical-center-inner">
						<button class="btn btn-default disabled" style="display: inline-block">3DS</button>
						<button class="btn btn-default disabled"><span class="glyphicon glyphicon-download"></span> <?php echo $app['downloads']; ?> downloads </button>
					</div>
				</div>
			</div>
			<?php
			if ($app['screenshots'] !== null){
				$screenshots = explode(',', $app['screenshots']);
			?>
			<div class="row">
				<div class="clear-float col-sm-6 col-xs-12">
					<div id="carousel-appScr" class="carousel slide" data-ride="carousel" style="max-width:400px;">
						<ol class="carousel-indicators">
						<?php
							$temp = 0;
							foreach ($screenshots as $screenshot) {
								$temp += 1;
								if($temp==1)
								echo '<li class="active" data-target="#carousel-appScr" data-slide-to="'. $temp . '"></li>';
								else
								echo '<li data-target="#carousel-appScr" data-slide-to="'. $temp . '"></li>';
							}
						?>
						</ol>
						<div class="carousel-inner">
						<?php
							$temp = 0;
							foreach ($screenshots as $screenshot) {
								$temp += 1;
								if($temp==1){
									echo '<div class="item active"><img class="app-screenshot" src="' . $screenshot . '" /><div class="carousel-caption"></div></div>';
								}
								else
								echo '<div class="item"><img class="app-screenshot" src="' . $screenshot . '" /><div class="carousel-caption"></div></div>';
							}
						?>
						</div>
						<a class="left carousel-control" href="#carousel-appScr" role="button" data-slide="prev">
						<span class="glyphicon glyphicon-chevron-left"></span>
						</a>
						<a class="right carousel-control" href="#carousel-appScr" role="button" data-slide="next">
						<span class="glyphicon glyphicon-chevron-right"></span>
						</a>
					</div>
				</div>
				<div class="app-desc col-sm-6 col-xs-12" style="max-width:400px;float:left;">
					<?php
						echo $desc;
					?>
				</div>
			</div>
			<?php
			}
			else {
			?>
				<div class="app-desc clear-float" style="padding-top:8px">
					<?php
						echo $desc;
					?>
				</div>
			<?php
			}
			?>
		</div>
	</div>
<?php		
	require_once('../common/uifooter.php');
?>
