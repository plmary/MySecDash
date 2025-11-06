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
	var app_nom = $('#app_nom').val();
	var app_nom_alias = $('#app_nom_alias').val();
	var ete_id_dima_dsi = $('#ete_id_dima_dsi').val();
	var libelle_ete_id_dima_dsi = $('#ete_id_dima_dsi option:selected').text();
	var scap_description_dima = $('#scap_description_dima').val();
	var ete_id_pdma_dsi = $('#ete_id_pdma_dsi').val();
	var libelle_ete_id_pdma_dsi = $('#ete_id_pdma_dsi option:selected').text();
	var scap_description_pdma = $('#scap_description_pdma').val();
	var frn_id = $('#frn_id').val();
	var sct_id = $('#sct_id').val();
	var frn_nom = $('#frn_id option:selected').text();
	var app_hebergement = $('#app_hebergement').val();
	var app_niveau_service = $('#app_niveau_service' ).val()
	var app_description = $('#app_description').val();

	var total = $( '#totalOccurrences' ).text();
	total = Number(total) + 1;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter',
		type: 'POST',
		data: $.param({'app_nom': app_nom, 'app_nom_alias': app_nom_alias, 'app_hebergement': app_hebergement,
			'ete_id_dima_dsi': ete_id_dima_dsi, 'libelle_ete_id_dima_dsi': libelle_ete_id_dima_dsi, 'scap_description_dima': scap_description_dima,
			'ete_id_pdma_dsi': ete_id_pdma_dsi, 'libelle_ete_id_pdma_dsi': libelle_ete_id_pdma_dsi, 'scap_description_pdma': scap_description_pdma,
			'app_niveau_service': app_niveau_service, 'app_description': app_description,
			'frn_id': frn_id, 'frn_nom': frn_nom, 'sct_id': sct_id}), // les paramètres sont protégés avant envoi
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
					$('#APP_' + reponse[ 'id' ] + ' .btn-modifier').click( function( event ){
						ModalAjouterModifier( reponse[ 'id' ] );
					});
				}

				// Assigne l'événement "click" sur le bouton de Suppression
				if ( reponse[ 'droit_supprimer' ] == true ) {
					$('#APP_' + reponse[ 'id' ] + ' .btn-supprimer').click(function(){
						var Libelle = $('#APP_'+reponse['id']).find('div[data-src="app_nom"]').find('span').text();

						ModalSupprimer( reponse['id'], Libelle );
					});
				}
			} else {
				afficherMessage( texteMsg, statut, '#idModal', 0, 'n' );
			}
		}
	});
}
