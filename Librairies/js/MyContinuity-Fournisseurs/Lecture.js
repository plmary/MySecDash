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
						var Libelle = $('#FRN_'+Id).find('div[data-src="frn_nom"]').find('span').text();

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


function ModalAjouterModifier( frn_id = '' ){
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		data: $.param({'frn_id': frn_id}), // les paramètres sont protégés avant envoi
		dataType: 'json',
		success: function( reponse ) {
			if ( frn_id == '' ) {
				Titre = reponse['L_Titre_Ajouter'];
				Bouton = reponse[ 'L_Ajouter' ]

				frn_nom = '';
				tfr_nom_code = '';
				frn_description = '';
			} else {
				Titre = reponse['L_Titre_Modifier'];
				Bouton = reponse[ 'L_Modifier' ]

				frn_nom = reponse['Fournisseur'][0].frn_nom;
				tfr_nom_code = reponse['Fournisseur'][0].tfr_nom_code;
				frn_description = reponse['Fournisseur'][0].frn_description;
			}

			var Corps =
				'<div class="row">' +
				'<label class="col-lg-2 col-form-label" for="frn_nom">' + reponse[ 'L_Nom' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<input id="frn_nom" class="form-control" type="text" value="'+ frn_nom + '" required>' +
				'</div>' +
				'</div>' +
				'<div class="row">' +
				'<label class="col-lg-2 col-form-label" for="tfr_id">' + reponse[ 'L_Type' ] + '</label>' +
				'<div class="col-lg-10" id="Zone-Type-Fournisseur">' +
				'<div id="Selectionner-Type-Fournisseur" class="input-group" role="group">' +
				'<select id="tfr_id" class="form-select">' +
				'<option value="">' + reponse['L_Aucun'] + '</option>';
				for (let Type_Fournisseur of reponse['Liste_Types_Fournisseur']) {
					if (reponse['Fournisseur'] !== undefined) {
						if (Type_Fournisseur.tfr_id == reponse['Fournisseur'][0].tfr_id) {
							Selected = ' selected';
						} else {
							Selected = '';
						}
					} else {
						Selected = '';
					}
					Corps += '<option value="' + Type_Fournisseur.tfr_id + '"' + Selected + '>' + Type_Fournisseur.tfr_nom_code + '</option>';
				}
			Corps += '</select>';

			if ( reponse['Droit_Ajouter_Types_Fournisseur'] == true ) {
				Corps += '<button id="Ajouter-Type-Fournisseur" type="button" class="btn btn-outline-secondary" title="'+reponse['L_Ajouter_Type_Fournisseur']+'"><i class="bi-plus"></i></button>';
			}

			Corps += '</div> <!-- .btn-group -->' +
				'</div>' +
				'</div>' +
				'<div class="row">' +
				'<label class="col-lg-2 col-form-label" for="frn_description">' + reponse[ 'L_Description' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<textarea id="frn_description" class="form-control" rows="3">'+ frn_description + '</textarea>' +
				'</div>' +
				'</div>';

			construireModal( 'idModal',
				Titre,
				Corps,
				'idBoutonAjouter', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formAjouterModifier', 'modal-lg' );

			$('#idModal').modal('show'); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModal').on('shown.bs.modal', function() {
				document.getElementById('frn_nom').selectionStart = frn_nom.length;
				$('#frn_nom').focus();

				$('#Ajouter-Type-Fournisseur').on('click', function(){
					$('#Selectionner-Type-Fournisseur').hide();

					$('#Zone-Type-Fournisseur').prepend(
						'<div id="Zone-Ajout-Type-Fournisseur" class="input-group">' +
						'  <span class="input-group-text">'+reponse['L_Nom']+'</span>' +
						'<input id="n_tfr_nom_code" type="text" class="form-control" autofocus>' +
						'<button class="btn btn-outline-secondary" type="button" id="btn-ajouter-type">'+reponse['L_Ajouter']+'</button>' +
						'<button class="btn btn-outline-secondary" type="button" id="btn-fermer-type">'+reponse['L_Fermer']+'</button>' +
						'</div>' +
						'<script>document.getElementById("n_tfr_nom_code").focus();</script>'
					);

					$('#btn-fermer-type').on('click', function(){
						$('#Zone-Ajout-Type-Fournisseur').remove();
						$('#Selectionner-Type-Fournisseur').show();
					});

					$('#btn-ajouter-type').on('click', function(){
						ajouterTypeFournisseur();
					});
				});
			});

			// Supprime la modale après l'avoir caché.
			$('#idModal').on('hidden.bs.modal', function() {
				$('#idModal').remove();
			});

			$('#formAjouterModifier').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				if ( frn_id == '' ) {
					AjouterFournisseur();
				} else {
					ModifierFournisseur( frn_id );
				}
			} );
		}
	});
}


function ajouterTypeFournisseur() {
	var n_tfr_nom_code = $('#n_tfr_nom_code').val();
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_Type_Fournisseur',
		type: 'POST',
		data: $.param({'n_tfr_nom_code': n_tfr_nom_code}), // les paramètres sont protégés avant envoi
		dataType: 'json',
		success: function( reponse ) {
			var statut = reponse['statut'];

			if( statut == 'success' ){
				$('#tfr_id option').removeAttr('selected');
				$('#tfr_id').prepend(
					'<option value="' + reponse['tfr_id'] + '" selected>' + n_tfr_nom_code + '</option>'
				);

				$('#Zone-Ajout-Type-Fournisseur').remove();
				$('#Selectionner-Type-Fournisseur').show();
				
				$('#frn_description').focus();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	});
}