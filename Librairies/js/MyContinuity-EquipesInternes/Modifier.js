$(function() {
	// Assigne l'événement "click" sur tous les boutons de Modification
	$('.btn-modifier').click( function( event ){
		var ein_id = $(this).attr('data-id');

		ModalMAJEquipe( ein_id );
	});
});



function ModifierEquipe( ein_id ) {
	var ein_nom = $('#ein_nom').val();
	var ein_description = $('#ein_description').val();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'ein_id': ein_id, 'ein_nom': ein_nom, 'ein_description': ein_description}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];
			if ( statut == 'success' ) {

				$('#idModalEquipe').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				$( '#EIN_' + ein_id + ' div[data-src="ein_nom"] span' ).text( ein_nom );
				$( '#EIN_' + ein_id + ' div[data-src="ein_description"] span' ).text( ein_description );
			} else {
				afficherMessage( texteMsg, statut, '#idModalEquipe', 0, 'n' );
			}
		}
	});
}
