$(function() {
	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first' ), true );

	// Active l'écoute du "click" sur les libellés de l'entête du tableau.
	$('.triable').click( function() {
		trier( this, true );
	});

	// Active l'écoute du "select" sur le changement de Société.
	$('#s_sct_id').change( function() {
		var sens_recherche = $( 'div#entete_tableau div.row div:first' ).attr( 'data-sens-tri' );
		var sct_id = $('#s_sct_id').val();

		$.ajax({
			url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Selectioner_Societe',
			type: 'POST',
			data: $.param({'trier': sens_recherche, 'sct_id': sct_id}),
			dataType: 'json', // le résultat est transmit dans un objet JSON
			success: function( reponse ){
				var statut = reponse['statut'];
	
				if( statut == 'success' ){
					var texteMsg = reponse['texteMsg'];
	
					afficherMessage( texteMsg, statut );

					trier( $( 'div#entete_tableau div.row div:first'), true );
				} else {
					var texteMsg = reponse['texteMsg'];
	
					afficherMessage( texteMsg, statut );
				}
			}
		});
	});

	// Active l'écoute du "select" sur le changement de Campagne.
	$('#s_cmp_id').change( function() {
		var sens_recherche = $( 'div#entete_tableau div.row div:first' ).attr( 'data-sens-tri' );
		var cmp_id = $('#s_cmp_id').val();
	
		$.ajax({
			url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Selectioner_Campagne',
			type: 'POST',
			data: $.param({'trier': sens_recherche, 'cmp_id': cmp_id}),
			dataType: 'json', // le résultat est transmit dans un objet JSON
			success: function( reponse ){
				var statut = reponse['statut'];
	
				if( statut == 'success' ){
					var texteMsg = reponse['texteMsg'];
	
					afficherMessage( texteMsg, statut );
	
					trier( $( 'div#entete_tableau div.row div:first'), true );
				} else {
					var texteMsg = reponse['texteMsg'];
	
					afficherMessage( texteMsg, statut );
				}
			}
		});
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
				var texteMsg = reponse['texteHTML'];

				$('div#corps_tableau').html( reponse[ 'texteHTML'] );

				if ( $('#s_sct_id').is('input') == false ) {
					$('#s_sct_id').val( reponse['sct_id'] );
				}

				$('#s_cmp_id').val( reponse['cmp_id'] );

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

				$('#totalOccurrences').text( ajouterZero(reponse[ 'total' ]) );

				if ( reponse[ 'droit_modifier' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Modification
					$('.btn-valider').click( function( event ){
						var Id = $(this).attr('data-id');

						ModalValiderEntite( Id );
					});
				}

				redimensionnerWindow();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	});
}


// ============================================

function ModalValiderEntite( ent_id ){
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		data: $.param({'ent_id': ent_id}),
		dataType: 'json',
		success: function( reponse ) {
			var Titre = reponse['L_Valider_BIA_Entite'];
			var Bouton = reponse[ 'L_Valider' ];
			
			var Tableau_Infos_Validation = '<br><table class="table-visu table-bordered mt-3">' +
				'<theader>' +
				'<tr>' +
				'<th class="bg_couleur_3 text-center fs-4" colspan="2">' + reponse['L_Informations_Validation'] + '</th>' +
				'<tr>' +
				'<th>' + reponse['L_Valideur'] + '</th>' +
				'<th>' + reponse['L_Date_Validation'] + '</th>' +
				'</tr>' +
				'</theader>';

			if ( reponse['infos_validation'].cmen_date_validation == null ) {
				Tableau_Infos_Validation += '<tbody>' +
					'<tr>' +
					'<td class="text-center" colspan="2"><span class="fs-5 fw-bold fg_couleur_3">' + reponse['L_Aucune'] + '</span></td>' +
					'</tr>' +
					'</tbody>' +
					'</table>';
			} else {
				Tableau_Infos_Validation += '<tbody>' +
					'<tr>' +
					'<td class="fg_couleur_3">' + reponse['infos_validation'].cvl_nom + ' ' + reponse['infos_validation'].cvl_prenom + '</td>' +
					'<td class="fg_couleur_3">' + reponse['infos_validation'].cmen_date_validation + '</td>' +
					'</tr>' +
					'</tbody>' +
					'</table>';
			}

			$.ajax({
				url: Parameters['URL_BASE'] + '/MyContinuity-VisualiserBIA.php?Action=AJAX_Synthese_Specifique',
				type: 'POST',
				//data: $.param({'ent_id': ent_id}),
				dataType: 'json',
				success: function( reponse2 ) {
					reponse2['texteHTML'] += Tableau_Infos_Validation;

					construireModal( 'idModalEntite',
						Titre,
						reponse2['texteHTML'],
						'idBoutonAjouter', Bouton,
						true, reponse[ 'L_Fermer' ],
						'formMAJEntite', 'modal-xxl' );
	
					// Affiche la modale qui vient d'être créée
					$('#idModalEntite').modal('show');
	
					// Attend que la modale soit affichée avant de donner le focus au champ.
					$('#idModalEntite').on('shown.bs.modal', function() {
					});
	
					$('#idModalEntite').on('hidden.bs.modal', function() {
						$('#idModalEntite').remove(); // Supprime la modale d'ajout.
					});
	
					$('#formMAJEntite').submit( function( event ) { // Gère la soumission du formulaire.
						event.preventDefault(); // Laisse le contrôle au Javascript.
	
						ValiderEntite( ent_id );
					} );
				}
			});
		}
	});

}
