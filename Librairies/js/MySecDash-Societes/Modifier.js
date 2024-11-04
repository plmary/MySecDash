$(function() {
	// Assigne l'événement "click" sur tous les boutons de Modification
	$('.btn-modifier').click( function( event ){
		var Id = $(this).attr('data-id');

		ModalMaJSociete(Id);
	});
});


function ModifierSociete(sct_id) {
	var sct_nom = $('#sct_nom').val();
	var sct_description = $('#sct_description').val();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'sct_id': sct_id, 'sct_description': sct_description, 'sct_nom': sct_nom}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];
			if ( statut == 'success' ) {

				$('#idModalSociete').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				$('#SCT_'+sct_id+' div span').text(sct_nom);
			} else {
				afficherMessage( texteMsg, statut, '#idModalSociete', 0, 'n' );
			}
		}
	});
}
