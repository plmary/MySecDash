// ======================================================================
// Mise en place des écoutes sur les événements produits par les objets.
$(function() {
	// Ajouter l'entité dans la base.
	$(".btn-ajouter").on('click', function(){
		ModalAjouterModifier();
	});
});



// ============================================
// Fonctions répondant aux événements écoutés.

function AjouterFournisseur() {
	var frn_nom = $('#frn_nom').val();
	var tfr_id = $('#tfr_id').val();
	var frn_description = $('#frn_description').val();

	var total = $( '#totalOccurrences' ).text();
	total = Number(total) + 1;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter',
		type: 'POST',
		data: $.param({'frn_nom': frn_nom, 'tfr_id': tfr_id, 'frn_description': frn_description}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				$( reponse[ 'texte' ] ).prependTo( '#corps_tableau' );
				$( '#totalOccurrences' ).text( ajouterZero( total ) );

				// Assigne l'événement "click" sur le bouton de Modification
				if ( reponse[ 'droit_modifier' ] == true ) {
					$('#FRN_' + reponse[ 'id' ] + ' .btn-modifier').click( function( event ){
						ModalAjouterModifier( reponse[ 'id' ] );
					});
				}

				// Assigne l'événement "click" sur le bouton de Suppression
				if ( reponse[ 'droit_supprimer' ] == true ) {
					$('#FRN_' + reponse[ 'id' ] + ' .btn-supprimer').click(function(){
						var Libelle = $('#FRN_'+reponse['id']).find('div[data-src="frn_nom"]').find('span').text();

						ModalSupprimer( reponse['id'], Libelle );
					});
				}
			} else {
				afficherMessage( texteMsg, statut, '#idModal', 0, 'n' );
			}
		}
	});
}
