// ======================================================================
// Mise en place des écoutes sur les événements produits par les objets.
$(function() {
	$( '#titre-menu .btn-ajouter' ).on( 'click', function(){
		ModalAjouter( '', 1 );
	});


	$('#titre-menu .btn-importer').on('click', function() {
		importerReferentiel();
	});
});


function ModalAjouter( Id, Niveau ) {
	var Id = Id || '';
	var Niveau = Niveau || 2;

	var Titre;

	var Langue = $('#langue_libelle option:selected').val();

	if ( Id != '' || Id === null ) {
		var Parent = $( '#' + Id );
	}

	$.ajax({
		url: '../../../Loxense-ReferentielsConformite.php?Action=AJAX_Libeller',
		type: 'POST',
		async: false,
		dataType: 'json',
		success: function( reponse ) {
			var Taille_Code = 'col-lg-2';
			var Prefixe_Code = '';
			var Taille_Max_Code = 20;

			if ( Langue == 'fr' ) {
				var Langue_fr = 'selected';
				var Langue_en = '';
			} else {
				var Langue_fr = '';
				var Langue_en = 'selected';
			}


			if ( Niveau == 1 ) {
				Titre = reponse[ 'L_Titre_R_A' ];

				L_Code = reponse[ 'L_Code' ];
				Taille_Code = 'col-lg-3';

				Taille_Libelle = 'col-lg-2';
				
				Taille_Max_Version = 10;
			}


			if ( Niveau > 1 ) {
				Titre = reponse[ 'L_Titre_M_A' ];

				L_Code = reponse[ 'L_Reference' ];

				Taille_Libelle = 'col-lg-2';

				Code = Parent.find('.plmTree-code').text();
				Prefixe_Code = Code + '.';
			}


			var code_HTML = '';

			if ( Niveau == 1 ) {
				code_HTML += '<div class="row">' +
					'<label class="' + Taille_Libelle + ' col-form-label" for="new_version">' + reponse[ 'L_Code' ] + '</label>' +
					'<div class="' + Taille_Code + '">' +
					'<input id="new_code" class="form-control" type="text" maxlength="' + Taille_Max_Code + '" required autofocus>' +
					'</div>' +
					'</div> <!-- .row -->' +
					'<div class="row">' +
					'<label class="' + Taille_Libelle + ' col-form-label" for="new_version">' + reponse[ 'L_Version' ] + '</label>' +
					'<div class="col-lg-2">' +
					'<input id="new_version" class="form-control" type="text" maxlength="' + Taille_Max_Version + '" required>' +
					'</div>';
			} else {
				var Tmp = Id.split('-');
				var Id_Parent = Tmp[0] + '-' + Tmp[1];
				var Code_Ref = $('#' + Id_Parent).find('.plmTree-code').text();
				var Version_Ref = $('#' + Id_Parent).find('.plmTree-version').text();
				var Libelle_Ref = $('#' + Id_Parent).find('.plmTree-label').text();

				code_HTML += '<div class="row">' +
					'<label class="' + Taille_Libelle + ' col-form-label">' + reponse['L_Referentiel'] + '</label>' +
					'<div class="col-lg-9">' +
					'<input class="form-control" type="text" disabled value="' + Code_Ref + ' - ' + Version_Ref + ' - ' + Libelle_Ref + '">' +
					'</div> <!-- .col-lg-9 -->' +
					'</div> <!-- .row -->' +
					'<div class="row">' +
					'<label class="' + Taille_Libelle + ' col-form-label" for="new_code">' + L_Code + '</label>';

				if ( Niveau == 2 ) {
					code_HTML += '<div class="col-lg-3">' +
						'<input id="new_code" class="form-control" type="text" maxlength="' + Taille_Max_Code + '" required>' +
						'</div> <!-- Taille_Code -->';
				} else {
					code_HTML += '<div class="col-lg-4 assembled-group">' +
						'<input id="code_prec" type="text" class="form-control field text-right" value="' + Prefixe_Code + '" disabled>' +
						'<input id="new_code" type="text" class="form-control field" maxlength="6" required>' +
						'</div><!-- .col-lg-4 .assembled-group -->';
				}
			}


			code_HTML += '<label class="col-lg-2 col-form-label" for="new_langue">' + reponse[ 'L_Langue' ] + '</label>' +
				'<div class="col-lg-4">' +
				'<select id="new_langue" class="form-select" disabled>' +
				'<option value="fr" ' + Langue_fr + '>' + reponse[ 'L_Langue_fr' ] + '</option>' +
				'<option value="en" ' + Langue_en + '>' + reponse[ 'L_Langue_en' ] + '</option>' +
				'</select>' +
				'</div>' +
				'</div>';
			
			if ( Niveau > 1 ) {
				code_HTML += '<div class="row">' +
					'<label class="' + Taille_Libelle + ' col-form-label" for="new_type">' + reponse['L_Type'] + '</label>' +
					'<div class="col-lg-4">' +
					'<select id="new_type" class="form-select">' +
					'<option value="1">' + reponse['L_MesureReferentiel_Type_1'] + '</option>' +
					'<option value="2">' + reponse['L_MesureReferentiel_Type_2'] + '</option>' +
					'</select>' +
					'</div> <!-- .col-lg-4 -->' +
					'</div> <!-- .row -->';
			}
			
			code_HTML += '<div class="row">' +
				'<label class="' + Taille_Libelle + ' col-form-label" for="new_libelle">' + reponse[ 'L_Libelle' ] + '</label>' +
				'<div class="col-lg-9">' +
				'<textarea id="new_libelle" class="form-control" rows="3" required></textarea>' +
				'</div>' +
				'</div>' +
				'<input type="hidden" id="obj_id" value="' + Id + '">' +
				'<input type="hidden" id="niveau" value="' + Niveau + '">';


			var Bouton = reponse[ 'L_Ajouter' ];


			construireModal( 'idModalAjouter',
				Titre,
				code_HTML,
				'idBouton', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formModalAjouter',
				'modal-lg' );


			// Affiche la modale qui vient d'être créée
			$('#idModalAjouter').modal('show');


			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalAjouter').on('shown.bs.modal', function() {
				$('#new_code').focus();

				P_Code = $('#new_code');
				V_Code = P_Code.val();

				if ( V_Code != '' ) P_Code[0].selectionStart = V_Code.length;
			});


			// Après avoir disparu à l'écran la fenêtre est supprimée.
			$('#idModalAjouter').on('hidden.bs.modal', function() {
				$('#idModalAjouter').remove(); // Supprime la modale d'ajout.
			});


			// Sauvegarde les modifications réalisées dans la fenêtre.
			$('#formModalAjouter').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				var Langue = $('#langue_libelle :selected').val();
				var Libelle = $('#new_libelle').val();
				var Niveau = $('#niveau').val();
				var obj_id = $('#obj_id').val();
				var tmpCode = $('#new_code').val();
				var Version = null;
				var Code;
				var Type = $('#new_type :selected').val();

				if ( Niveau == 1 ) {
					Version = $('#new_version').val();
				}

				if ( Niveau > 2 ) {
					Code = Prefixe_Code + tmpCode;
				} else {
					Code = tmpCode;
				}
				
				//alert(Type+' - '+Code+' - '+Langue+' - '+Libelle+' - '+Niveau+' - '+obj_id+' - '+Version);
				//return;


				$.ajax({
					url: '../../../Loxense-ReferentielsConformite.php?Action=AJAX_Ajouter',
					type: 'POST',
					dataType: 'json',
					data: $.param({'code': Code, 'version': Version, 'langue': Langue, 'libelle': Libelle, 'niveau': Niveau,
						'obj_id': obj_id, 'type': Type}),
					success: function( reponse ) {
						var statut = reponse[ 'statut' ];
						var texteMsg = reponse[ 'texteMsg' ];

						if ( statut == 'success' ) {
							$('#idModalAjouter').modal( 'hide' );

							$('#corps_tableau').on('defiler', function() {
								defilerPage( '#'+obj_id+'-'+tmpCode, 1 );
							});

							trier( $( 'div#entete_tableau div.row div:first'), true );

							afficherMessage( texteMsg, statut, 'body' );
						} else {
							afficherMessage( texteMsg, statut, '.modal-body', 100, 'n' );
						}
					}
				});
			});
		}
	});
}


