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

				// Vérifie s'il y a une limitation à la création des Entités.
				if ( reponse[ 'total' ] >= reponse['limitation'] && reponse['limitation'] != 0 ) {
					var old_title = $('div#titre_ecran button.btn-ajouter').attr('title');

					$('div#titre_ecran button.btn-ajouter').attr('disabled', 'disabled').attr('title', reponse['libelle_limitation']).attr('data-old_title', old_title);;
				} else {
					$('div#titre_ecran button.btn-ajouter').removeAttr('disabled');
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
						var Last_Name = $('#CVL_'+Id).find('div[data-src="cvl_nom"]').find('span').text();
						var First_Name = $('#CVL_'+Id).find('div[data-src="cvl_prenom"]').find('span').text();

						ModalSupprimer( Id, Last_Name, First_Name );
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


function ModalMAJ(cvl_id=''){
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		data: $.param({'cvl_id': cvl_id}), // les paramètres sont protégés avant envoi
		dataType: 'json',
		success: function( reponse ) {
			var Prenom = '';
			var Nom = '';

			if (cvl_id == '') {
				Titre = reponse['L_Titre_Ajouter'];
				Bouton = reponse['L_Ajouter'];
			} else {
				Titre = reponse['L_Titre_Modifier'];
				Bouton = reponse['L_Modifier'];
				Prenom = reponse['Civilite'].cvl_prenom
				Nom = reponse['Civilite'].cvl_nom;
			}

			var Corps =
				'<div class="row">' +
				'<label class="col-lg-2 col-form-label" for="cvl_nom">' + reponse[ 'L_Nom' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<input id="cvl_nom" class="form-control text-uppercase" type="text" value="' + Nom + '" required>' +
				'</div>' +
				'</div>' +
				'<div class="row">' +
				'<label class="col-lg-2 col-form-label" for="cvl_prenom">' + reponse[ 'L_Prenom' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<input id="cvl_prenom" class="form-control text-capitalize" type="text" value="' + Prenom + '" required>' +
				'</div>' +
				'</div>';

			construireModal( 'idModalCivilite',
				Titre,
				Corps,
				'idBoutonAjouter', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formMAJCivilite', 'modal-lg' );

			$('#idModalCivilite').modal('show'); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalCivilite').on('shown.bs.modal', function() {
				$('#cvl_nom').focus();
				document.getElementById('cvl_nom').selectionStart = Nom.length;
			});

			// Supprime la modale après l'avoir caché.
			$('#idModalCivilite').on('hidden.bs.modal', function() {
				$('#idModalCivilite').remove();
			});

			$('#formMAJCivilite').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				var cvl_nom = $('#cvl_nom').val();
				cvl_nom = cvl_nom.toUpperCase();
				$('#cvl_nom').val( cvl_nom );

				var cvl_prenom = $('#cvl_prenom').val();
				cvl_prenom = transformePrenom( cvl_prenom );
				$('#cvl_prenom').val( cvl_prenom );

				if (cvl_id == '') {
					AjouterCivilite();
				} else {
					ModifierCivilite( cvl_id );
				}
			} );
		}
	});
}
