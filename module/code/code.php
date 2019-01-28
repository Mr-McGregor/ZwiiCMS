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

class code extends common {

	public static $actions = [
		'config' => self::GROUP_ADMIN,
		'index' => self::GROUP_VISITOR
	];

	/**
	 * Configuration
	 */
	public function config() {
		// Soumission du formulaire
		if($this->isPost()) {
			$this->setData(['module', $this->getUrl(0), 'file', $this->getInput('codeConfigFile', helper::FILTER_URL, true)]);
			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . $this->getUrl(),
				'notification' => 'Modifications enregistrées',
				'state' => true
			]);
		}
		// Valeurs en sortie
		$this->addOutput([
			'title' => 'Configuration du module',
			'view' => 'config'
		]);
	}

	/**
	 * Accueil
	 */
	public function index() {
		// Message si l'utilisateur peut éditer la page
		if(
			$this->getUser('password') === $this->getInput('ZWII_USER_PASSWORD')
			AND $this->getUser('group') >= self::GROUP_ADMIN
			AND $this->getUrl(1) !== 'force'
		) {
			// Valeurs en sortie
			$this->addOutput([
				'display' => self::DISPLAY_LAYOUT_BLANK,
				'title' => '',
				'view' => 'index'
			]);
		}
		// Sinon code
		else {
			// Incrémente le compteur de clics
			$this->setData(['module', $this->getUrl(0), 'count', helper::filter($this->getData(['module', $this->getUrl(0), 'count']) + 1, helper::FILTER_INT)]);
			// Valeurs en sortie
			$this->addOutput([
				'content' => '<iframe id="modulecode" src="' . 
							   helper::baseUrl(false) . 
							   'site/file/source/' . 
							   $this->getData(['module', $this->getUrl(0), 'file']) . 
							   '"></iframe>' ,
				'state' => true
			]);
		}
	}

}