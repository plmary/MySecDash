function modifierLibelles() {
	var lbr_code = $('#lbr_code').val();

	var Libelles_modifier = [];

	$('textarea[id^="lbr_libelle-"]').each(function(index, element){
		lng_id = $(element).attr('id').split('-')[1];
		lbr_libelle = $(element).val();

		if ( $(element).attr('data-old_value') == undefined) { // Cas d'un ajout
			Libelles_ajouter.push([lng_id, lbr_libelle]);
		} else { // Cas d'une possible modification
			Libelles_modifier.push([lng_id, lbr_libelle]);
		}
	});

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier',
		type: 'POST',
		dataType: 'json',
		data: $.param({'code': lbr_code, 'libelles': Libelles_modifier}),
		success: function( reponse ) {
			var statut = reponse[ 'statut' ];
			var texteMsg = reponse[ 'texteMsg' ];
			var obj_id = reponse['lbr_id'];

			if ( statut == 'success' ) {
				$('#idModalAjouterModifier').modal( 'hide' );

				$('#corps_tableau').on('defiler', function() {
					defilerPage( '#'+obj_id, 1 );
				});

				trier( $( 'div#entete_tableau div.row div:first'), true );

				afficherMessage( texteMsg, statut, 'body' );
			} else {
				afficherMessage( texteMsg, statut, '.modal-body', 100, 'n' );
			}
		}
	});
}