function importerReferentiel() {
	$.ajax({
		url: Parameters['URL_BASE']+'/Loxense-ReferentielsConformite.php?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
		data: $.param({'ChargerRefentiels': $('#langue_libelle').val()}),

		success: function( reponse ) {
			var Corps =
				'<form id="formCharger" method="post" class="form-horizontal">' + // Bloc horizontal
				reponse['Referentiels'] +
				'</form>';

			construireModal( 'idModal',
				reponse[ 'L_Importer_Referentiel' ],
				Corps,
				'btnImporter', reponse[ 'L_Importer' ],
				true, reponse[ 'L_Fermer' ],
				'formTelecharger', 'modal-lg' );

			$('#idModal').modal('show'); // Affiche la modale qui vient d'être créée


			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModal').on('shown.bs.modal', function() {
				$('input:first').focus();
			});


			// Détruit la modale quand cette dernière est désactivée.
			$('#idModal').on('hidden.bs.modal', function() {
				$('#idModal').remove();
			});

			$('#formTelecharger').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.
				
				var ReferentielsACharger = [];

				$( "#formTelecharger input:checked" ).each(function( index ) {
					ReferentielsACharger[ index ] = $( this ).val();
				});
				
				if ( ReferentielsACharger.length > 0 ) {
					$.ajax({
						url: Parameters['URL_BASE']+'/Loxense-ReferentielsConformite.php?Action=AJAX_Referentiels_A_Charger',
						type: 'POST',
						dataType: 'json',
						data: $.param({'Referentiels': ReferentielsACharger}),
	
						success: function( reponse ) {
							var statut = reponse[ 'statut' ];
							var texteMsg = reponse[ 'texteMsg' ];

							trier( $( 'div#entete_tableau div.row div:first'), true );

							afficherMessage( texteMsg, statut, 'body' );
						}
					});
				}

				$('#idModal').modal( 'hide' );
			});
		}
	});
}
