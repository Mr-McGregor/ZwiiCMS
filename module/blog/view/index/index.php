<?php if($module::$articles): ?>
	<div class="row">
		<div class="col12">
			<?php foreach($module::$articles as $articleId => $article): ?>					
				<div class="row">
					<div class="col3">
						<a href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/' . $articleId; ?>" class="blogPicture">
							<img src="<?php echo helper::baseUrl(false) . self::FILE_DIR.'thumb/' . $article['picture']; ?>" alt="<?php echo $article['picture']; ?>">
						</a>
					</div>
					<div class="col9">
						<h1 class="blogTitle">
							<a href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/' . $articleId; ?>">
								<?php echo $article['title']; ?>
							</a>
						</h1>
						<div class="blogComment">
							<a href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/' . $articleId; ?>#comment">
								<?php echo count($article['comment']); ?>
							</a>
							<?php echo template::ico('comment', 'left'); ?>
						</div>						
						<div class="blogDate">
							<i class="far fa-calendar-alt"></i>
							<?php echo utf8_encode(strftime('%d %B %Y', $article['publishedOn']));  ?>
						</div>
						<p class="blogContent">
							<?php echo helper::subword(strip_tags($article['content']), 0, 300); ?>...
							<a href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/' . $articleId; ?>">Lire la suite</a>
						</p>
					</div>
				</div>
				<hr />
			<?php endforeach; ?>
		</div>
	</div>
	<?php echo $module::$pages; ?>
<?php else: ?>
	<?php echo template::speech('Aucun article.'); ?>
<?php endif; ?>