$(function() {
	// Active l'écoute du "select" sur le changement de Campagne.
	$('#s_lng_id').change(function() {
		var lng_id = $('#s_lng_id').val();

		$.ajax({
			url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Selectioner_Langue',
			type: 'POST',
			data: $.param({/*'trier': sens_recherche,*/ 'lng_id': lng_id }),
			dataType: 'json', // le résultat est transmit dans un objet JSON
			success: function(reponse) {
				var statut = reponse['statut'];
				var texteMsg = reponse['texteMsg'];

				afficherMessage(texteMsg, statut);

				if (statut == 'success') {
					trier( $( 'div#entete_tableau div.row div:first'), true );
				}
			}
		});
	});


	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first'), true );

	// Active l'écoute du "click" sur les libellés de l'entête du tableau.
	$('.triable').click( function() {
		trier( this, true );
	});
});



function trier( myElement, changerTri = false ) {
	var sens_recherche = $( myElement ).attr( 'data-sens-tri' );

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Trier',
		type: 'POST',
		dataType: 'json',
		data: $.param({'trier': sens_recherche}),
		success: function( reponse ){
			var statut = reponse['statut'];

			if ( statut == 'success' ) {
				Texte = reponse[ 'texteHTML' ];

				$('#corps_tableau').html( Texte );

				$('#totalOccurrences').text( reponse[ 'total' ] );

				if ( changerTri == true ) {
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

				if ( reponse[ 'droit_modifier' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Modification
					$('.btn-modifier').on( 'click', function( event ){
						var Id = $(this).attr('data-id');

						ModalAjouterModifier( Id );
					});
				}

				if ( reponse[ 'droit_supprimer' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Modification
					$('.btn-supprimer').on( 'click', function( event ){
						var Id = $(this).attr('data-id');
	
						ModalSupprimer( Id );
					});
				}
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	}); 

	redimensionnerWindow();
}



function ModalAjouterModifier( Id = '' ) {
	if ( Id != '' || Id === null ) {
		var lbr_code = $('#LBR_'+ Id + ' div[data-src="lbr_code"] span').text();
		var lng_id = $('#LBR_'+ Id + ' div[data-src="lng_id"] span').text();
		var lbr_libelle = $('#LBR_'+ Id + ' div[data-src="lbr_libelle"] span').text();
		var type_chp_code = 'disabled';
	} else {
		var lbr_code = '';
		var lng_id = '';
		var lbr_libelle = '';
		var type_chp_code = '';
	}

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		async: false,
		dataType: 'json',
		success: function( reponse ) {
			var code_HTML = '<div class="row">' +
				'<label class="col-lg-3 col-form-label" for="lbr_code">' + reponse[ 'L_Code' ] + '</label>' +
				'<div class="col-lg-9">' +
				'<input id="lbr_code" class="form-control" type="text" maxlength="60" required value="' + lbr_code + '" ' + type_chp_code + '>' +
				'</div> <!-- .col-lg-9 -->' +
				'</div> <!-- .row -->' +
				'<div class="row" style="margin-top: 21px;">' +
				'<label class="col-lg-3 col-form-label fw-bold">' + reponse[ 'L_Langue' ] + '</label>' +
				'<label class="col-lg-9 col-form-label fw-bold">' + reponse[ 'L_Libelle' ] + '</label>' +
				'</div> <!-- .row -->';

			if ( Id == '' && Id !== null ) {
				var Titre = reponse['L_Titre_Ajouter'];
				var Bouton = reponse[ 'L_Ajouter' ];

				for (const Langue in reponse['Liste_Langues']) {
					code_HTML += '<div class="row">' +
						'<div class="col-lg-3">' +
						'<input id="lbr_langue" class="form-control" type="text" disabled value="' + reponse['Liste_Langues'][Langue]['lng_id'] + '">' +
						'</div>' +
						'<div class="col-lg-9">' +
						'<textarea id="lbr_libelle-' + reponse['Liste_Langues'][Langue]['lng_id'] + '" class="form-control" required></textarea>' +
						'</div>' +
						'</div> <!-- .row -->';
				}
			} else {
				var Titre = reponse['L_Titre_Modifier'];
				var Bouton = reponse[ 'L_Modifier' ];

				code_HTML += '<div class="row">' +
					'<div class="col-lg-3">' +
					'<select id="lbr_langue-' + Id + '" class="form-select" disabled>';

				for (const Langue in reponse['Liste_Langues']) {
					if (reponse['Liste_Langues'][Langue]['lng_id'] == lng_id) {
						_Defaut = ' selected';
					} else {
						_Defaut = '';
					}

					code_HTML += '<option value="' + reponse['Liste_Langues'][Langue]['lng_id'] + '"' + _Defaut + '>' +
						reponse['Liste_Langues'][Langue]['lng_id'] +
						'</option>';
				}

				code_HTML += '</select>' +
					'</div>' +
					'<div class="col-lg-9">' +
					'<textarea id="lbr_libelle-' + Id + '" class="form-control" required>' + lbr_libelle + '</textarea>' +
					'</div>' +
					'</div>';
			}


			construireModal( 'idModalAjouterModifier',
				Titre,
				code_HTML,
				'idBouton', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formModalAjouterModifier',
				'modal-lg' );


			// Affiche la modale qui vient d'être créée
			$('#idModalAjouterModifier').modal('show');


			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalAjouterModifier').on('shown.bs.modal', function() {
				if ( Id == '' && Id !== null ) {
					$('#lbr_code').focus();
	
					var P_Code = $('#lbr_code');
					var V_Code = P_Code.val();
	
					if ( V_Code != '' ) P_Code[0].selectionStart = V_Code.length;
				} else {
					$('#lbr_libelle-' + Id).focus();

					var P_Libelle = $('#lbr_libelle-' + Id);
					var V_Libelle = P_Libelle.val();

					if ( V_Libelle != '' ) P_Libelle[0].selectionStart = V_Libelle.length;
				}
			});


			// Après avoir disparu à l'écran la fenêtre est supprimée.
			$('#idModalAjouterModifier').on('hidden.bs.modal', function() {
				$('#idModalAjouterModifier').remove(); // Supprime la modale d'ajout.
			});


			// Sauvegarde les modifications réalisées dans la fenêtre.
			$('#formModalAjouterModifier').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				if ( Id == '' ) {
					ajouterLibelles();
				} else {
					modifierLibelles( Id );
				}
			});
		}
	});
}
