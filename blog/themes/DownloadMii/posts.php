<?php theme_include('header'); ?>

<div id="content" class="">

	<?php if(has_posts()): ?>
		<ul class="items">
			<?php $i = 0; while(posts()): ?>
			<li>
				<article class="wrap">
					<h1>
						<a style="color: #25A4D6 !important;" href="<?php echo article_url(); ?>" title="<?php echo article_title(); ?>"><?php echo article_title(); ?></a>
					</h1>
					
					<h3>
						Posted: <time><?php echo article_date(); ?></time>
					</h3>

					<div class="content">
						<?php echo article_markdown(); ?>
					</div>

					<h3>
						By <?php echo article_author('real_name'); ?>
					</h3
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

<?php theme_include('footer'); ?>
