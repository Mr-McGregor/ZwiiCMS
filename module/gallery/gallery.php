<?php

/**
 * This file is part of Zwii.
 *
 * For full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 *
 * @author Rémi Jean <remi.jean@outlook.com>
 * @copyright Copyright (C) 2008-2018, Rémi Jean
 * @license GNU General Public License, version 3
 * @link http://zwiicms.com/
 */

class gallery extends common {

	const SORT_ASC = 'SORT_ASC';
	const SORT_DSC = 'SORT_DSC';
	const SORT_HAND = 'SORT_HAND';

	public static $actions = [
		'config' => self::GROUP_MODERATOR,
		'delete' => self::GROUP_MODERATOR,
		'dirs' => self::GROUP_MODERATOR,
		'sort' => self::GROUP_MODERATOR,
		'edit' => self::GROUP_MODERATOR,
		'index' => self::GROUP_VISITOR		
	];

	public static $sort = [
		self::SORT_ASC  => 'Alphabétique ',
		self::SORT_DSC  => 'Alphabétique inverse',
		self::SORT_HAND => 'Manuel'
	];

	public static $directories = [];

	public static $firstPictures = [];

	public static $galleries = [];

	public static $galleriesId = [];

	public static $pictures = [];

	public static $picturesId = [];

	public static $thumbs = [];

	const GALLERY_VERSION = '2.12';	


	/**
	 * Tri sans bouton
	 */
	public function sort () {
		if($_POST['response']) {
			$data = explode('&',$_POST['response']);
			$data = str_replace('galleryTable%5B%5D=','',$data);
			for($i=0;$i<count($data);$i++) {
				$this->setData(['module', $this->getUrl(0), $data[$i], [
					'config' => [
						'name' => $this->getData(['module',$this->getUrl(0),$data[$i],'config','name']),
						'directory' => $this->getData(['module',$this->getUrl(0),$data[$i],'config','directory']),
						'homePicture' => $this->getData(['module',$this->getUrl(0),$data[$i],'config','homePicture']),
						'sort' => $this->getData(['module',$this->getUrl(0),$data[$i],'config','sort']),
						'position' => $i
					],
					'legend' => $this->getData(['module',$this->getUrl(0),$data[$i],'legend'])
				]]);
			}	
		}
	}


	/**
	 * Configuration
	 */
	public function config() {
		//Affichage de la galerie triée
		$g = $this->getData(['module', $this->getUrl(0)]);
		$p = helper::arrayCollumn(helper::arrayCollumn($g,'config'),'position');
		asort($p,SORT_NUMERIC);		
		$galleries = [];
		foreach ($p as $positionId => $item) {
			$galleries [$positionId] = $g[$positionId];			
		}
		// Traitement de l'affichage
		if($galleries) {	
			foreach($galleries as $galleryId => $gallery) {
				// Erreur dossier vide
				if(is_dir($gallery['config']['directory'])) {
					if(count(scandir($gallery['config']['directory'])) === 2) {
						$gallery['config']['directory'] = '<span class="galleryConfigError">' . $gallery['config']['directory'] . ' (dossier vide)</span>';
					}
				}
				// Erreur dossier supprimé
				else {
					$gallery['config']['directory'] = '<span class="galleryConfigError">' . $gallery['config']['directory'] . ' (dossier introuvable)</span>';
				}
				// Met en forme le tableau
				self::$galleries[] = [	
					template::ico('sort'),				
					$gallery['config']['name'],
					$gallery['config']['directory'],
					template::button('galleryConfigEdit' . $galleryId , [
						'href' => helper::baseUrl() . $this->getUrl(0) . '/edit/' . $galleryId  . '/' . $_SESSION['csrf'],
						'value' => template::ico('pencil')
					]),
					template::button('galleryConfigDelete' . $galleryId, [
						'class' => 'galleryConfigDelete buttonRed',
						'href' => helper::baseUrl() . $this->getUrl(0) . '/delete/' . $galleryId . '/' . $_SESSION['csrf'],
						'value' => template::ico('cancel')
					])
				];
				// Tableau des id des galleries pour le drag and drop
				self::$galleriesId[] = $galleryId;
			}
		}
		// Soumission du formulaire d'ajout d'une galerie
		if($this->isPost()) {
			if (!$this->getInput('galleryConfigFilterResponse')) {
				$galleryId = helper::increment($this->getInput('galleryConfigName', helper::FILTER_ID, true), (array) $this->getData(['module', $this->getUrl(0)]));								
				// définir une vignette par défaut
				$directory = $this->getInput('galleryConfigDirectory', helper::FILTER_STRING_SHORT, true);
				$iterator = new DirectoryIterator($directory);				
				foreach($iterator as $fileInfos) {
					if($fileInfos->isDot() === false AND $fileInfos->isFile() AND @getimagesize($fileInfos->getPathname())) {						
						// Créer la miniature si manquante
						if (!file_exists( str_replace('source','thumb',$fileInfos->getPath()) . '/' . self::THUMBS_SEPARATOR  . strtolower($fileInfos->getFilename()))) {
							$this->makeThumb($fileInfos->getPathname(),
											str_replace('source','thumb',$fileInfos->getPath()) .  '/' . self::THUMBS_SEPARATOR  . strtolower($fileInfos->getFilename()),
											self::THUMBS_WIDTH);
						}
						// Miniatures 
						$homePicture = strtolower($fileInfos->getFilename());
					break;
					}
				}
				$this->setData(['module', $this->getUrl(0), $galleryId, [
					'config' => [
						'name' => $this->getInput('galleryConfigName'),
						'directory' => $this->getInput('galleryConfigDirectory', helper::FILTER_STRING_SHORT, true),
						'homePicture' => $homePicture,
						'sort' => self::SORT_ASC,
						'position' => count($this->getData(['module',$this->getUrl(0)])) + 1
					],
					'legend' => [],
					'position' => []
				]]);
				// Valeurs en sortie
				$this->addOutput([
					'redirect' => helper::baseUrl() . $this->getUrl() /*. '#galleryConfigForm'*/,
					'notification' => 'Modifications enregistrées',
					'state' => true
				]);
			}
		}
		// Valeurs en sortie
		$this->addOutput([
			'title' => 'Configuration du module',
			'view' => 'config',
			'vendor' => [
				'tablednd'
			]
		]);
	}

