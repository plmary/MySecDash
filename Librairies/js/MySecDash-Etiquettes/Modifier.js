// ============================================
// Fonctions répondant aux événements écoutés.

function ModifierEtiquette( Id ) {
	var Prefixe = 'TGS_';

	var Code = $('#new_code').val();
	var Libelle = $('#new_libelle').val();
	var Description = $('#new_description').val();

	var e_ID, e_OLD;

	var liste_IDN_a_ajouter = [], liste_IDN_a_supprimer = [];


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
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'id': Id, 'code': Code, 'libelle': Libelle,
			'liste_IDN_a_ajouter': liste_IDN_a_ajouter, 'liste_IDN_a_supprimer': liste_IDN_a_supprimer}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModalAjouterModifier').modal('hide'); // Cache la modale d'ajout.

				// Mise à jour visuelle des champs de l'occurrence.
				var Occurrence = $('#'+Prefixe+Id)

				Occurrence.find('div[data-src="tgs_code"] span[class="modifiable"]').text( Code );

				Occurrence.find('div[data-src="tgs_libelle"] span[class="modifiable"]').text( Libelle );

				Occurrence.find('div[data-src="total_apr"] span').html( reponse['total_apr']);

				Occurrence.find('div[data-src="total_idn"] span').html( reponse['total_idn'] );

				afficherMessage( texteMsg, statut, 'body' );
			} else {
				afficherMessage( texteMsg, statut, '#idModalAjouterModifier', 0, 'n' );
			}
		}
	});
}
