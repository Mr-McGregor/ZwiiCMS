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
 * @Author 23/9/18 Frédéric Tempez <frederic.tempez@outlook.com>
 */

/**
 * Aperçu en direct
 */
$("input, select").on("change", function() {
	// Couleurs du pied de page
	var colors = core.colorVariants($("#themeFooterBackgroundColor").val());
	var textColor = $("#themeFooterTextColor").val();
	var css = "footer{background-color:" + colors.normal + ";color:" + textColor + "}";
	css += "footer a{color:" + textColor + "}";
	// Hauteur du pied de page
	css += "footer .container > div{margin:" + $("#themeFooterHeight").val() + " 0}";
	css += "footer .container-large > div{margin:" + $("#themeFooterHeight").val() + " 0}";
	// Alignement du contenu
	css += "#footerSocials{text-align:" + $("#themeFooterSocialsAlign").val() + "}";
	css += "#footerText{text-align:" + $("#themeFooterTextAlign").val() + "}";
	css += "#footerCopyright{text-align:" + $("#themeFooterCopyrightAlign").val() + "}";
	// Marge
	if($("#themeFooterMargin").is(":checked")) {
		css += 'footer{margin:0 20px 20px}';
	}
	else {
		css += 'footer{margin:0}';
	}
	// Ajout du css au DOM
	$("#themePreview").remove();
	$("<style>")
		.attr("type", "text/css")
		.attr("id", "themePreview")
		.text(css)
		.appendTo("head");
	// Position du pied de page
	switch($("#themeFooterPosition").val()) {
		case 'hide':
			$("footer").hide();
			break;
		case 'site':
			$("footer").show().appendTo("#site");
			break;
		case 'body':
			$("footer").show().appendTo("body");
			break;
	}
});

// Position dans les blocs FT

// Bloc texte personnalisé

$("#themeFooterForm").on("change",function() {
	switch($("#themeFooterTextPosition").val()) {
			case 'hide':
				$("#footerText").hide();
				break;
			case 'left':
				$("#footerText").show().appendTo("#bodyLeft");			
				$("#footerText").show().appendTo("#siteLeft");
				break;
			case 'center':
				$("#footerText").show().appendTo("#bodyCenter");
				$("#footerText").show().appendTo("#siteCenter");
				break;
			case 'right':
				$("#footerText").show().appendTo("#bodyRight");
				$("#footerText").show().appendTo("#siteRight");				
				break;
	}
	switch($("#themeFooterSocialsPosition").val()) {
			case 'hide':
				$("#footerSocials").hide();
				break;		
			case 'left':
				$("#footerSocials").show().appendTo("#bodyLeft");			
				$("#footerSocials").show().appendTo("#siteLeft");
				break;
			case 'center':
				$("#footerSocials").show().appendTo("#bodyCenter");
				$("#footerSocials").show().appendTo("#siteCenter");
				break;
			case 'right':
				$("#footerSocials").show().appendTo("#bodyRight");
				$("#footerSocials").show().appendTo("#siteRight");				
				break;
	}
		switch($("#themeFooterCopyrightPosition").val()) {
			case 'hide':
				$("#footerCopyright").hide();
				break;		
			case 'left':
				$("#footerCopyright").show().appendTo("#bodyLeft");			
				$("#footerCopyright").show().appendTo("#siteLeft");
				break;
			case 'center':
				$("#footerCopyright").show().appendTo("#bodyCenter");
				$("#footerCopyright").show().appendTo("#siteCenter");
				break;
			case 'right':
				$("#footerCopyright").show().appendTo("#bodyRight");
				$("#footerCopyright").show().appendTo("#siteRight");				
				break;
	}
}).trigger("change");


// Fin Position dans les blocs




// Lien de connexion
$("#themeFooterLoginLink").on("change", function() {
	if($(this).is(":checked")) {
		$("#footerLoginLink").show();
	}
	else {
		$("#footerLoginLink").hide();
	}
}).trigger("change");
// Aperçu du texte
$("#themeFooterText").on("change keydown keyup", function() {
	$("#footerText").html($(this).val());
});
// Affiche / Cache les options de la position
$("#themeFooterPosition").on("change", function() {
	if($(this).val() === 'site') {
		$("#themeFooterPositionOptions").slideDown();
	}
	else {
		$("#themeFooterPositionOptions").slideUp(function() {
			$("#themeFooterMargin").prop("checked", false).trigger("change");
		});
	}
}).trigger("change");