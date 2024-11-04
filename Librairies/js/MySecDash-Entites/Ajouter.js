// ======================================================================
// Mise en place des écoutes sur les événements produits par les objets.
$(function() {
	// Ajouter l'entité dans la base.
	$(".btn-ajouter").on('click', function(){
		ModalMAJEntite();
	});
});



function AjouterEntite() {
	var ent_nom = $('#ent_nom').val();
	var ent_description = $('#ent_description').val();

	var total = $( '#totalOccurrences' ).text();
	total = Number(total) + 1;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter',
		type: 'POST',
		data: $.param({'ent_nom': ent_nom, 'ent_description': ent_description}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];
			if ( statut == 'success' ) {

				$('#idModalEntite').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				// Vérifie s'il y a une limitation à la création des Entités.
				if ( total >= reponse['limitation'] && reponse['limitation'] != 0 ) {
					var old_title = $('div#titre_ecran button.btn-ajouter').attr('title');

					$('div#titre_ecran button.btn-ajouter').attr('disabled', 'disabled').attr('title', reponse['libelle_limitation']).attr('data-old_title', old_title);
				} else {
					var old_title = $('div#titre_ecran button.btn-ajouter').attr('data-old_title');

					$('div#titre_ecran button.btn-ajouter').removeAttr('disabled').attr('title', old_title);
				}

				$( reponse[ 'texte' ] ).prependTo( '#corps_tableau' );

				$( '#totalOccurrences' ).text( ajouterZero( total ) );

				// Assigne l'événement "click" sur le bouton de Modification
				if ( reponse[ 'droit_modifier' ] == true ) {
					$('#ENT_' + reponse[ 'id' ]).find('button.btn-modifier').click(function(event){
						ModalMAJEntite( reponse[ 'id' ] );
					});
				}

				// Assigne l'événement "click" sur le bouton de Suppression
				if ( reponse[ 'droit_supprimer' ] == true ) {
					$('#ENT_' + reponse[ 'id' ] + ' .btn-supprimer').click(function(){
						ModalSupprimer( reponse[ 'id' ], ent_nom );
					});
				}
			} else {
				afficherMessage( texteMsg, statut, '#idModalEntite', 0, 'n' );
			}
		}
	});
}
