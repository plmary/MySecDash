// ======================================================================
// Mise en place des écoutes sur les événements produits par les objets.
$(function() {
	// Ajouter l'entité dans la base.
	$(".btn-ajouter").on('click', function(){
		ModalMAJ();
	});
});



// ============================================
// Fonctions répondant aux événements écoutés.

function AjouterCivilite() {
	var Last_Name = $('#cvl_nom').val();
	var First_Name = $('#cvl_prenom').val();

	var total = $( '#totalOccurrences' ).text();
	total = Number(total) + 1;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter',
		type: 'POST',
		data: $.param({'last_name': Last_Name,'first_name': First_Name}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModalCivilite').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				$( reponse[ 'texte' ] ).prependTo( '#corps_tableau' );
				$( '#totalOccurrences' ).text( ajouterZero( total ) );

				 // Vérifie s'il y a une limitation à la création des Entités.
				if ( total >= reponse['limitation'] && reponse['limitation'] != 0 ) {
					var old_title = $('div#titre_ecran button.btn-ajouter').attr('title');
					$('div#titre_ecran button.btn-ajouter').attr('data-old_title', old_title );

					$('div#titre_ecran button.btn-ajouter').attr('disabled', 'disabled').attr('title', reponse['libelle_limitation']);
				} else {
					var old_title = $('div#titre_ecran button.btn-ajouter').attr('data-old_title');

					$('div#titre_ecran button.btn-ajouter').removeAttr('disabled').attr('title', old_title);
				}

			   // Assigne l'événement "click" sur le bouton de Modification
				if ( reponse[ 'droit_modifier' ] == 1 ) {
					$('#CVL_' + reponse[ 'id' ] + ' .btn-modifier').click( function( event ){
						ModalMAJ( reponse[ 'id' ] );
					});
				}

				// Assigne l'événement "click" sur le bouton de Suppression
				if ( reponse[ 'droit_supprimer' ] == 1 ) {
					$('#CVL_' + reponse[ 'id' ] + ' .btn-supprimer').click(function(){
						ModalSupprimer( reponse[ 'id' ], Last_Name, First_Name );
					});
				}
			} else {
				afficherMessage( texteMsg, statut, '#idModalCivilite', 0, 'n' );
			}
		}
	});
}
