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
						var Libelle = $('#PPR_'+Id).find('div[data-src="ppr_nom"]').find('span').text();
						Libelle += ' ' + $('#PPR_'+Id+' div[data-src="ppr_prenom"] span').text();

						var ppr_description = $('#PPR_'+Id+' div[data-src="ppr_description"] span').text();
						if (ppr_description != '') {
							Libelle += ' (' + ppr_description + ')';
						}

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


function ModalAjouterModifier( ppr_id = '' ){
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		data: $.param({'ppr_id': ppr_id}), // les paramètres sont protégés avant envoi
		dataType: 'json',
		success: function( reponse ) {
			interne_0_selected = '';
			interne_1_selected = '';

			if ( ppr_id == '' ) {
				Titre = reponse['L_Titre_Ajouter'];
				Bouton = reponse[ 'L_Ajouter' ];

				ppr_nom = '';
				ppr_prenom = '';
				ppr_interne = '';
				ppr_description = '';
			} else {
				Titre = reponse['L_Titre_Modifier'];
				Bouton = reponse[ 'L_Modifier' ];

				ppr_nom = reponse['PartiePrenante'][0].ppr_nom;
				ppr_prenom = reponse['PartiePrenante'][0].ppr_prenom;
				ppr_interne = reponse['PartiePrenante'][0].ppr_interne;
				if (ppr_interne == true) {
					interne_1_selected = ' selected';
				} else {
					interne_0_selected = ' selected';
				}
				ppr_description = reponse['PartiePrenante'][0].ppr_description;
			}

			var Corps =
				'<div class="row">' +
				'<label class="col-lg-2 col-form-label" for="ppr_nom">' + reponse[ 'L_Nom' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<input id="ppr_nom" class="form-control text-uppercase" type="text" value="'+ ppr_nom + '" maxlength="35" required>' +
				'</div>' +
				'</div>' +
				'<div class="row">' +
				'<label class="col-lg-2 col-form-label" for="ppr_prenom">' + reponse[ 'L_Prenom' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<input id="ppr_prenom" class="form-control text-capitalize" type="text" value="'+ ppr_prenom + '" maxlength="25" required>' +
				'</div>' +
				'</div>' +
				'<div class="row">' +
				'<label class="col-lg-2 form-label" for="ppr_interne">' + reponse[ 'L_Interne' ] + '</label>' +
				'<div class="col-lg-2">' +
				'<select id="ppr_interne" class="form-select" required>' +
				'<option value="0"' + interne_0_selected + '>' + reponse['L_Non'] + '</option>' +
				'<option value="1"' + interne_1_selected + '>' + reponse['L_Oui'] + '</option>' +
				'</select>' +
				'</div>' +
				'</div>' +
				'<div class="row">' +
				'<label class="col-lg-2 col-form-label" for="ppr_description">' + reponse[ 'L_Description' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<textarea id="ppr_description" class="form-control" rows="3">'+ ppr_description + '</textarea>' +
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
				document.getElementById('ppr_nom').selectionStart = ppr_nom.length;
				$('#ppr_nom').focus();

				$('#Ajouter-Role-PartiePrenante').on('click', function(){
					$('#Selectionner-Role-PartiePrenante').hide();

					$('#Zone-Role-PartiePrenante').prepend(
						'<div id="Zone-Ajout-Role-PartiePrenante" class="input-group">' +
						'  <span class="input-group-text">'+reponse['L_Nom']+'</span>' +
						'<input id="n_rpp_nom_code" type="text" class="form-control" autofocus>' +
						'<button class="btn btn-outline-secondary" type="button" id="btn-ajouter-type">'+reponse['L_Ajouter']+'</button>' +
						'<button class="btn btn-outline-secondary" type="button" id="btn-fermer-type">'+reponse['L_Fermer']+'</button>' +
						'</div>' +
						'<script>document.getElementById("n_rpp_nom_code").focus();</script>'
					);

					$('#btn-fermer-type').on('click', function(){
						$('#Zone-Ajout-Role-PartiePrenante').remove();
						$('#Selectionner-Role-PartiePrenante').show();
					});

					$('#btn-ajouter-type').on('click', function(){
						ajouterRolePartiePrenante();
					});
				});
			});

			// Supprime la modale après l'avoir caché.
			$('#idModal').on('hidden.bs.modal', function() {
				$('#idModal').remove();
			});

			$('#formAjouterModifier').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				if ( ppr_id == '' ) {
					AjouterPartiePrenante();
				} else {
					ModifierPartiePrenante( ppr_id );
				}
			} );
		}
	});
}


function ajouterRolePartiePrenante() {
	var n_rpp_nom_code = $('#n_rpp_nom_code').val();
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_Role_PartiePrenante',
		type: 'POST',
		data: $.param({'n_rpp_nom_code': n_rpp_nom_code}), // les paramètres sont protégés avant envoi
		dataType: 'json',
		success: function( reponse ) {
			var statut = reponse['statut'];

			if( statut == 'success' ){
				$('#rpp_id option').removeAttr('selected');
				$('#rpp_id').prepend(
					'<option value="' + reponse['rpp_id'] + '" selected>' + n_rpp_nom_code + '</option>'
				);

				$('#Zone-Ajout-Role-PartiePrenante').remove();
				$('#Selectionner-Role-PartiePrenante').show();
				
				$('#ppr_interne').focus();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	});
}