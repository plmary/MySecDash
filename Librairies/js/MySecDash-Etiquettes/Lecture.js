$(function() {
	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first'), true );


	// Active l'écoute du "click" sur les libellés de l'entête du tableau.
	$('.triable').click( function() {
		trier( this, true );
	});


	if ( $('.btn-chercher').length > 0 ) {
		$('.btn-chercher').on('click', function() {
			trier( $('div.active'), false, $('#c_rechercher').val() );
		});
	}
});


function trier( myElement, changerTri, chercher ) {
	// AJAX changeant la valeur du filtre
	var sens_recherche = $( myElement ).attr( 'data-sens-tri' );
	var changer_tri = changerTri || false;

	if ( changer_tri == false ) {
		var Element = sens_recherche.split('-');
		if ( Element[ Element.length - 1 ] == 'desc' ) {
			sens_recherche = Element[ 0 ];
		} else {
			sens_recherche = Element[ 0 ] + '-desc';
		}
	}

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Trier',
		type: 'POST',
		data: $.param({'trier': sens_recherche, 'chercher': chercher}),
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ){
			var statut = reponse['statut'];

			if( statut == 'success' ){
				var texteMsg = reponse['texteHTML'];

				$('div#corps_tableau').html( reponse[ 'texteHTML'] );

				var Element = sens_recherche.split('-');
				if ( Element[ Element.length - 1 ] == 'desc' ) {
					sens_recherche = Element[ 0 ];
				} else {
					sens_recherche = Element[ 0 ] + '-desc';
				}

				$(myElement).attr( 'data-sens-tri', sens_recherche );

				$('.triable').removeClass( 'active' );
				$(myElement).addClass( 'active' );

				$('#totalOccurrences').text( ajouterZero(reponse[ 'total' ] ) );


				if ( reponse[ 'droit_modifier' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Modification
					$('.btn-modifier').click( function( event ){
						var Id = $(this).attr('data-id');
						//var Code = $(this).parent().parent().find('div[data-src="tgs_code"]').find('span').text();
						//var Libelle = $(this).parent().parent().find('div[data-src="tgs_libelle"]').find('span').text();

						ModalAjouterModifier( Id ); //, Code, Libelle );
					});
				}

				if ( reponse[ 'droit_supprimer' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Suppression
					$('.btn-supprimer').click(function(){
						var Id = $(this).attr('data-id');

						ModalSupprimer( Id );
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


function ModalAjouterModifier( Id ) { //, Code, Libelle, Description ){
	var Id = Id || '';
/*	var Code = Code || '';
	var Libelle = Libelle || '';
	var Description = Description || ''; */
	var Action;

	if ( Id != '' ) {
		Action = "M";
	} else {
		Action = "A";
	}

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		data: $.param({'tgs_id': Id, 'action': Action}),
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) {
			if ( reponse[ 'Info' ] ) {
				var Code = reponse[ 'Info' ].tgs_code||'';
				var Libelle = reponse[ 'Info' ].tgs_libelle||'';
				var Description = reponse[ 'Info' ].tgs_description||'';
			} else {
				var Code = '';
				var Libelle = '';
				var Description = '';
			}

			var code_HTML = '<div class="row">' +
				'<label class="col-lg-2 col-form-label" for="new_code">' + reponse[ 'L_Code' ] + '</label>' +
				'<div class="col-lg-2">' +
				'<input id="new_code" class="form-control text-uppercase" maxlength="10" required value="' + protegerQuotes( Code ) + '">' +
				'</div>' +
				'</div>' +
				'<div class="row">' +
				'<label class="col-lg-2 col-form-label" for="new_libelle">' + reponse[ 'L_Libelle' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<input id="new_libelle" class="form-control" required value="' + protegerQuotes( Libelle ) + '">' +
				'</div>' +
				'</div>' +
				'<div class="row mb-3">' +
				'<label class="col-lg-2 col-form-label" for="new_description">' + reponse[ 'L_Description' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<textarea id="new_description" class="form-control">' + protegerQuotes( Description ) + '</textarea>' +
				'</div>' +
				'</div>' +
				'<input type="hidden" name="action_form" value="' + Action + '">' +
				'<input type="hidden" id="tgs_id" value="' + Id + '">' +
				'<ul class="nav nav-tabs">' +
//				'<li role="presentation" class="active"><a id="onglet-tsp" href="#">' + reponse['L_Types_Actif_Support'] + '</a></li>' +
				'<li><a class="nav-link active" id="onglet-idn" href="#">' + reponse['L_Utilisateurs'] + '</a></li>' +
				'</ul>' +
				'<div id="onglet-association"></div>';


			if ( Id != '' ) {
				var Bouton = reponse[ 'L_Modifier' ];
				var Titre = reponse[ 'Titre_Modifier'];
			} else {
				var Bouton = reponse[ 'L_Ajouter' ];
				var Titre = reponse[ 'Titre_Ajouter'];
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
				N_Champ = '#new_code';

				$( N_Champ ).focus();

				P_Champ = $( N_Champ );
				V_Champ = P_Champ.val();

				if ( V_Champ != '' ) P_Champ[0].selectionStart = V_Champ.length;

				Lanceur( 'Lister_Utilisateurs', Id );

				$('div#onglet-association').on('maj', function() {
					$('div#Lister_Utilisateurs').show().removeClass('hidden');
				});
			});


			// Après avoir disparu à l'écran la fenêtre est supprimée.
			$('#idModalAjouterModifier').on('hidden.bs.modal', function() {
				$('#idModalAjouterModifier').remove(); // Supprime la modale d'ajout.
			});


			// Sauvegarde les modifications réalisées dans la fenêtre.
			$('#formModalAjouterModifier').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				if ( Action == 'M' ) {
					ModifierEtiquette( Id );
				} else {
					AjouterEtiquette();
				}
			} );
		}
	});

}


function Lanceur( Action, Id ) {
	var Id = Id || '';

	if ( Id != '' ) Id = Id.split('-')[0];

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_' + Action,
		type: 'POST',
		dataType: 'json',
		data: $.param({'id': Id}),
		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				$('div#onglet-association').append( '<div id="' + Action + '" class="hidden">' + reponse[ 'texteHTML'] + '</div>' ).trigger('maj');
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'], '#idModalAjouterModifier' );
			}
		}
	});
}
