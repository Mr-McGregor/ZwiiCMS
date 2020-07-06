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


/**
 * Confirmation de suppression
 */
$(".blogCommentDelete").on("click", function() {
	var _this = $(this);
	var nom = "<?php echo $this->getData(['module', $this->getUrl(0), $this->getUrl(2), 'title' ]); ?>";
	return core.confirm("Êtes-vous sûr de vouloir supprimer le commentaire de l'article " + nom + " ?", function() {
		$(location).attr("href", _this.attr("href"));
	});
});

/**
 * Confirmation de suppression en masse
 */
$(".blogCommentDeleteAll").on("click", function() {
	var _this = $(this);
	var nombre = "<?php echo count($this->getData(['module', $this->getUrl(0), $this->getUrl(2), 'comment' ])); ?>";
	var nom = "<?php echo $this->getData(['module', $this->getUrl(0), $this->getUrl(2), 'title' ]); ?>";
	if( nombre === "1"){
		var message = "Êtes-vous sûr de vouloir supprimer le commentaire de l'article " + nom + " ?";
	} else{
		var message = "Êtes-vous sûr de vouloir supprimer les " + nombre + " commentaires de l'article " + nom + " ?";
	}
	return core.confirm(message, function() {
		$(location).attr("href", _this.attr("href"));
	});
});
