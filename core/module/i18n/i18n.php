<?php

/**
 * This file is part of Zwii.
 *
 * For full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 *
 * @author Frédéric Tempez <frederic.tempez@outlook.com>
 * @copyright Copyright (C) 2018-2020, Frédéric Tempez
 * @license GNU General Public License, version 3
 * @link http://zwiicms.com/
 */

class i18n extends common {

	public static $actions = [
		'index' => self::GROUP_MODERATOR,
		'delete' => self::GROUP_MODERATOR,
		'lang' => self::GROUP_VISITOR
	];
	public static $languages = [];


    /**
	 * Config : gestion des langues
	 */
	public function index() { 
		if ($this->isPost()) {
			// Mode ajout de langue
			// Récupérer les données du formulaire
			if (!empty ($this->getInput('i18nLanguageAdd'))) { // Formulaire valide
				$create = $this->getInput('i18nLanguageAdd');			
				$copyFrom = $this->getInput('i18nLanguageCopyFrom');
				// Mode création de langue
				// La langue est déja créée ?
				if (in_array($create,$this->i18nInstalled()) === false) { // La langue n'est pas installée ?
					//Déterminer l'origine des données copiées
					$copyFrom = $copyFrom === '' ? 'core/module/i18n/ressource/' : self::DATA_DIR . $copyFrom . '/';
					// Créer le dossier
					if (is_dir(self::DATA_DIR . $create) === false ) { // Si le dossier est déjà créé
						$success  = mkdir (self::DATA_DIR . $create);
					} else {
						$success = true;
					}				
					// Copier les données par défaut avec gestion des erreurs
					$success  = (copy ($copyFrom . 'module.json', self::DATA_DIR . $this->getInput('i18nLanguageAdd') . '/module.json') === true && $success  === true) ? true : false;
					$success  = (copy ($copyFrom . 'page.json', self::DATA_DIR . $this->getInput('i18nLanguageAdd') . '/page.json') === true && $success  === true) ? true : false;
					// Enregistrement des données de langue dans la config
					// Chemin des images
					$this->setData(['config','i18n',$create,'flagFolder',$this->geti18nFlagFolder($create)]);
					$this->setData(['config','i18n',$create,'autoTranslate',$this->getInput('i18AutoTranslation',helper::FILTER_BOOLEAN)]);
				} else {
					$notification = $create . ' est déjà ajoutée.';
					$success =  false;
				}
				// Valeurs en sortie
				$notification = $success === true ? self::$i18nList[$this->getInput('i18nLanguageAdd')] . ' ajouté' : self::$i18nList[create] . ' déjà ajouté.' ;
			} else {
				$notification = 'Veuillez choisir une langue.';
				$success =  false;
			}
			$this->addOutput([
				'notification'  =>  $notification,
				'title' 		=> 'Gestion des langues',
				'view' 			=> 'index',
				'state' 		=>  $success
			]);
		// Fin traitement du formulaire
		}
		// Affichage par défaut			
		$langIds = $this->i18nInstalled();
		asort($langIds);
		foreach($langIds as $itemKeyLang => $itemLang) {
			self::$languages[] = [
				$itemLang,
				stripslashes($this->getData(['config','i18n',$itemKeyLang,'flagFolder'])),
				$this->getData(['config','i18n',$itemKeyLang,'autoTranslate']) === true ? 'Oui' : 'Non',
				template::button('i18nDelete' . $itemKeyLang, [
					'class' => 'i18nDelete buttonRed',
					'href' => helper::baseUrl() . 'i18n/delete/' . $itemKeyLang. '/' . $_SESSION['csrf'],
					'value' => template::ico('cancel')
				])
			];
		}
		// Valeurs en sortie
		$this->addOutput([
			'title' => 'Gestion des langues',
			'view' => 'index'
		]);
	}


	/* Effacer une langue 
	*
	*/
	public function delete() {
		// Jeton incorrect
		if ($this->getUrl(3) !== $_SESSION['csrf']) {
			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . 'i18n',
				'notification' => 'Action non autorisée'
			]);
		} elseif ( $this->getUrl(2) === $this->geti18n()) {
			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . 'i18n',
				'notification' => 'Vous ne pouvez pas supprimer la langue courante.'
			]);
		} elseif ( $this->getUrl(2) === 'fr') {
			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . 'i18n',
				'notification' => 'Vous ne pouvez pas supprimer la langue par défaut.'
			]);
		} else {
			// Le dossier existe  ?
			if (is_dir(self::DATA_DIR . $this->getUrl(2)) === true) {
				$success  = unlink (self::DATA_DIR . $this->getUrl(2) . '/module.json');
				$success  = (unlink (self::DATA_DIR . $this->getUrl(2) . '/page.json') && $success  === true) ? true : false ;
				$success  = (rmdir (self::DATA_DIR . $this->getUrl(2)) === true  && $success  === true) ? true : false ;
				// Effacer les données de config
				$this->deleteData(['config','i18n',$this->getUrl(2)]);

			} else {
				$success = false;
			}
			// Valeurs en sortie
			$notification = $success === true ?  self::$i18nList[$this->getUrl(2)] .' supprimé' : self::$i18nList[$this->getUrl(2)] . ' n\'existe pas.' ;
			$this->addOutput([
				'notification' => $notification,
				'redirect' => helper::baseUrl() . 'i18n',
				'state' 	=> $success		
			]);			
		}
	}


		/*
	* Traitement du changement de langues
	*/
	public function lang() {
		if ( isset($_POST)) {
			// Clic dans le menu par l'utilisateur
			if (strlen(array_keys($_POST)[0]) === 4) {
				$lan = substr(array_keys($_POST)[0],0,2);
			// Clic dans le menu de la barre d'administration
			} elseif (  isset($_POST['i18nSelect'])) {
				$lan = $_POST['i18nSelect'];
			}
		}
		// Traitement du changement de langue
		if (isset($lan)) {
			$this->seti18n($lan);
			// Valeurs en sortie sans post
			$this->addOutput([
				'redirect' 		=> 	helper::baseUrl(false),
				'state'			=> true
			]);							
		} else {
			$this->addOutput([
				'redirect' 		=> 	helper::baseUrl(false)
			]);	
		}	
	}
}
