function ModalSupprimer( Id_Referentiel, Langue ) {
	var Langue = Langue || 'fr';

	var Titre;
	var Niveau;

	var Parent = $(Id_Referentiel).parent().parent().parent();

	var Id = $(Parent).attr('id');

	if ( Parent.hasClass('level-1') ) {
		Niveau = 1;
	} else if ( Parent.hasClass('level-2') ) {
		Niveau = 2;
	} else if ( Parent.hasClass('level-3') ) {
		Niveau = 3;
	} else if ( Parent.hasClass('level-4') ) {
		Niveau = 4;
	} else if ( Parent.hasClass('level-5') ) {
		Niveau = 5;
	} else if ( Parent.hasClass('level-6') ) {
		Niveau = 6;
	}

	$.ajax({
		url: '../../../Loxense-ReferentielsConformite.php?Action=AJAX_Libeller',
		type: 'POST',
		async: false,
		dataType: 'json',
		success: function( reponse ) {
			var L_Code = reponse[ 'L_Reference' ];
			var Taille_Code = 'col-lg-2';
			var Prefixe_Code = '';

			if ( Langue == 'fr' ) {
				var Langue_fr = 'selected';
				var Langue_en = '';
			} else {
				var Langue_fr = '';
				var Langue_en = 'selected';
			}

			var Code = $('#'+Id+ ' .plmTree-code').text(); //Parent.find('.plmTree-code').text();
			var Libelle = $('#'+Id+ ' .plmTree-label').text(); //Parent.find('.plmTree-label').text();

			var Code_Precedent = Code;


			var code_HTML = '';

			if ( Niveau == 1 ) {
				Titre = reponse[ 'L_Titre_R_S' ];
				var Version = Parent.find('.plmTree-version').text();

				Taille_Code = 'col-lg-6';
				Taille_Libelle = 'col-lg-2';

				Code_Precedent = Code;
				
				code_HTML += '<div class="row">' +
					'<label class="' + Taille_Libelle + ' col-form-label" for="new_version">' + reponse[ 'L_Code' ] + '</label>' +
					'<div class="' + Taille_Code + '">' +
					'<input id="new_code" class="form-control" type="text" value="' + Code + '" disabled>' +
					'</div>' +
					'</div> <!-- .row -->' +
					'<div class="row">' +
					'<label class="' + Taille_Libelle + ' col-form-label" for="new_version">' + reponse[ 'L_Version' ] + '</label>' +
					'<div class="col-lg-2">' +
					'<input id="new_version" class="form-control" type="text" value="' + Version + '" disabled>' +
					'</div>';
			} else {
				Titre = reponse[ 'L_Titre_M_S' ];

				Code_Precedent = Code;

				Taille_Code = 'col-lg-2';
				Taille_Libelle = 'col-lg-2';

				var Tmp = Id.split('-');
				var Id_Parent = Tmp[0] + '-' + Tmp[1];

				var Code_Ref = $('#' + Id_Parent + ' .plmTree-code').text();
				var Version_Ref = $('#' + Id_Parent + ' .plmTree-version').text();
				var Libelle_Ref = $('#' + Id_Parent + ' .plmTree-label').text();

				code_HTML += '<div class="row">' +
					'<label class="' + Taille_Libelle + ' col-form-label">' + reponse['L_Referentiel'] + '</label>' +
					'<div class="col-lg-9">' +
					'<input class="form-control" type="text" disabled value="' + Code_Ref + ' - ' + Version_Ref + ' - ' + Libelle_Ref + '">' +
					'</div> <!-- .col-lg-9 -->' +
					'</div> <!-- .row -->' +
					'<div class="row">' +
					'<label class="' + Taille_Libelle + ' col-form-label" for="new_code">' + reponse[ 'L_Reference' ] + '</label>' +
					'<div class="col-lg-3">' +
					'<input id="new_code" class="form-control" type="text" value="' + Code + '" disabled>' +
					'</div> <!-- Taille_Code -->';
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
					'<select id="new_type" class="form-select" disabled>' +
					'<option value="1">' + reponse['L_MesureReferentiel_Type_1'] + '</option>' +
					'<option value="2">' + reponse['L_MesureReferentiel_Type_2'] + '</option>' +
					'</select>' +
					'</div> <!-- .col-lg-4 -->' +
					'</div> <!-- .row -->';
			}
			
			code_HTML += '<div class="row">' +
				'<label class="' + Taille_Libelle + ' col-form-label" for="new_libelle">' + reponse[ 'L_Libelle' ] + '</label>' +
				'<div class="col-lg-9">' +
				'<textarea id="new_libelle" class="form-control" rows="6" disabled>' + Libelle + '</textarea>' +
				'</div>' +
				'</div>';
			
			if ( Niveau == 1 ) {
				Libelle_Avertissement = reponse[ 'L_Referentiel_Avertissement_Suppression' ];
			} else {
				Libelle_Avertissement = reponse[ 'L_Mesure_Avertissement_Suppression' ];
			}

			code_HTML += '<div class="row text-center">' +
				'<strong>' + Libelle_Avertissement + '</strong>' +
				'</div>' +
				'<input type="hidden" id="obj_id" value="' + Id + '">' +
				'<input type="hidden" id="msr_code" value="' + Code + '">' +
				'<input type="hidden" id="niveau" value="' + Niveau + '">';


			var Bouton = reponse[ 'L_Supprimer' ];


			construireModal( 'idModalSupprimer',
				Titre,
				code_HTML,
				'idBouton', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formModalSupprimer',
				'modal-xl' );


			// Affiche la modale qui vient d'être créée
			$('#idModalSupprimer').modal('show');


			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalSupprimer').on('shown.bs.modal', function() {
				$('.btn-fermer').focus();
			});


			// Après avoir disparu à l'écran la fenêtre est supprimée.
			$('#idModalSupprimer').on('hidden.bs.modal', function() {
				$('#idModalSupprimer').remove(); // Supprime la modale d'ajout.
			});


			// Sauvegarde les modifications réalisées dans la fenêtre.
			$('#formModalSupprimer').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				var Niveau = $('#niveau').val();
				var Code_Precedent = $('#code_precedent').val();

				var msr_code = $('#msr_code').val();
				var id_libelle = $('#obj_id').val();
				var ref_obj = $('#' + id_libelle );

				var lng_obj = ref_obj.attr('data-lng_id');
				var rfc_id = ref_obj.attr('data-rfc_id');
				var msr_id;
				
				if ( Niveau > 1 ) {
					msr_id = ref_obj.attr('data-msr_id');
				}


				$.ajax({
					url: '../../../Loxense-ReferentielsConformite.php?Action=AJAX_Supprimer',
					type: 'POST',
					dataType: 'json',
					data: $.param({'niveau': Niveau, 'rfc_id': rfc_id, 'msr_id': msr_id, 'id_libelle': id_libelle, 'langue': lng_obj,
						'msr_code': msr_code}),
					success: function( reponse ) {
						var statut = reponse[ 'statut' ];
						var texteMsg = reponse[ 'texteMsg' ];

						if ( statut == 'success' ) {
							afficherMessage( texteMsg, statut, 'body' );

							$.each($('div[id^="' + id_libelle + '"]'), function( index, valeur ) {
								$(valeur).remove();
							});

							$('#totalOccurrences').text( reponse['total'] );

							$('#idModalSupprimer').modal('hide'); // Cache la modale.

							trier( $( 'div#entete_tableau div.row div:first'), true );
						} else {
							afficherMessage( texteMsg, statut, '#idModalSupprimer' );
						}
					}
				});
			});
		}
	});
}
