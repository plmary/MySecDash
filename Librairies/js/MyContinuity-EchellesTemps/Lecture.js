$(function() {
	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first'), false );

	// Active l'écoute du "click" sur les libellés de l'entête du tableau.
	$('.triable').click( function() {
		trier( this, true );
	});

	// Active l'écoute du "select" sur le changement de Société.
	$('#s_sct_id').change(function() {
		//var sens_recherche = $( 'div#entete_tableau div.row div:first' ).attr( 'data-sens-tri' );
		var sct_id = $('#s_sct_id').val();

		$.ajax({
			url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Selectioner_Societe',
			type: 'POST',
			data: $.param({'sct_id': sct_id }),
			dataType: 'json', // le résultat est transmit dans un objet JSON
			success: function(reponse) {
				var statut = reponse['statut'];
				var texteMsg = reponse['texteMsg'];

				if (statut == 'success') {
					afficherMessage(texteMsg, statut);

					$('#s_cmp_id option').remove();

					if (reponse['cmp_id'] == '') {
						$('#s_cmp_id').prepend('<option value="">---</option>');
					} else {
						if ( reponse['Liste_Campagnes'] == undefined ) {
							$('#s_cmp_id').append('<option value="">---</option>');
						} else {
							// Mise à jour de la liste déroulante des Campagnes associées à la Société
							if ( reponse['Liste_Campagnes'] != [] && reponse['Liste_Campagnes'] != '' ) {
								if (reponse['Liste_Campagnes'].length > 0) {
									for (let element of reponse['Liste_Campagnes']) {
										$('#s_cmp_id').append('<option value="' + element.cmp_id + '">' + element.cmp_date + '</option>');
									}
								} else {
									$('#s_cmp_id').val(reponse['Liste_Campagnes'][0].cmp_date);
								}
							}
						}
					}

					trier( $( 'div#entete_tableau div.row div:first'), false );
				} else {
					afficherMessage(texteMsg, statut);
				}
			}
		});
	});

	// Active l'écoute du "select" sur le changement de Campagne.
	$('#s_cmp_id').change(function() {
		//var sens_recherche = $( 'div#entete_tableau div.row div:first' ).attr( 'data-sens-tri' );
		var cmp_id = $('#s_cmp_id').val();

		$.ajax({
			url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Selectioner_Campagne',
			type: 'POST',
			data: $.param({/*'trier': sens_recherche,*/ 'cmp_id': cmp_id }),
			dataType: 'json', // le résultat est transmit dans un objet JSON
			success: function(reponse) {
				var statut = reponse['statut'];

				if (statut == 'success') {
					var texteMsg = reponse['texteMsg'];

					afficherMessage(texteMsg, statut);

					trier( $( 'div#entete_tableau div.row div:first'), false );
				} else {
					var texteMsg = reponse['texteMsg'];

					afficherMessage(texteMsg, statut);
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
					$('.btn-modifier').click( function( event ){
						var Id = $(this).attr('data-id');

						ModalAjouterModifier( Id );
					});
				}

				if ( reponse[ 'droit_supprimer' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Suppression
					$('.btn-supprimer').click(function(){
						var Id = $(this).attr('data-id');
						var Libelle = $('#ETE_'+Id+' div[data-src="ete_nom_code"] span').text();

						ModalSupprimer( Id, Libelle );
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


function ModalAjouterModifier( ete_id = '' ){
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		data: $.param({'ete_id': ete_id}), // les paramètres sont protégés avant envoi
		dataType: 'json',
		success: function( reponse ) {
			if ( ete_id == '' ) {
				Titre = reponse['L_Titre_Ajouter'];
				Bouton = reponse[ 'L_Ajouter' ];

				ete_poids = Number($( '#totalOccurrences' ).text()) + 1;
				ete_nom_code = '';
			} else {
				Titre = reponse['L_Titre_Modifier'];
				Bouton = reponse[ 'L_Modifier' ]

				ete_poids = reponse['EchelleTemps'].ete_poids;
				ete_nom_code = reponse['EchelleTemps'].ete_nom_code;
			}

			var Corps =
				'<div class="row">' +
				'<label class="col-lg-2 col-form-label" for="ete_poids">' + reponse[ 'L_Poids' ] + '</label>' +
				'<div class="col-lg-2">' +
				'<input id="ete_poids" class="form-control" type="text" value="'+ ete_poids + '" disabled>' +
				'</div>' +
				'</div>' +

				'<div class="row">' +
				'<label class="col-lg-4 col-form-label" for="ete_nom_code">' + reponse[ 'L_Nom_Echelle_Temps' ] + '</label>' +
				'<div class="col-lg-12">' +
				'<input id="ete_nom_code" class="form-control" type="text" value="'+ ete_nom_code + '">' +
				'</div>' +
				'</div>';

			construireModal( 'idModal',
				Titre,
				Corps,
				'idBoutonAjouter', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formMAJ', 'modal-lg' );

			$('#idModal').modal('show'); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModal').on('shown.bs.modal', function() {
				$('#ete_nom_code').focus();

				document.getElementById('ete_nom_code').selectionStart = ete_nom_code.length;
			});

			// Supprime la modale après l'avoir caché.
			$('#idModal').on('hidden.bs.modal', function() {
				$('#idModal').remove();
			});

			$('#formMAJ').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				if ( ete_id == '' ) {
					AjouterEchelleTemps();
				} else {
					ModifierEchelleTemps( ete_id );
				}
			} );
		}
	});
}
