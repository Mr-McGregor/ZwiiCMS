

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
 *
 **/ 

 if (document.referrer.indexOf("edit") === -1)
 {
  	core.confirm(
  		"OUI pour modifier la page et accéder aux statitiques de ce module.\r\n NON pour être redirigé vers l'URL saisie dans le module.",
   		function() {
  			$(location).attr("href", "<?php echo helper::baseUrl(); ?>page/edit/<?php echo $this->getUrl(0); ?>");
  		},
  		function() {
  			$(location).attr("href", "<?php echo helper::baseUrl() . $this->getUrl(); ?>/force");
  		}
  	);
  }
  else
  {
  	$(location).attr("href", "<?php echo helper::baseUrl(); ?>");
  }
