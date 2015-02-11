<?php theme_include('header'); ?>

<div id="content" class="">
	<div class="col-md-9 col-xs-12">
		<?php if(has_posts()): ?>
			<ul class="items">
				<?php $i = 0; while(posts()): $i++; ?>
				<li>
					<article class="wrap" id="post<?php echo $i; ?>">
						<h1 class="animated bounceInDown">
							<a style="color: #25A4D6 !important;" href="<?php echo article_url(); ?>" title="<?php echo article_title(); ?>"><?php echo article_title(); ?></a>
						</h1>
						<h3>
							Posted: <time><?php echo article_date(); ?></time>
						</h3>
						<br />
						
						<div class="content">
							<?php echo article_markdown(); ?>
						</div>
	
						<h3>
							By <?php echo article_author('real_name'); ?>
						</h3>
						<br />
						<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
						<!-- DownloadMii_Blog -->
						<ins class="adsbygoogle"
						     style="display:inline-block;width:320px;height:100px"
						     data-ad-client="ca-pub-1408003448017335"
						     data-ad-slot="8380336033"></ins>
						<script>
						(adsbygoogle = window.adsbygoogle || []).push({});
						</script>
					</article>
				</li>
				<hr />
				<?php endwhile; ?>
			</ul>
	
			<?php if(has_pagination()): ?>
			<nav class="pagination">
				<div class="wrap">
					<?php echo posts_prev(); ?>
					<?php echo posts_next(); ?>
				</div>
			</nav>
			<?php endif; ?>
	
		<?php else: ?>
			<div style="padding: 0 40px; margin-top: 65px; margin-bottom: 35px;">
				<h1>This page looks empty!</h1>
			</div>
		<?php endif; ?>
	</div>
	<!-- Recommended links -->
	<div class="col-md-3 col-xs-0 col-sm-0" style="margin-top: 112px;">
		<div class="addthis_recommended_vertical"></div>
		<div class="addthis_vertical_follow_toolbox"></div>
	</div>
</div>
<?php theme_include('footer'); ?>
