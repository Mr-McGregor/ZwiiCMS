<?php echo template::formOpen('galleryConfigForm'); ?>
	<div class="row">
		<div class="col2">
			<?php echo template::button('galleryConfigBack', [
				'class' => 'buttonGrey',
				'href' => helper::baseUrl() . 'page/edit/' . $this->getUrl(0),
				'ico' => 'left',
				'value' => 'Retour'
			]); ?>
		</div>
	</div>
	<div class="row">
		<div class="col12">
			<div class="block">
				<h4>Nouvelle galerie</h4>
				<div class="row">
					<div class="col4">
						<?php echo template::text('galleryConfigName', [
							'label' => 'Nom'
						]); ?>
					</div>
					<div class="col4">
						<?php echo template::hidden('galleryConfigDirectoryOld', [
							'noDirty' => true // Désactivé à cause des modifications en ajax
						]); ?>
						<?php echo template::select('galleryConfigDirectory', [], [
							'label' => 'Dossier cible',
							'noDirty' => true // Désactivé à cause des modifications en ajax
						]); ?>
					</div>
					<div class="col3">
						<?php echo template::select('galleryConfigSort', $module::$sort, [
							'label' => 'Tri des images',
							'help' => 'Les images sont triées par nom de fichier grâce à la méthode naturelle qui donne de meilleurs résultats lorsque les images sont numérotées.'
						]); ?>	
					</div>
					<div class="col1 verticalAlignBottom">
						<?php echo template::submit('galleryConfigSubmit', [
							'ico' => '',
							'value' => template::ico('plus')
						]); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php echo template::formClose(); ?>	
<?php echo template::formOpen('galleryConfigFilterForm'); ?>
	<?php if($module::$galleries): ?>
	<?php echo template::table([1, 4, 5, 1, 1], $module::$galleries, ['','Nom', 'Dossier cible', '', ''], ['id' => 'galleryTable'],$module::$galleriesId); ?>
	<?php echo template::hidden('galleryConfigFilterResponse'); ?>
	<?php echo template::hidden('galleryConfigFilterSubmit',[
				'value' => false
			]); ?>
	<?php else: ?>
		<?php echo template::speech('Aucune galerie.'); ?>
	<?php endif; ?>
	<div class="row">
		<div class="col2 offset10">
			<?php echo template::submit('galleryConfigFilterSubmit', [
				'value' => 'Trier',
				'disabled' => true
			]); ?>
		</div>
	</div>
<?php echo template::formClose(); ?>
<div class="moduleVersion">Version n°
	<?php echo $module::GALLERY_VERSION; ?>
</div>