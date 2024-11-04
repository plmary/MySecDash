// ======================================================================
// Mise en place des écoutes sur les événements produits par les objets.
$(function() {
	// Ajouter l'entité dans la base.
	$(".btn-ajouter").on('click', function(){
		ModalMaJSociete();
	});
});



// ============================================
// Fonctions répondant aux événements écoutés.

function AjouterSociete() {
	var sct_nom = $('#sct_nom').val();
	var sct_description = $('#sct_description').val();

	var total = $( '#totalOccurrences' ).text();
	total = Number(total) + 1;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter',
		type: 'POST',
		data: $.param({'sct_description': sct_description, 'sct_nom': sct_nom}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];
			if ( statut == 'success' ) {

				$('#idModalSociete').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				var old_title = $('div#titre_ecran button.btn-ajouter').attr('data-old_title');

				$('div#titre_ecran button.btn-ajouter').removeAttr('disabled').attr('title', old_title);

				$( reponse[ 'texte' ] ).prependTo( '#corps_tableau' );

				$( '#totalOccurrences' ).text( ajouterZero( total ) );

				// Assigne l'événement "click" sur le bouton de Modification
				if ( reponse[ 'droit_modifier' ] == true ) {
					$('#SCT_' + reponse[ 'id' ]).find('button.btn-modifier').click(function(event){
						ModalMaJSociete( reponse[ 'id' ] );
					});
				}

				// Assigne l'événement "click" sur le bouton de Suppression
				if ( reponse[ 'droit_supprimer' ] == true ) {
					$('#SCT_' + reponse[ 'id' ] + ' .btn-supprimer').click(function(){
						ModalSupprimer( reponse[ 'id' ] );
					});
				}
			} else {
				afficherMessage( texteMsg, statut, '#idModalSociete', 0, 'n' );
			}
		}
	});
}
