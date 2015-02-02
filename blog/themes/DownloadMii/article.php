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
					App: <a href="https://www.downloadmii.com/apps/<?php echo article_custom_field('appnameField1_GUID'); ?>"><?php echo article_custom_field('appnameField1'); ?></a>
					<br />
			<?php
				}
			?>
			<div class="addthis_sharing_toolbox"></div>
			<br />
			<article>
				<?php echo article_markdown(); ?>
				<a href="https://www.downloadmii.com">Return to DownloadMii's homepage </a>
			</article>
		</section>
		<?php if(comments_open()): ?>
		<section class="comments">
			<?php if(has_comments()): ?>
			<h3>Comments</h3>
			<ul class="commentlist">
				<?php $i = 0; while(comments()): $i++; ?>
				<li class="comment" id="comment-<?php echo comment_id(); ?>">
					<div class="wrap">
						<h2><?php echo comment_name(); ?></h2>
						<time><?php echo relative_time(comment_time()); ?></time>

						<div class="content">
							<?php echo comment_text(); ?>
						</div>

						<span class="counter"><?php echo $i; ?></span>
					</div>
				</li>
				<?php endwhile; ?>
			</ul>
			<?php endif; ?>
			
			<h3>Leave a comment</h3>
			<form id="comment" class="commentform wrap" method="post" action="<?php echo comment_form_url(); ?>#comment">
				<?php echo comment_form_notifications(); ?>

				<p class="name">
					<label for="name">Your name:</label>
					<?php echo comment_form_input_name('placeholder="Your name"'); ?>
				</p>

				<p class="email">
					<label for="email">Your email address:</label>
					<?php echo comment_form_input_email('placeholder="Your email (won’t be published)"'); ?>
				</p>

				<p class="textarea">
					<label for="text">Your comment:</label>
					<?php echo comment_form_input_text('placeholder="Your comment"'); ?>
				</p>

				<p class="submit">
					<?php echo comment_form_button(); ?>
				</p>
			</form>

		</section>
		<?php endif; ?>
	</div>
<?php theme_include('footer'); ?>
