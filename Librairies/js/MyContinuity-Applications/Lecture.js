$(function() {
	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first'), true );

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
					$('#s_cmp_id option').remove();

					// Mise à jour de la liste déroulante des Campagnes associées à la Société
					for (let element of reponse['Liste_Campagnes']) {
						$('#s_cmp_id').append('<option value="' + element.cmp_id + '">' + element.cmp_date + '</option>');
					}

					afficherMessage(texteMsg, statut);

					trier( $( 'div#entete_tableau div.row div:first'), true );
				} else {
					var texteMsg = reponse['texteMsg'];

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

					trier( $( 'div#entete_tableau div.row div:first'), true );
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
						var Libelle = $('#APP_'+Id+' div[data-src="app_nom"] span').text();

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


function ModalAjouterModifier( app_id = '' ){
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		data: $.param({'app_id': app_id}), // les paramètres sont protégés avant envoi
		dataType: 'json',
		success: function( reponse ) {
			if ( app_id == '' ) {
				var Titre = reponse['L_Titre_Ajouter'];
				var Bouton = reponse[ 'L_Ajouter' ]

				var app_nom = '';
				var frn_id = '';
				var app_hebergement = '';
				var app_niveau_service = '';
				var app_description = '';
				var sct_id = '';
			} else {
				var Titre = reponse['L_Titre_Modifier'];
				var Bouton = reponse[ 'L_Modifier' ]

				var app_nom = reponse['Application'].app_nom;
				var frn_id = reponse['Application'].frn_id;
				var app_hebergement = reponse['Application'].app_hebergement;
				var app_niveau_service = reponse['Application'].app_niveau_service;
				var app_description = reponse['Application'].app_description;
				var sct_id = reponse['Application'].sct_id;
			}

			var Corps =
				'<div class="row">' +
				 '<label class="col-lg-2 col-form-label" for="app_nom">' + reponse[ 'L_Nom' ] + '</label>' +
				 '<div class="col-lg-10">' +
				  '<input id="app_nom" class="form-control" type="text" value="'+ app_nom + '" required>' +
				 '</div>' +
				'</div>' +

				'<div id="zone-selection-fournisseur">' +
				 '<div class="row">' +
				  '<label class="col-lg-2 col-form-label" for="frn_id">' + reponse[ 'L_Fournisseur' ] + '</label>' +
				  '<div class="col-lg-9">' +
				   '<select id="frn_id" class="form-select">' +
				    reponse['Liste_Fournisseurs'] +
				   '</select>' +
				  '</div>'; // .col-lg-8

			if ( reponse['Droit_Ajouter_Fournisseurs'] == true ) {
				Corps +='<div class="col-lg-1" id="zone-btn-ajout-fournisseur">' +
					   '<a id="afficher-zone-ajout-fournisseur" class="btn btn-outline-secondary">' +
					    '<i class="bi-plus"></i>' +
					   '</a>' +
					  '</div>'; // .col-lg-1
			}

			Corps += '</div>' + // .row
				'</div>' + // #zone-selection-fournisseur

				'<div id="zone-ajouter-fournisseur" class="d-none">' +
				 '<div class="row">' +
				  '<label class="col-lg-2 col-form-label" for="frn_id">' + reponse[ 'L_Fournisseur' ] + '</label>' +
				  '<div class="col-lg-10">' +
				   '<div class="input-group">' +
				    '<input id="frn_nom" class="form-control" type="text" placeholder="'+reponse['L_Fournisseur']+'">' +
				    '<select id="tfr_id" class="form-select">';

			for (let element of reponse['Liste_Types_Fournisseur']) {
				Corps += '<option value="'+element.tfr_id+'">'+element.tfr_nom_code+'</option>';
			}

			Corps += '</select>' +
				    '<a id="ajouter-fournisseur" class="btn btn-outline-secondary">' +
				     reponse['L_Creer'] +
				    '</a>' +
				    '<a id="cacher-zone-ajout-fournisseur" class="btn btn-outline-secondary">' +
				     reponse['L_Fermer'] +
				    '</a>' +
				   '</div>' + // .input-group
				  '</div>' + // .col-lg-9
				 '</div>' + // .row
				'</div>' + // #zone-selection-fournisseur

				'<div class="row">' +
				 '<label class="col-lg-2 col-form-label" for="app_hebergement">' + reponse[ 'L_Hebergement' ] + '</label>' +
				 '<div class="col-lg-10">' +
				  '<input id="app_hebergement" class="form-control" type="text" value="'+ app_hebergement + '">' +
				 '</div>' + // .col-lg-9
				'</div>' + // .row

				'<div class="row">' +
				 '<label class="col-lg-2 col-form-label" for="app_niveau_service">' + reponse[ 'L_Niveau_Service' ] + '</label>' +
				 '<div class="col-lg-10">' +
				  '<input id="app_niveau_service" class="form-control" type="text" value="'+ app_niveau_service + '">' +
				 '</div>' + // .col-lg-9
				'</div>' + // .row

				'<div class="row">' +
				 '<label class="col-lg-2 col-form-label" for="sct_id">' + reponse[ 'L_Specifique_A' ] + ' ' + $('#s_sct_id option:selected').text() + '</label>' +
				 '<div class="col-lg-2">' +
				  '<select id="sct_id" class="form-select">';
			if ( sct_id == null ) {
				Corps += '<option value="0" selected>' + reponse['L_Non'] + '</option>' +
					'<option value="1">' + reponse['L_Oui'] + '</option>';
			} else {
				Corps += '<option value="0">' + reponse['L_Non'] + '</option>' +
					'<option value="1" selected>' + reponse['L_Oui'] + '</option>';
			}
			Corps += '</select>' +
				'</div>' + // .col-lg-9
				'</div>' + // .row

				'<div class="row">' +
				 '<label class="col-lg-2 col-form-label" for="app_description">' + reponse[ 'L_Description' ] + '</label>' +
				 '<div class="col-lg-10">' +
				  '<textarea id="app_description" class="form-control" rows="3">'+ app_description + '</textarea>' +
				 '</div>' + // .col-lg-9
				'</div>'; // .row

			construireModal( 'idModal',
				Titre,
				Corps,
				'idBoutonAjouter', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formMAJ', 'modal-xl' );

			$('#idModal').modal('show'); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModal').on('shown.bs.modal', function() {
				$('#afficher-zone-ajout-fournisseur').on('click', function(){
					$('#zone-selection-fournisseur').addClass('d-none');
					$('#zone-ajouter-fournisseur').removeClass('d-none');

					$('#cacher-zone-ajout-fournisseur').on('click', function(){
						$('#zone-selection-fournisseur').removeClass('d-none');
						$('#zone-ajouter-fournisseur').addClass('d-none');

						$('#frn_nom').val('');
						$('#tfr_id').val('');
					});

					$('#ajouter-fournisseur').on('click', function(){
						$('#zone-selection-fournisseur').removeClass('d-none');
						$('#zone-ajouter-fournisseur').addClass('d-none');

						var frn_nom = $('#frn_nom').val();
						var tfr_id = $('#tfr_id').val();

						$.ajax({
							url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_Fournisseur',
							type: 'POST',
							data: $.param({'frn_nom': frn_nom, 'tfr_id': tfr_id}), // les paramètres sont protégés avant envoi
							dataType: 'json',
							success: function( reponse ) {
								if ( reponse['statut'] == 'success' ) {
									afficherMessage( reponse['texteMsg'], reponse['statut'], 'body' );

									$('#frn_id').prepend('<option value="'+reponse['frn_id']+'">'+frn_nom+'</option>').val(reponse['frn_id']);
								} else {
									afficherMessage( reponse['texteMsg'], reponse['statut'], '#idModal', 0, 'n' );
								}
							}
						});
					});

					$('#frn_nom').focus();
				});

				$('#app_nom').focus();

				document.getElementById('app_nom').selectionStart = app_nom.length;
			});

			// Supprime la modale après l'avoir caché.
			$('#idModal').on('hidden.bs.modal', function() {
				$('#idModal').remove();
			});

			$('#formMAJ').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				if ( app_id == '' ) {
					AjouterApplication();
				} else {
					ModifierApplication( app_id );
				}
			} );
		}
	});
}
