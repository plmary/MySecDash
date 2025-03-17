// ======================================================================
// Mise en place des écoutes sur les événements produits par les objets.
$(function() {
	$( '#titre-menu .btn-ajouter' ).on( 'click', function(){
		ModalAjouterModifier( '', 1 );
	});


	$('#titre-menu .btn-importer').on('click', function() {
		importerReferentiel();
	});
});



function ajouterLibelles() {
	var lbr_code = $('#lbr_code').val();

	var Libelles_ajouter = [];

	var total = $( '#totalOccurrences' ).text();

	$('textarea[id^="lbr_libelle-"]').each(function(index, element){
		lng_id = $(element).attr('id').split('-')[1];
		lbr_libelle = $(element).val();

		if ( $(element).attr('data-old_value') == undefined) { // Cas d'un ajout
			Libelles_ajouter.push([lng_id, lbr_libelle]);
		} else { // Cas d'une possible modification
			Libelles_modifier.push([lng_id, lbr_libelle]);
		}

		total = Number(total) + 1;
	});

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter',
		type: 'POST',
		dataType: 'json',
		data: $.param({'code': lbr_code, 'libelles': Libelles_ajouter}),
		success: function( reponse ) {
			var statut = reponse[ 'statut' ];
			var texteMsg = reponse[ 'texteMsg' ];

			if ( statut == 'success' ) {
				$('#idModalAjouterModifier').modal( 'hide' );

				$( reponse[ 'codeHTML' ] ).prependTo( '#corps_tableau' );
				$( '#totalOccurrences' ).text( ajouterZero( total ) );

				if ( reponse[ 'droit_modifier' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Modification
					$('.btn-modifier').off( 'click' ).on( 'click', function( event ){
						var Id = $(this).attr('data-id');

						ModalAjouterModifier( Id );
					});
				}

				if ( reponse[ 'droit_supprimer' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Modification
					$('.btn-supprimer').off( 'click' ).on( 'click', function( event ){
						var Id = $(this).attr('data-id');

						ModalSupprimer( Id );
					});
				}

				afficherMessage( texteMsg, statut, 'body' );
			} else {
				afficherMessage( texteMsg, statut, '.modal-body', 100, 'n' );
			}
		}
	});
}
