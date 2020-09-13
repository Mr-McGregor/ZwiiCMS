	// Paramètres d'initialisation
	$(document).ready(function() {

		// Ajouter la classe Gallery afin de faire la liaison avec simplelightbox
		$("a[rel='gallery']").addClass(
			"gallery",""
		);

		// Démarrer le script
		var a = new SimpleLightbox('.gallery', { 
			closeText:"&times;",
			captionsData:'alt' 
		});
		var b = new SimpleLightbox('.galleryGalleryPicture', { 
			captionSelector: "self",
			captionType: "data",
			captionsData: "caption",
			closeText: "&times;"
		});
	});
