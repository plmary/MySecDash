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

function AjouterApplication() {
	var ain_libelle = $('#ain_libelle').val();
	var tap_id = $('#tap_id').val();
	var ain_localisation = $('#ain_localisation').val();

	var total = $( '#totalOccurrences' ).text();
	total = Number(total) + 1;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter',
		type: 'POST',
		data: $.param({'ain_libelle': ain_libelle,'tap_id': tap_id,'ain_localisation': ain_localisation}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModalApplication').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				$( reponse[ 'texte' ] ).prependTo( '#corps_tableau' );
				$( '#totalOccurrences' ).text( ajouterZero( total ) );

				// Assigne l'événement "click" sur le bouton de Modification
				if ( reponse[ 'droit_modifier' ] == true ) {
					$('#AIN_' + reponse[ 'id' ] + ' .btn-modifier').click( function( event ){
						ModalAjouterModifier( reponse[ 'id' ] );
					});
				}

				// Assigne l'événement "click" sur le bouton de Suppression
				if ( reponse[ 'droit_supprimer' ] == true ) {
					$('#AIN_' + reponse[ 'id' ] + ' .btn-supprimer').click(function(){
						var Libelle = $('#AIN_'+reponse['id']).find('div[data-src="ain_libelle"]').find('span').text();
						var Type = $('#AIN_'+reponse['id']).find('div[data-src="tap_code"]').find('span').text();
						var Localisation = $('#AIN_'+reponse['id']).find('div[data-src="ain_localisation"]').find('span').text();

						ModalSupprimer( reponse['id'], Libelle, Type, Localisation );
					});
				}
			} else {
				afficherMessage( texteMsg, statut, '#idModalApplication', 0, 'n' );
			}
		}
	});
}
