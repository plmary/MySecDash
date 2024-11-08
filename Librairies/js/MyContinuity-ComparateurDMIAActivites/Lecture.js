$(function() {
	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first'), true );

	// Active l'écoute du "click" sur les libellés de l'entête du tableau.
	$('.triable').click( function() {
		trier( this, true );
	});
});


function trier( myElement, changerTri ) {
	// AJAX changeant la valeur du filtre
	var sens_recherche = $( myElement ).attr( 'data-sens-tri' );
	var changer_tri = changerTri || false;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Trier',
		type: 'POST',
		data: $.param({'trier': sens_recherche}),
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ){
			var statut = reponse['statut'];

			if( statut == 'success' ){
				if ( reponse['s_cmp_id'] == '---' || reponse['s_cmp_id'] == '' ) {
					$('#s_cmp_id').append('<option value="">---</option>');
					afficherMessageCorps(reponse['L_Societe_Sans_Campagne'], reponse['L_Gestion_Campagnes']);
				} else {
					var texteMsg = reponse['texteHTML'];
	
					$('div#corps_tableau').html( reponse[ 'texteHTML'] );
	
					if ( changer_tri == true ) {
						var Element = sens_recherche.split('-');
						if ( Element[ Element.length - 1 ] == 'desc' ) {
							sens_recherche = Element[ 0 ];
						} else {
							sens_recherche = Element[ 0 ] + '-desc';
						}
					}
	
					// Postionne la couleur sur la colonne active sur le tri.
					$('div#entete_tableau div.row div.triable').removeClass('active');
					$(myElement).addClass('active');
	
					$(myElement).attr( 'data-sens-tri', sens_recherche );
	
					$('#totalOccurrences').text( ajouterZero(reponse[ 'total' ] ) );
	
	
/*					if ( reponse[ 'droit_ajouter' ] == 1 ) {
						// Assigne l'événement "click" sur tous les boutons de Modification
						$('.btn-dupliquer').click( function( event ){
							var Id = $(this).attr('data-id');

							ModalDupliquer( Id );
						});
					}
	
					if ( reponse[ 'droit_modifier' ] == 1 ) {
						// Assigne l'événement "click" sur tous les boutons de Modification
						$('.btn-modifier').click( function( event ){
							var Id = $(this).attr('data-id');

							ModalAjouterModifier( Id );
						});
					}

					if ( reponse[ 'droit_supprimer' ] == 1 ) {
						// Assigne l'événement "click" sur tous les boutons de Suactession
						$('.btn-supprimer').click(function(){
							var Id = $(this).attr('data-id');
							var Libelle = $('#ACT_'+Id).find('div[data-src="act_nom"]').find('span').text();
	
							ModalSupprimer( Id, Libelle );
						});
					}*/
				}

				redimensionnerWindow();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	});
}
