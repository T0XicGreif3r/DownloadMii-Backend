<?php ob_start(); theme_include('header'); ?>
	<div style="padding: 0 40px; margin-top: 65px; margin-bottom: 35px;">
		<section class="content wrap" id="article-<?php echo article_id(); ?>">
			<h1><?php echo article_title(); ?></h1>
			<h3>
				Posted: <time><?php echo article_date(); ?></time> By <?php echo article_author('real_name'); ?>
			</h3>
			<?php
				$pageTitle = article_title() . ' - DownloadMii Blog';
				$buffer=ob_get_contents();
				ob_end_clean();
				$buffer=str_replace("%TITLE%", $pageTitle,$buffer);
				echo $buffer;
				if(article_custom_field('appnameField1', 'null') != 'null'){
			?>
					App: <a href="https://www.downloadmii.com/apps/view/<?php echo article_custom_field('appnameField1_GUID'); ?>"><?php echo article_custom_field('appnameField1'); ?></a>
					<br />
			<?php
				}
			?>
			<div class="addthis_sharing_toolbox"></div>
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
			<article>
				<?php echo article_markdown(); ?>
				<a href="https://www.downloadmii.com">Return to DownloadMii's homepage </a>
			</article>
		</section>
		<section class="comments">
			  <div id="disqus_thread"></div>
			    <script type="text/javascript">
			        var disqus_shortname = 'downloadmii'; // required: replace example with your forum shortname
			
			        /* * * DON'T EDIT BELOW THIS LINE * * */
			        (function() {
			            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
			            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
			            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
			        })();
			    </script>
		</section>
	</div>
<?php theme_include('footer'); ?>
