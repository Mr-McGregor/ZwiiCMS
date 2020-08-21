<?php echo template::formOpen('i18nIndexForm'); ?>
<div class="notranslate">
	<div class="row">
		<div class="col2">
			<?php echo template::button('i18nBack', [
				'class' => 'buttonGrey',
				'href' => helper::baseUrl(false),
				'ico' => 'home',
				'value' => 'Accueil'
			]); ?>
		</div>
		<div class="col2 offset8">
			<?php echo template::submit('galleryEditSubmit'); ?>
		</div>
	</div>
	<div class="row">
		<div class="col12">
			<div class="block">
			<h4>Ajouter une langue&nbsp;<a href="./core/module/i18n/ressource/help.html" data-lity data-tippy-content="Aide en ligne"<?php echo template::ico('help'); ?></a></h4>
				<div class="row">
					<div class="col6">
						<div class="row">
							<?php 
								$available = array ('' => 'Sélectionner');
								$available = array_merge ($available, array_diff(self::$i18nList,$this->i18nInstalled()));
								echo template::select('i18nLanguageAdd', $available, [
								'label' => 'Nouvelle langue'
								]); ?>							
						</div>					
					</div>				
					<div class="col6">
						<?php echo template::select('i18nLanguageCopyFrom', $this->i18nInstalled(true), [
							'label' => 'Site à dupliquer',
							'selected' => -1 					
						]); ?>
					</div>	
				</div>
				<div class="row">
					<div class="col4">
						<?php echo template::checkbox('i18AutoTranslation', true, 'Traduction automatique par '); ?>
						<a href="//policies.google.com/terms#toc-content" data-lity><img src="core/module/i18n/ressource/googtrans.png" /></a>
					</div>
				</div>	
			</div>
		</div>
	</div>

	<?php echo template::table([5,2,2,1], $module::$languages, ['Langues installées', 'Chemins des drapeaux', 'Traduction automatique' ,'']); ?>
</div>
<?php echo template::formClose(); ?>	
