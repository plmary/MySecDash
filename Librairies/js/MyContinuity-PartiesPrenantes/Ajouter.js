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

function AjouterPartiePrenante() {
	var ppr_nom = $('#ppr_nom').val();
	var ppr_prenom = $('#ppr_prenom').val();
	var ppr_interne = $('#ppr_interne').val();
	var ppr_description = $('#ppr_description').val();

	var total = $( '#totalOccurrences' ).text();
	total = Number(total) + 1;

	ppr_nom = ppr_nom.toUpperCase();
	ppr_prenom = ppr_prenom[0].toUpperCase()+ppr_prenom.substring(1);

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter',
		type: 'POST',
		data: $.param({'ppr_nom': ppr_nom, 'ppr_prenom': ppr_prenom, 'ppr_interne': ppr_interne,
			'ppr_description': ppr_description}), // les paramètres sont protégés avant envoi
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
					$('#PPR_' + reponse[ 'id' ] + ' .btn-modifier').click( function( event ){
						ModalAjouterModifier( reponse[ 'id' ] );
					});
				}

				// Assigne l'événement "click" sur le bouton de Suppression
				if ( reponse[ 'droit_supprimer' ] == true ) {
					$('#PPR_' + reponse[ 'id' ] + ' .btn-supprimer').click(function(){
						var Libelle = ppr_nom + ' ' + ppr_prenom;
						if (ppr_description != '') {
							Libelle += ' (' + ppr_description + ')';
						}

						ModalSupprimer( reponse['id'], Libelle );
					});
				}
			} else {
				afficherMessage( texteMsg, statut, '#idModal', 0, 'n' );
			}
		}
	});
}