	/**
	 * Suppression
	 */
	public function delete() {
		// $url prend l'adresse sans le token	
		// La galerie n'existe pas
		if($this->getData(['module', $this->getUrl(0), $this->getUrl(2)]) === null) {
			// Valeurs en sortie
			$this->addOutput([
				'access' => false
			]);
		}
		// Jeton incorrect
		if ($this->getUrl(3) !== $_SESSION['csrf']) {
			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . $this->getUrl(0) . '/config',
				'notification' => 'Suppression  non autorisée'
			]);
		}		
		// Suppression
		else {
			$this->deleteData(['module', $this->getUrl(0), $this->getUrl(2)]);
			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . $this->getUrl(0) . '/config',
				'notification' => 'Galerie supprimée',
				'state' => true
			]);
		}
	}

	/**
	 * Liste des dossiers
	 */
	public function dirs() {
		// Valeurs en sortie
		$this->addOutput([
			'display' => self::DISPLAY_JSON,
			'content' => galleriesHelper::scanDir(self::FILE_DIR.'source')
		]);
	}

	/**
	 * Édition
	 */
	public function edit() {
		// Jeton incorrect
		if ($this->getUrl(3) !== $_SESSION['csrf']) {
			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . $this->getUrl(0) . '/config',
				'notification' => 'Action  non autorisée'
			]);
		}			
		// La galerie n'existe pas
		if($this->getData(['module', $this->getUrl(0), $this->getUrl(2)]) === null) {
			// Valeurs en sortie
			$this->addOutput([
				'access' => false
			]);
		}
		// La galerie existe
		else {
			// Soumission du formulaire
			if($this->isPost()) {
				/**
				 * $picturesPosition contient un tableau avec les images triées
				 */
				$picturesPosition = [];
				if ($this->getInput('galleryEditFormResponse') &&
					$this->getInput('galleryEditSort') === self::SORT_HAND) {
					// Tri des images si valeur de retour et choix manuel
					$picturesPosition = explode('&',($this->getInput('galleryEditFormResponse')));
					$picturesPosition = str_replace('galleryTable%5B%5D=','',$picturesPosition);	
					$picturesPosition = array_flip($picturesPosition);				
				}
				// Tri manuel sélectionné mais de déplacement, reprendre la config sauvegardée
				if ($this->getInput('galleryEditSort') === self::SORT_HAND &&
				   empty($picturesPosition)) {
					$picturesPosition  = $this->getdata(['module', $this->getUrl(0), $this->getUrl(2), 'position']);
					// Si la position sauvegardée est vide, on activera le tri alpha
				}
				// Si l'id a changée
				$galleryId = $this->getInput('galleryEditName', helper::FILTER_ID, true);
				if($galleryId !== $this->getUrl(2)) {
					// Incrémente le nouvel id de la galerie
					$galleryId = helper::increment($galleryId, $this->getData(['module', $this->getUrl(0)]));
					// Supprime l'ancienne galerie
					$this->deleteData(['module', $this->getUrl(0), $this->getUrl(2)]);
				}
				// légendes
				$legends = [];
				foreach((array) $this->getInput('legend', null) as $file => $legend) {
					// Image de couverure par défaut si non définie
					$homePicture = $file;
					$file = str_replace('.','',$file);
					$legends[$file] = helper::filter($legend, helper::FILTER_STRING_SHORT);

				}
				// Photo de la page de garde de l'album définie dans form
				if (is_array($this->getInput('homePicture', null)) ) {
					$d = array_keys($this->getInput('homePicture', null));
					$homePicture = $d[0];
				}
				// Sauvegarder
				$this->setData(['module', $this->getUrl(0), $galleryId, [
					'config' => [
						'name' => $this->getInput('galleryEditName', helper::FILTER_STRING_SHORT, true),
						'directory' => $this->getInput('galleryEditDirectory', helper::FILTER_STRING_SHORT, true),
						'homePicture' => $homePicture,
						// pas de positions, on active le tri alpha
						'sort' =>  (empty($picturesPosition) && $this->getInput('galleryEditSort') === self::SORT_HAND) ? self::SORT_ASC : $this->getInput('galleryEditSort'),
						'position' => $this->getData(['module', $this->getUrl(0), $galleryId,'config','position']) === '' ? count($this->getData(['module',$this->getUrl(0)]))-1 : $this->getData(['module', $this->getUrl(0), $galleryId,'config','position'])
					],
					'legend' => $legends,
					'position' => $picturesPosition
				]]);
				// Valeurs en sortie				
				$this->addOutput([
					'redirect' => helper::baseUrl() . $this->getUrl(0) . '/edit/' . $galleryId  . '/' . $_SESSION['csrf'] ,
					'notification' => 'Modifications enregistrées',
					'state' => true
				]);
			}
			// Met en forme le tableau
			$directory = $this->getData(['module', $this->getUrl(0), $this->getUrl(2), 'config', 'directory']);
			if(is_dir($directory)) {
				$iterator = new DirectoryIterator($directory);
				
				foreach($iterator as $fileInfos) {
					if($fileInfos->isDot() === false AND $fileInfos->isFile() AND @getimagesize($fileInfos->getPathname())) {
						// Créer la miniature RFM si manquante
						if (!file_exists( str_replace('source','thumb',$fileInfos->getPath()) . '/' . strtolower($fileInfos->getFilename()))) {
							$this->makeThumb($fileInfos->getPathname(),
											str_replace('source','thumb',$fileInfos->getPath()) .  '/' .  strtolower($fileInfos->getFilename()),
											122);
						}
						self::$pictures[str_replace('.','',$fileInfos->getFilename())] = [								
							template::ico('sort'),
							$fileInfos->getFilename(),
							template::checkbox( 'homePicture[' . $fileInfos->getFilename() . ']', true, '', [ 
								'checked' => $this->getData(['module', $this->getUrl(0), $this->getUrl(2),'config', 'homePicture']) === $fileInfos->getFilename() ? true : false,
								'class' => 'homePicture'
							]),	
							template::text('legend[' . $fileInfos->getFilename() . ']', [
								'value' => $this->getData(['module', $this->getUrl(0), $this->getUrl(2), 'legend', str_replace('.','',$fileInfos->getFilename())])
							]),
							'<a href="' . str_replace('source','thumb',$directory) . '/' . self::THUMBS_SEPARATOR . $fileInfos->getFilename() .'" rel="data-lity" data-lity=""><img src="'. str_replace('source','thumb',$directory) . '/' . $fileInfos->getFilename() .  '"></a>'
						];
						self::$picturesId [] = str_replace('.','',$fileInfos->getFilename());
					}
				}
				// Tri des images 		
				switch ($this->getData(['module', $this->getUrl(0), $this->getUrl(2), 'config', 'sort'])) {
					case self::SORT_HAND:
						$positions = $this->getdata(['module',$this->getUrl(0), $this->getUrl(2),'position']);
						if ($positions) {
							foreach ($positions as $key => $value) {
								if (array_key_exists($key,self::$pictures)) {
									$tempPictures[$key] = self::$pictures[$key];
									$tempPicturesId [] = $key;
								}
							}	
							// Images ayant été ajoutées dans le dossier mais non triées
							foreach (self::$pictures as $key => $value) {
								if (!array_key_exists($key,$tempPictures)) {
									$tempPictures[$key] = self::$pictures[$key];
									$tempPicturesId [] = $key;
								}
							}
							self::$pictures = $tempPictures;
							self::$picturesId  = $tempPicturesId;
						}
						break;
					case self::SORT_ASC:
						ksort(self::$pictures,SORT_NATURAL);
						sort(self::$picturesId,SORT_NATURAL);
						break;						
					case self::SORT_DSC:
						krsort(self::$pictures,SORT_NATURAL);
						rsort(self::$picturesId,SORT_NATURAL);
						break;													
				}	
			}
			// Valeurs en sortie
			$this->addOutput([
				'title' => $this->getData(['module', $this->getUrl(0), $this->getUrl(2), 'config', 'name']),
				'view' => 'edit',
				'vendor' => [
					'tablednd'
				]
			]);
		}
	}

	/**
	 * Accueil (deux affichages en un pour éviter une url à rallonge)
	 */
	public function index() {
		// Images d'une galerie
		if($this->getUrl(1)) {
			// La galerie n'existe pas
			if($this->getData(['module', $this->getUrl(0), $this->getUrl(1)]) === null) {
				// Valeurs en sortie
				$this->addOutput([
					'access' => false
				]);
			}
			// La galerie existe
			else {
				// Images de la galerie
				$directory = $this->getData(['module', $this->getUrl(0), $this->getUrl(1), 'config', 'directory']);			
				if(is_dir($directory)) {
					$iterator = new DirectoryIterator($directory);
					foreach($iterator as $fileInfos) {
						if($fileInfos->isDot() === false AND $fileInfos->isFile() AND @getimagesize($fileInfos->getPathname())) {
							self::$pictures[$directory . '/' . $fileInfos->getFilename()] = $this->getData(['module', $this->getUrl(0), $this->getUrl(1), 'legend', str_replace('.','',$fileInfos->getFilename())]);							
							$picturesSort[$directory . '/' . $fileInfos->getFilename()] = $this->getData(['module', $this->getUrl(0), $this->getUrl(1), 'position', str_replace('.','',$fileInfos->getFilename())]);
							// Créer la miniature si manquante
							if (!file_exists( str_replace('source','thumb',$fileInfos->getPath()) . '/' . self::THUMBS_SEPARATOR  . strtolower($fileInfos->getFilename()))) {
								$this->makeThumb($fileInfos->getPathname(),
												str_replace('source','thumb',$fileInfos->getPath()) .  '/' . self::THUMBS_SEPARATOR  . strtolower($fileInfos->getFilename()),
												self::THUMBS_WIDTH);
							}							
							// Définir la Miniature
							self::$thumbs[$directory . '/' . $fileInfos->getFilename()] = 	file_exists( str_replace('source','thumb',$directory) . '/' . self::THUMBS_SEPARATOR  . strtolower($fileInfos->getFilename())) 
																							? str_replace('source','thumb',$directory) . '/' . self::THUMBS_SEPARATOR .  strtolower($fileInfos->getFilename())
																							: str_replace('source','thumb',$directory) . '/' .  strtolower($fileInfos->getFilename());
						}
					}
					// Tri des images par ordre alphabétique
					switch ($this->getData(['module', $this->getUrl(0), $this->getUrl(1), 'config', 'sort'])) {
						case self::SORT_HAND:
							asort($picturesSort);
							if ($picturesSort) {
								foreach ($picturesSort as $name => $position) {
									$temp[$name] = self::$pictures[$name];
								}				
								self::$pictures = $temp;
								break;
							}
						case self::SORT_DSC:
							krsort(self::$pictures,SORT_NATURAL);
							break;													
						case self::SORT_ASC:
						default:
							ksort(self::$pictures,SORT_NATURAL);
							break;
					}					
				}
				// Affichage du template
				if(self::$pictures) {
					// Valeurs en sortie
					$this->addOutput([
						'showBarEditButton' => true,
						'title' => $this->getData(['module', $this->getUrl(0), $this->getUrl(1), 'config', 'name']),
						'view' => 'gallery'
					]);
				}
				// Pas d'image dans la galerie
				else {
					// Valeurs en sortie
					$this->addOutput([
						'access' => false
					]);
				}
			}

		}
		// Liste des galeries
		else {
			// Tri des galeries suivant l'ordre défini
			$g = $this->getData(['module', $this->getUrl(0)]);
			$p = helper::arrayCollumn(helper::arrayCollumn($g,'config'),'position');
			asort($p,SORT_NUMERIC);		
			$galleries = [];
			foreach ($p as $positionId => $item) {
				$galleries [$positionId] = $g[$positionId];			
			}
			// Construire le tableau
			foreach((array) $galleries as $galleryId => $gallery) {
				if(is_dir($gallery['config']['directory'])) {
					$iterator = new DirectoryIterator($gallery['config']['directory']);
					foreach($iterator as $fileInfos) {
						if($fileInfos->isDot() === false AND $fileInfos->isFile() AND @getimagesize($fileInfos->getPathname())) {
							self::$galleries[$galleryId] = $gallery;
							// L'image de couverture est-elle supprimée ?
							if (file_exists( $gallery['config']['directory'] . '/' . $gallery['config']['homePicture'])) {
								// Créer la miniature si manquante
								if (!file_exists( str_replace('source','thumb',$gallery['config']['directory']) . '/' . self::THUMBS_SEPARATOR  . strtolower($gallery['config']['homePicture']))) {
									$this->makeThumb($gallery['config']['directory'] . '/' . str_replace(self::THUMBS_SEPARATOR ,'',$gallery['config']['homePicture']),
													str_replace('source','thumb',$gallery['config']['directory']) .  '/' . self::THUMBS_SEPARATOR  . strtolower($gallery['config']['homePicture']),
													self::THUMBS_WIDTH);
								}	
								// Définir l'image de couverture
								self::$firstPictures[$galleryId] =	file_exists( str_replace('source','thumb',$gallery['config']['directory']) . '/' . self::THUMBS_SEPARATOR  . strtolower($gallery['config']['homePicture']))
																	? str_replace('source','thumb',$gallery['config']['directory']) . '/' . self::THUMBS_SEPARATOR .  strtolower($gallery['config']['homePicture'])
																	: str_replace('source','thumb',$gallery['config']['directory']) . '/' .  strtolower($gallery['config']['homePicture']);
							} else {							
								// homePicture contient une image invalide, supprimée ou déplacée
								// Définir l'image de couverture, première image disponible
								$this->makeThumb($fileInfos->getPath() . '/' . $fileInfos->getFilename(),
												str_replace('source','thumb',$fileInfos->getPath()) .  '/' . self::THUMBS_SEPARATOR  . strtolower($fileInfos->getFilename()),
												self::THUMBS_WIDTH);
								self::$firstPictures[$galleryId] =	file_exists( str_replace('source','thumb',$fileInfos->getPath()) . '/' . self::THUMBS_SEPARATOR  . strtolower($fileInfos->getFilename()))
																	? str_replace('source','thumb',$fileInfos->getPath()) . '/' . self::THUMBS_SEPARATOR .  strtolower($fileInfos->getFilename())
																	: str_replace('source','thumb',$fileInfos->getPath()) . '/' .  strtolower($fileInfos->getFilename());
							}
						} 
						continue(2);
					}
				}
			}
			// Valeurs en sortie
			$this->addOutput([
				'showBarEditButton' => true,
				'showPageContent' => true,
				'view' => 'index'
			]);
		}
	}

}

class galleriesHelper extends helper {

	/**
	 * Scan le contenu d'un dossier et de ses sous-dossiers
	 * @param string $dir Dossier à scanner
	 * @return array
	 */
	public static function scanDir($dir) {
		$dirContent = [];
		$iterator = new DirectoryIterator($dir);
		foreach($iterator as $fileInfos) {
			if($fileInfos->isDot() === false AND $fileInfos->isDir()) {
				$dirContent[] = $dir . '/' . $fileInfos->getBasename();
				$dirContent = array_merge($dirContent, self::scanDir($dir . '/' . $fileInfos->getBasename()));
			}
		}
		return $dirContent;
	}
}