// ======================================================================
// Mise en place des écoutes sur les événements produits par les objets.
$(function() {
	// Ajouter l'entité dans la base.
	$(".btn-ajouter").on('click', function(){
		ModalMAJCampagne();
	});
});



function AjouterCampagne( p_cmp_date ) {
	var cmp_date = $('#cmp_date').val();
	var Liste_ENT_Ajouter = [];
	var Liste_APP_Ajouter = [];
	var Liste_FRN_Ajouter = [];
	var Liste_STS_Ajouter = [];

	var total = $( '#totalOccurrences' ).text();
	total = Number(total) + 1;

	$('input[id^="entite-"]').each(function(index, element){
		if ($(element).is(':checked')) {
			if ( $(element).attr('data-old_value') == 0) {
				Liste_ENT_Ajouter.push($(element).attr('id').split('-')[1]);
			}
		}
	});

	$('input[id^="application-"]').each(function(index, element){
		if ($(element).is(':checked')) {
			if ( $(element).attr('data-old_value') == 0) {
				Liste_APP_Ajouter.push($(element).attr('id').split('-')[1]);
			}
		}
	});

	$('input[id^="fournisseur-"]').each(function(index, element){
		if ($(element).is(':checked')) {
			if ( $(element).attr('data-old_value') == 0) {
				Liste_FRN_Ajouter.push($(element).attr('id').split('-')[1]);
			}
		}
	});

	$('input[id^="site-"]').each(function(index, element){
		if ($(element).is(':checked')) {
			if ( $(element).attr('data-old_value') == 0) {
				Liste_STS_Ajouter.push($(element).attr('id').split('-')[1]);
			}
		}
	});

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter',
		type: 'POST',
		data: $.param({'cmp_date': cmp_date, 'p_cmp_date': p_cmp_date,
			'liste_ent_ajouter': Liste_ENT_Ajouter,
			'liste_app_ajouter': Liste_APP_Ajouter,
			'liste_frn_ajouter': Liste_FRN_Ajouter,
			'liste_sts_ajouter': Liste_STS_Ajouter
			}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModalCampagne').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				$( reponse[ 'texte' ] ).prependTo( '#corps_tableau' );

				$( '#totalOccurrences' ).text( ajouterZero( total ) );

				// Assigne l'événement "click" sur le bouton de Modification
				if ( reponse[ 'droit_modifier' ] == true ) {
					$('#CMP_' + reponse[ 'id' ]).find('button.btn-modifier').click(function(event){
						ModalMAJCampagne( reponse[ 'cmp_id' ] );
					});
				}

				// Assigne l'événement "click" sur le bouton de Suppression
				if ( reponse[ 'droit_supprimer' ] == true ) {
					$('#CMP_' + reponse[ 'id' ] + ' .btn-supprimer').click(function(){
						ModalSupprimer( reponse[ 'id' ], cmp_date );
					});
				}
			} else {
				afficherMessage( texteMsg, statut, '#idModalCampagne', 0, 'n' );
			}
		}
	});
}


function DupliquerCampagne( cmp_id ) {
	var total = $( '#totalOccurrences' ).text();
	var total = Number(total) + 1;
	var p_cmp_date = $('#p_cmp_date').val();
	var cmp_date = $('#cmp_date').val()
	
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Dupliquer',
		type: 'POST',
		data: $.param({'cmp_id': cmp_id, 'p_cmp_date': p_cmp_date, 'cmp_date': cmp_date}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];
			if ( statut == 'success' ) {

				$('#idModalCampagne').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				$( reponse[ 'texte' ] ).prependTo( '#corps_tableau' );

				$( '#totalOccurrences' ).text( ajouterZero( total ) );

				// Assigne l'événement "click" sur le bouton de Modification
				if ( reponse[ 'droit_modifier' ] == true ) {
					$('#CMP_' + reponse[ 'id' ] + ' .btn-modifier').click(function( event ){
						ModalMAJCampagne( reponse[ 'id' ] );
					});
				}

				// Assigne l'événement "click" sur le bouton de Suppression
				if ( reponse[ 'droit_supprimer' ] == true ) {
					$('#CMP_' + reponse[ 'id' ] + ' .btn-supprimer').click(function( event ){
						ModalSupprimer( reponse[ 'id' ], cmp_date );
					});
				}

				// Assigne l'événement "click" sur tous les boutons de Duplication
				$('#CMP_' + reponse[ 'id' ] + '.btn-dupliquer').click( function( event ){
					ModalDupliquerCampagne( reponse[ 'id' ] );
				});
			} else {
				afficherMessage( texteMsg, statut );
			}
		}
	});
}

