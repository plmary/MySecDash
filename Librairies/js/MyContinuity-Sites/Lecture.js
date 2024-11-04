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

				$(myElement).attr( 'data-sens-tri', sens_recherche );

				$('#totalOccurrences').text( ajouterZero(reponse[ 'total' ] ) );


				// Vérifie s'il y a une limitation à la création des Entités.
				if ( reponse['total'] >= reponse['limitation'] && reponse['limitation'] != 0 ) {
					var old_title = $('div#titre_ecran button.btn-ajouter').attr('title');

					$('div#titre_ecran button.btn-ajouter').attr('disabled', 'disabled').attr('title', reponse['libelle_limitation']).attr('data-old_title', old_title);
				}

				if ( reponse[ 'droit_modifier' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Modification
					$('.btn-modifier').click( function( event ){
						var Id = $(this).attr('data-id');

						ModalMAJ( Id );
					});
				}

				if ( reponse[ 'droit_supprimer' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Suppression
					$('.btn-supprimer').click(function(){
						var Id = $(this).attr('data-id');
						var Libelle = $('#STS_'+Id).find('div[data-src="sts_nom"]').find('span').text();

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


// ============================================

function ModalMAJ( sts_id = '' ){
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		data: $.param({'sts_id': sts_id}),
		dataType: 'json',
		success: function( reponse ) {
			if ($('#s_sct_id').is('input')) {
				sct_nom = $('#s_sct_id').val();
			} else {
				sct_nom = $('#s_sct_id option:selected').text();
			}

			if ( sts_id != '' ) {
				Titre = reponse['L_Titre_Modifier'];
				Bouton = reponse[ 'L_Modifier' ];

				sts_nom = reponse['objSite'][0].sts_nom;
				sts_description = reponse['objSite'][0].sts_description;
			} else {
				Titre = reponse['L_Titre_Ajouter'];
				Bouton = reponse[ 'L_Ajouter' ];

				sts_nom = '';
				sts_description = '';
			}

			Corps = '<div class="mb-3">' +
				'<label class="col-lg-2 col-form-label" for="sct_nom">' + reponse[ 'L_Societe' ] + '</label>' +
				'<div class="col-lg-8">' +
				'<input id="sct_nom" class="form-control" type="text" disabled value="' + sct_nom + '">' +
				'</div>' +
				'</div>' +
				'<div class="mb-3">' +
				'<label class="col-lg-2 col-form-label" for="sts_nom">' + reponse[ 'L_Nom' ] + '</label>' +
				'<div class="col-lg-6">' +
				'<input id="sts_nom" class="form-control" type="text" required autofocus value="' + sts_nom + '">' +
				'</div>' +
				'</div>' +
				'<div class="mb-3">' +
				'<label class="col-lg-2 col-form-label" for="sts_description">' + reponse[ 'L_Description' ] + '</label>' +
				'<div class="col-lg-12">' +
				'<textarea id="sts_description" class="form-control" rows="3" required>' + sts_description + '</textarea>' +
				'</div>' +
				'</div>';
/*				'<ul class="nav nav-tabs">' +
				//'<li><a id="lister_chk_niveaux_impact" class="nav-link" href="#">' + reponse[ 'L_Niveaux_Appreciation'] + '</a></li>' +
				//'<li><a id="lister_chk_types_impact" class="nav-link" href="#">' + reponse[ 'L_Types_Impact'] + '</a></li>' +
				//'<li><a id="lister_chk_matrice_impacts" class="nav-link" href="#">' + reponse[ 'L_Matrice_Impacts'] + '</a></li>' +
				'<li><a id="lister_chk_entites" class="nav-link" href="#">' + reponse[ 'L_Entites'] + '</a></li>' +
				'<li><a id="lister_chk_echelle_temps" class="nav-link" href="#">' + reponse[ 'L_Echelles_Temps'] + '</a></li>' +
				'<li><a id="lister_chk_applications" class="nav-link" href="#">' + reponse[ 'L_Applications'] + '</a></li>' +
				'<li><a id="lister_chk_fournisseurs" class="nav-link" href="#">' + reponse[ 'L_Fournisseurs'] + '</a></li>' +
				'</ul>';*/

//			Corps += '<div id="onglets_utilisateur">' +
//				'</div>';


			construireModal( 'idModal',
				Titre,
				Corps,
				'idBoutonAjouter', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formMAJ', 'modal-lg' );


			// Affiche la modale qui vient d'être créée
			$('#idModal').modal('show');


			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModal').on('shown.bs.modal', function() {
				$('#sts_nom').focus();

				document.getElementById('sts_nom').selectionStart = sts_nom.length;
			});


			$('#idModal').on('hidden.bs.modal', function() {
				$('#idModal').remove(); // Supprime la modale d'ajout.
			});


			$('#formMAJ').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				if ( sts_id == '' ) {
					AjouterSite();
				} else {
					ModifierSite( sts_id );
				}
			} );

		}
	});

}
