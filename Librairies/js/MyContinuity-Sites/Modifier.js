$(function() {
	// Assigne l'événement "click" sur tous les boutons de Modification
	$('.btn-modifier').click( function( event ){
		var id = $(this).attr('data-id');

		ModalMAJ( id );
	});
});



function ModifierSite( sts_id ) {
	var sts_nom = $('#sts_nom').val();
	var sts_description = $('#sts_description').val();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'sts_id': sts_id, 'sts_nom': sts_nom, 'sts_description': sts_description}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];
			if ( statut == 'success' ) {

				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				$( '#STS_' + sts_id + ' div[data-src="sts_nom"] span' ).text( sts_nom );
				$( '#STS_' + sts_id + ' div[data-src="sts_description"] span' ).text( sts_description );
			} else {
				afficherMessage( texteMsg, statut, '#idModal', 0, 'n' );
			}
		}
	});
}
