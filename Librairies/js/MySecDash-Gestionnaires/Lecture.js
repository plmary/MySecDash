$(function() {
	// Charge les données du tableau.
    trier( $( 'div#entete_tableau div.row div:first'), false );


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
		url: Parameters['URL_BASE'] + '/Loxense-Gestionnaires.php?Action=AJAX_Trier',
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
						var Libelle = $(this).parent().parent().find('div[data-src="gst_libelle"]').find('span').text();

						ModalAjouterModifier( Id, Libelle );
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


function ModalAjouterModifier( Id, Libelle ){
	var Id = Id || '';
	var Libelle = Libelle || '';
	var Action;

	if ( Id != '' ) {
		Action = "M";
	} else {
		Action = "A";
	}

	$.ajax({
		url: Parameters['URL_BASE'] + '/Loxense-Gestionnaires.php?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
		success: function( reponse ) {
			var code_HTML = '<div class="row mb-3">' +
				'<label class="col-lg-2 col-form-label" for="new_libelle">' + reponse[ 'L_Libelle' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<input id="new_libelle" class="form-control" required value="' + protegerQuotes( Libelle ) + '">' +
				'</div>' +
				'</div>' +
				'<input type="hidden" name="action_form" value="' + Action + '">' +
				'<input type="hidden" id="gst_id" value="' + Id + '">' +
				'<ul class="nav nav-tabs">' +
				'<li><a class="nav-link active" id="onglet-tsp" href="#">' + reponse['L_Types_Actif_Support'] + '</a></li>' +
				'<li><a class="nav-link" id="onglet-idn" href="#">' + reponse['L_Utilisateurs'] + '</a></li>' +
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
				N_Champ = '#new_libelle';

				$( N_Champ ).focus();

				P_Champ = $( N_Champ );
				V_Champ = P_Champ.val();

				if ( V_Champ != '' ) P_Champ[0].selectionStart = V_Champ.length;

				Lanceur( 'Lister_Types_Supports', Id );
				Lanceur('Lister_Utilisateurs', Id );

				$('div#onglet-association').on('maj', function() {
					$('div#Lister_Types_Supports').show().removeClass('hidden');
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
					ModifierGestionnaire( Id );
				} else {
					AjouterGestionnaire();
				}
			} );

			// Affiche le contenu de l'onglet sélectionné.
			$('.nav-link').on( 'click', function() {
				$('.nav-link').removeClass('active');
				$(this).addClass('active');

				if ( $(this).attr('id') == 'onglet-tsp' ) {
					Nom_Onglet = 'Lister_Types_Supports';
				} else if ( $(this).attr('id') == 'onglet-idn' ) {
					Nom_Onglet = 'Lister_Utilisateurs';
				}

				$('div[id^="Lister_"]').hide();
				$('div#' + Nom_Onglet ).show().removeClass('hidden');
				$('div#' + Nom_Onglet + ' input:first' ).focus();
			});

		}
	});

}


function Lanceur( Action, Id ) {
	var Id = Id || '';

	if ( Id != '' ) Id = Id.split('-')[0];

	$.ajax({
		url: Parameters['URL_BASE'] + '/Loxense-Gestionnaires.php?Action=AJAX_' + Action,
		type: 'POST',
		dataType: 'json',
		data: $.param({'id': Id}),
		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				$('div#onglet-association').append( '<div id="' + Action + '" style="display: none;">' + reponse[ 'texteHTML'] + '</div>' ).trigger('maj');
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'], '#idModalAjouterModifier' );
			}
		}
	});
}
