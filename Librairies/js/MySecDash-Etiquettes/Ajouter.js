// ======================================================================
// Mise en place des écoutes sur les événements produits par les objets.
$(function() {
	// Ajouter l'entité dans la base.
	$(".btn-ajouter").on('click', function(){
		ModalAjouterModifier(); // Fenêtre définie dans "lecture.js"
	});
});



// ============================================
// Fonctions répondant aux événements écoutés.


function AjouterEtiquette() {
	var Prefixe = 'TGS';

	var Code = $('#new_code').val();
	var Libelle = $('#new_libelle').val();
	var Description = $('#new_description').val();

	var total = $( '#totalOccurrences' ).text();
	total = Number(total) + 1;

	var e_ID, liste_IDN_a_ajouter = [];


	$('div#Lister_Utilisateurs input:checked').each( function( index, element ) {
		e_ID = $(element).attr('id');

		liste_IDN_a_ajouter.push( e_ID.split('_')[1] );
	});


	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter',
		type: 'POST',
		data: $.param({'liste_IDN_a_ajouter': liste_IDN_a_ajouter, 'code': Code, 'libelle': Libelle, 'description': Description}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];
			if ( statut == 'success' ) {

				$('#idModalAjouterModifier').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				// Vérifie s'il y a une limitation à la création des Utilisateurs.
				//gererBoutonAjouter( reponse['total'], reponse['limitation'], reponse['libelle_limitation'] );

				$( reponse[ 'texte' ] ).prependTo( '#corps_tableau' );

				$( '#totalOccurrences' ).text( ajouterZero( total ) );


				// Assigne l'événement "click" sur le bouton de Suppression
				if ( reponse[ 'droit_modifier' ] == 1 ) {
					$('#' + Prefixe + '_' + reponse[ 'id' ] + ' .btn-modifier').click( function( event ){
						var Id = $(this).attr('data-id');
						var Code = $(this).parent().parent().find('div[data-src="tgs_code"]').find('span').text();
						var Libelle = $(this).parent().parent().find('div[data-src="tgs_libelle"]').find('span').text();

						ModalAjouterModifier( Id, Code, Libelle );
					});
				}


				// Assigne l'événement "click" sur le bouton de Suppression
				if ( reponse[ 'droit_supprimer' ] == 1 ) {
					$('#' + Prefixe + '_' + reponse[ 'id' ] + ' .btn-supprimer').click(function(){
						var Id = $(this).attr('data-id');

						ModalSupprimer( Id );
					});
				}
			} else {
				afficherMessage( texteMsg, statut, '#idModalAjouterModifier', 0, 'n' );
			}
		}
	});
}
