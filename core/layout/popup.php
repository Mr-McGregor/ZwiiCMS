<?php $layout = new layout($this); ?>
<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#" lang="fr">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php $layout->showMetaTitle(); ?>
		<?php $layout->showMetaDescription(); ?>
		<?php $layout->showMetaType(); ?>			
		<?php $layout->showMetaImage(); ?>		
		<?php $layout->showFavicon(); ?>
		<?php $layout->showVendor(); ?>
		<?php $layout->showAnalytics(); ?>	
		<link rel="stylesheet" href="<?php echo helper::baseUrl(false); ?>core/layout/common.css">
		<link rel="stylesheet" href="<?php echo helper::baseUrl(false); ?>site/data/theme.css?<?php echo md5_file(self::DATA_DIR.'theme.css'); ?>">
		<link rel="stylesheet" href="<?php echo helper::baseUrl(false); ?>site/data/custom.css?<?php echo md5_file(self::DATA_DIR.'custom.css'); ?>">
		<?php $layout->showStyle(); ?>
		<?php if (file_exists('site/data/head.inc.html')) {
			include('site/data/head.inc.html'); 
		}?>
	</head>
	<body>		
	<?php $layout->showNotification(); ?>		
	<!-- Corps de page -->
	<section>
	<?php 
		// Gabarit :
		// Récupérer la config de la page courante
		$blocks = explode('-',$this->getData(['page',$this->getUrl(0),'block']));
		// Initialiser
		$blockleft=$blockright="";
		switch (sizeof($blocks)) {
			case 1 :  // une colonne
				$content    = 'col'. $blocks[0] ; 
				break;			
			case 2 :  // 2 blocks 
				if ($blocks[0] < $blocks[1]) { // détermine la position de la colonne
					$blockleft = 'col'. $blocks[0];
					$content    = 'col'. $blocks[1] ;
				} else {
					$content    = 'col' . $blocks[0];
					$blockright  = 'col' . $blocks[1];						
				}
			break;
			case 3 :  // 3 blocks
					$blockleft  = 'col' . $blocks[0];
					$content    = 'col' . $blocks[1];
					$blockright = 'col' . $blocks[2];	
		}
		// Page pleine pour la configuration des modules et l'édition des pages sauf l'affichae d'un article de blog
		$pattern = ['config','edit','add','comment','data'];
		if ((sizeof($blocks) === 1 || 
			in_array($this->getUrl(1),$pattern)  )
			) { // Pleine page en mode configuration
				$layout->showContent();
				if (file_exists('site/data/body.inc.html')) {
					include('site/data/body.inc.html'); 
				}				
		} else {
		?>
		<div class="row siteContainer"> 
			<?php 
				if ($blockleft !== "") :?> 
				<div class="<?php echo $blockleft; ?>" id="contentLeft"><?php 	$layout->showBarContentLeft(); ?></div> 
				<?php endif; ?>
				<div class="<?php echo $content; ?>" id="contentSite"><?php $layout->showContent();
					if (file_exists('site/data/body.inc.html')) {
						include('site/data/body.inc.html'); 
					}
				?>
				</div>
			<?php 
				if ($blockright !== "") :?> 
				<div class="<?php echo $blockright; ?>" id="contentRight"><?php $layout->showBarContentRight(); ?></div>
				<?php endif; ?>	
		</div>
		<?php }
		?>
	</section>
		<!-- Lien remonter en haut -->
		<div id="backToTop"><?php echo template::ico('up'); ?></div>
		<?php $layout->showScript();?>
</body>
</html>
