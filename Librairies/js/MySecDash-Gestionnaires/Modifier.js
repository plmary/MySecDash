// ============================================
// Fonctions répondant aux événements écoutés.

function ModifierGestionnaire( Id ) {
	var Prefixe = 'GST_';

	var Libelle = $('#new_libelle').val();

	var e_ID, e_OLD;

	var liste_TSP_a_ajouter = [], liste_TSP_a_supprimer = [];
	var liste_IDN_a_ajouter = [], liste_IDN_a_supprimer = [];


	$('div#Lister_Types_Supports input').each( function( index, element ) {
		e_ID = $(element).attr('id');
		e_OLD = $(element).attr('data-old');

		if ( $(element).is(':checked') == true && e_OLD == '0' ) {
			liste_TSP_a_ajouter.push( e_ID.split('_')[1] );
		}

		if ( $(element).is(':checked') == false && e_OLD == '1' ) {
			liste_TSP_a_supprimer.push( e_ID.split('_')[1] );
		}
	});


	$('div#Lister_Utilisateurs input').each( function( index, element ) {
		e_ID = $(element).attr('id');
		e_OLD = $(element).attr('data-old');

		if ( $(element).is(':checked') == true && e_OLD == '0' ) {
			liste_IDN_a_ajouter.push( e_ID.split('_')[1] );
		}

		if ( $(element).is(':checked') == false && e_OLD == '1' ) {
			liste_IDN_a_supprimer.push( e_ID.split('_')[1] );
		}
	});


	$.ajax({
		url: Parameters['URL_BASE'] + '/Loxense-Gestionnaires.php?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'id': Id, 'libelle': Libelle,
			'liste_TSP_a_ajouter': liste_TSP_a_ajouter, 'liste_TSP_a_supprimer': liste_TSP_a_supprimer,
			'liste_IDN_a_ajouter': liste_IDN_a_ajouter, 'liste_IDN_a_supprimer': liste_IDN_a_supprimer}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModalAjouterModifier').modal('hide'); // Cache la modale d'ajout.

				// Mise à jour visuelle des champs de l'occurrence.
				var Occurrence = $('#'+Prefixe+Id)

				Occurrence.find('div[data-src="gst_libelle"] span[class="modifiable"]').text( Libelle );

				Occurrence.find('div[data-src="total_tsp"] span').html( reponse['total_tsp']);

				Occurrence.find('div[data-src="total_idn"] span').html( reponse['total_idn'] );

				afficherMessage( texteMsg, statut, 'body' );
			} else {
				afficherMessage( texteMsg, statut, '#idModalAjouterModifier', 0, 'n' );
			}
		}
	});
}
