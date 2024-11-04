$(function() {
	// Assigne l'événement "click" sur tous les boutons de Modification
	$('.btn-modifier').click( function( event ){
		var ent_id = $(this).attr('data-id');

		ModalMAJEntite( ent_id );
	});
});



function ModifierEntite( ent_id ) {
	var ent_nom = $('#ent_nom').val();
	var ent_description = $('#ent_description').val();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'ent_id': ent_id, 'ent_nom': ent_nom, 'ent_description': ent_description}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];
			if ( statut == 'success' ) {

				$('#idModalEntite').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				$( '#ENT_' + ent_id + ' div[data-src="ent_nom"] span' ).text( ent_nom );
				$( '#ENT_' + ent_id + ' div[data-src="ent_description"] span' ).text( ent_description );
			} else {
				afficherMessage( texteMsg, statut, '#idModalEntite', 0, 'n' );
			}
		}
	});
}
