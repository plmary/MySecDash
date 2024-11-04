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


function AjouterGestionnaire() {
	var Prefixe = 'GST';

	var Libelle = $('#new_libelle').val();

	var total = $( '#totalOccurrences' ).text();
	total = Number(total) + 1;

	var e_ID, liste_TSP_a_ajouter = [], liste_IDN_a_ajouter = [];


	$('div#Lister_Types_Supports input:checked').each( function( index, element ) {
		e_ID = $(element).attr('id');

		liste_TSP_a_ajouter.push( e_ID.split('_')[1] );
	});


	$('div#Lister_Utilisateurs input:checked').each( function( index, element ) {
		e_ID = $(element).attr('id');

		liste_IDN_a_ajouter.push( e_ID.split('_')[1] );
	});


	$.ajax({
		url: '../../../Loxense-Gestionnaires.php?Action=AJAX_Ajouter',
		type: 'POST',
		data: $.param({'liste_TSP_a_ajouter': liste_TSP_a_ajouter, 'liste_IDN_a_ajouter': liste_IDN_a_ajouter, 'libelle': Libelle}), // les paramètres sont protégés avant envoi
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
						var Libelle = $(this).parent().parent().find('div[data-src="gst_libelle"]').find('span').text();

						ModalAjouterModifier( Id, Libelle );
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
