<?php
	/*
		DownloadMii App List Page (by current user)
	*/
	$page = 'Userpage';
	$title = 'Userpage'; //TODO: Set to requested username
	require_once('../common/ucpheader.php');
	if(false){ //TODO: duh
?>
		<h1 class="animated bounceInDown text-center"><img class="user-avatar" src="https://i.imgur.com/BkPIjrB.png"/> SetToUsername</h1>
		<hr/>
		<div class="container-fluid">
			<div id="appcontainer">
				<div class="row">
					<?php
						for($x = 0; $x <= 12; $x++){
					?>
					<a href="/apps/view/guid" style="color: black;max-width:100%">
						<div itemscope itemtype="http://schema.org/SoftwareApplication" class="col-sm-2 col-xs-6 app-view" style="height:280px;margin-bottom:30px">
							<div style="max-width:100%;overflow:hidden;white-space:nowrap;">
								<img class="app-icon" alt="App logo" src="https://i.imgur.com/BkPIjrB.png"/>
								<div class="app-content app-vertical-center-outer pull-left" style="padding:0 10px;background:#f3f3f3;width:100%;">
									<div class="pull-left">
										<h4 class="app-vertical-center-inner">
											<span itemprop="name" style="float:left;overflow:hidden"> <div class="app-name">AppName<div class="dimmer"/></div></span><br/>
											<span itemprop="publisher" itemscope itemtype="http://schema.org/Organization" style="width:100%;padding:2px;font-size:14px">publisher</span>
										</h4>
									</div>
								</div>
								<div class="app-content app-vertical-center-outer pull-right btn-toolbar" style="background:#f3f3f3;width:100%;padding:15px 10px">
									<div class="app-vertical-center-inner" style="text-align: center;">
										<div><span class="glyphicon glyphicon-download"></span> 736 downloads</div>
										<button class="btn btn-default disabled" style="display: inline-block">3DS</button><button class="btn btn-default disabled" style="display: inline-block">Wii U</button>
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
<?php
	}
	else{
?>
	<h1 class="animated bounceInDown text-center">Coming Soon</h1>
<?php
	}
	require_once('../common/ucpfooter.php');
?>