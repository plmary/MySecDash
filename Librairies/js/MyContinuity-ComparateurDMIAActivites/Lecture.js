$(function() {
	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first'), true );

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


					if ( reponse[ 'droit_modifier' ] == 1 ) {
						// Assigne l'événement "click" sur tous les boutons de Modification
						$('.cellule-echelle').click( function( event ){
							var act_id = $(this).attr('data-act_id');
							var ete_id = $(this).attr('data-ete_id');
							var mim_id = $(this).attr('data-mim_id');

							//alert( act_id + ' - ' + ete_id +' - ' + mim_id );

							$.ajax({
								url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
								type: 'POST',
								data: $.param({'trier': sens_recherche}),
								dataType: 'json', // le résultat est transmit dans un objet JSON
								success: function( reponse ){
									var Titre = reponse['L_Titre_Modifier'];
									var Bouton = reponse['L_Modifier'];

									Corps = '<div id="tableau-matrice" class="">' +
										chargerTableauMatrice(reponse['Liste_Niveaux_Impact'], reponse['Liste_Types_Impact'], reponse['Liste_Matrice_Impacts'], reponse['L_Type'], reponse['L_Niveau']) +
										'</div> <!-- #tableau-matrice -->';
		
									construireModal( 'idModal',
										Titre,
										Corps,
										'idBoutonAjouter', Bouton,
										true, reponse[ 'L_Fermer' ],
										'formAjouterModifier', 'modal-xxl' );
		
		
									$('#idModal').modal('show'); // Affiche la modale qui vient d'être créée
		
		
									// Attend que la modale soit affichée avant de donner le focus au champ.
									$('#idModal').on('shown.bs.modal', function() {
										if ( mim_id != '') {
											$('#mim_id-' + mim_id).addClass("active")
										}

										$('#tableau-matrice .cellule-impact').on( 'click', function (){
											var Cellule = $(this);
											$('#tableau-matrice .cellule-impact').removeClass( "active" );
											$(Cellule).addClass("active");
										});
									});
		
		
									$('#formAjouterModifier').submit( function( event ) { // Gère la soumission du formulaire.
										event.preventDefault(); // Laisse le contrôle au Javascript.

										var n_mim_id = $('#tableau-matrice .cellule-impact.active').attr('data-mim_id');
										
										if ( mim_id != n_mim_id ) {
											modifier_DMIA_Activite( mim_id, n_mim_id, act_id, ete_id );
										}

										$('#idModal').modal('hide'); // Cache la modale d'ajout.
									} );
		
		
									// Supprime la modale après l'avoir caché.
									$('#idModal').on('hidden.bs.modal', function() {
										$('#idModal').remove();
									});
								}
							});
						});
					}

/*					if ( reponse[ 'droit_modifier' ] == 1 ) {
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

				$('#rech_dmia').addClass('d-none');
				$('.btn-rechercher').trigger('click');

				redimensionnerWindow();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	});
}
