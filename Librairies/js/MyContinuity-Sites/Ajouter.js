// ======================================================================
// Mise en place des écoutes sur les événements produits par les objets.
$(function() {
	// Ajouter l'entité dans la base.
	$(".btn-ajouter").on('click', function(){
		ModalMAJ();
	});
});



function AjouterSite() {
	var sts_nom = $('#sts_nom').val();
	var sts_description = $('#sts_description').val();

	var total = $( '#totalOccurrences' ).text();
	total = Number(total) + 1;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter',
		type: 'POST',
		data: $.param({'sts_nom': sts_nom, 'sts_description': sts_description}), // les paramètres sont protégés avant envoi
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
					$('#STS_' + reponse[ 'sts_id' ] + ' button.btn-modifier').click(function(event){
						ModalMAJ( reponse[ 'sts_id' ] );
					});
				}

				// Assigne l'événement "click" sur le bouton de Suppression
				if ( reponse[ 'droit_supprimer' ] == true ) {
					$('#STS_' + reponse[ 'sts_id' ] + ' button.btn-supprimer').click(function(){
						ModalSupprimer( reponse[ 'sts_id' ], sts_nom );
					});
				}
			} else {
				afficherMessage( texteMsg, statut, '#idModal', 0, 'n' );
			}
		}
	});
}
