function ModalModifier( Element, Langue ) {
	var Langue = Langue || 'fr';

	var Titre;
	var Niveau;

	var Parent = $(Element).parent().parent().parent();

	var Id = Parent.attr('id');

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
			var Prefixe_Code = '';
			var Taille_Max_Code = 20;

			Code = Parent.find('.plmTree-code').text();
			Libelle = Parent.find('.plmTree-label').text();

			if ( Langue == 'fr' ) {
				var Langue_fr = 'selected';
				var Langue_en = '';
			} else {
				var Langue_fr = '';
				var Langue_en = 'selected';
			}

			var code_HTML = '';

			if ( Niveau == 1 ) {
				Titre = reponse[ 'L_Titre_R_M' ];
				var Version = Parent.find('.plmTree-version').text();

				Taille_Code = 'col-lg-6';
				Taille_Libelle = 'col-lg-2';

				Code_Precedent = Code;
				
				Taille_Max_Version = 10;

				code_HTML += '<div class="row">' +
					'<label class="' + Taille_Libelle + ' col-form-label" for="new_version">' + reponse[ 'L_Code' ] + '</label>' +
					'<div class="' + Taille_Code + '">' +
					'<input id="new_code" class="form-control" type="text" maxlength="' + Taille_Max_Code + '" value="' + Code + '" required autofocus>' +
					'</div>' +
					'</div> <!-- .row -->' +
					'<div class="row">' +
					'<label class="' + Taille_Libelle + ' col-form-label" for="new_version">' + reponse[ 'L_Version' ] + '</label>' +
					'<div class="col-lg-2">' +
					'<input id="new_version" class="form-control" type="text" maxlength="' + Taille_Max_Version + '" value="' + Version + '" required>' +
					'</div>';
			} else {
				Titre = reponse[ 'L_Titre_M_M' ];

				Code_Precedent = Code;

				Taille_Code = 'col-lg-2';
				Taille_Libelle = 'col-lg-2';

				var Tmp = Id.split('-');
				var Id_Parent = Tmp[0] + '-' + Tmp[1];
				var Code_Ref = $('#' + Id_Parent).find('.plmTree-code').text();
				var Version_Ref = $('#' + Id_Parent).find('.plmTree-version').text();
				var Libelle_Ref = $('#' + Id_Parent).find('.plmTree-label').text();

				var Type_Mesure = Parent.attr('data-type-mesure');

				code_HTML += '<div class="row">' +
					'<label class="' + Taille_Libelle + ' col-form-label">' + reponse['L_Referentiel'] + '</label>' +
					'<div class="col-lg-9">' +
					'<input class="form-control" type="text" disabled value="' + Code_Ref + ' - ' + Version_Ref + ' - ' + Libelle_Ref + '">' +
					'</div> <!-- .col-lg-9 -->' +
					'</div> <!-- .row -->' +
					'<div class="row">' +
					'<label class="' + Taille_Libelle + ' col-form-label" for="new_code">' + reponse[ 'L_Reference' ] + '</label>' +
					'<div class="col-lg-3">' +
					'<input id="new_code" class="form-control" type="text" maxlength="' + Taille_Max_Code + '" value="' + Code + '" required>' +
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
				if ( Type_Mesure == 1 ) {
					Type_Mesure_1_Select = ' selected';
					Type_Mesure_2_Select = '';
				} else {
					Type_Mesure_1_Select = '';
					Type_Mesure_2_Select = ' selected';					
				}

				code_HTML += '<div class="row">' +
					'<label class="' + Taille_Libelle + ' col-form-label" for="new_type">' + reponse['L_Type'] + '</label>' +
					'<div class="col-lg-4">' +
					'<select id="new_type" class="form-select">' +
					'<option value="1"' + Type_Mesure_1_Select + '>' + reponse['L_MesureReferentiel_Type_1'] + '</option>' +
					'<option value="2"' + Type_Mesure_2_Select + '>' + reponse['L_MesureReferentiel_Type_2'] + '</option>' +
					'</select>' +
					'</div> <!-- .col-lg-4 -->' +
					'</div> <!-- .row -->';
			}
			
			code_HTML += '<div class="row">' +
				'<label class="' + Taille_Libelle + ' col-form-label" for="new_libelle">' + reponse[ 'L_Libelle' ] + '</label>' +
				'<div class="col-lg-9">' +
				'<textarea id="new_libelle" class="form-control" rows="6" required>' + Libelle + '</textarea>' +
				'</div>' +
				'</div>' +
				'<input type="hidden" id="obj_id" value="' + Id + '">' +
				'<input type="hidden" id="msr_code" value="' + Code + '">' +
				'<input type="hidden" id="niveau" value="' + Niveau + '">';


			var Bouton = reponse[ 'L_Modifier' ];


			construireModal( 'idModalModifier',
				Titre,
				code_HTML,
				'idBouton', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formModalModifier',
				'modal-xl' );


			// Affiche la modale qui vient d'être créée
			$('#idModalModifier').modal('show');


			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalModifier').on('shown.bs.modal', function() {
				$('#new_code').focus();

				P_Code = $('#new_code');
				V_Code = P_Code.val();

				if ( V_Code != '' ) P_Code[0].selectionStart = V_Code.length;
			});


			// Après avoir disparu à l'écran la fenêtre est supprimée.
			$('#idModalModifier').on('hidden.bs.modal', function() {
				$('#idModalModifier').remove(); // Supprime la modale d'ajout.
			});


			// Sauvegarde les modifications réalisées dans la fenêtre.
			$('#formModalModifier').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				var Code = $('#new_code').val();
				var Version = $('#new_version').val();
				var Libelle = $('#new_libelle').val();
				var Type = $('#new_type').val();

				var Niveau = $('#niveau').val();
				var Code_Precedent = $('#msr_code').val();

				var obj_id = $('#obj_id').val();
				var ref_obj = $('#' + obj_id);

				var lng_id = ref_obj.attr('data-lng_id');

				var rfc_id = ref_obj.attr('data-rfc_id');

				var msr_id = null;

				if ( Niveau > 1 ) {
					msr_id = ref_obj.attr('data-msr_id');
				}


				$.ajax({
					url: '../../../Loxense-ReferentielsConformite.php?Action=AJAX_Modifier',
					type: 'POST',
					dataType: 'json',
					data: $.param({'code': Code, 'version': Version, 'langue': lng_id, 'libelle': Libelle, 'type': Type,
						'niveau': Niveau, 'rfc_id': rfc_id, 'msr_id': msr_id, 'code_precedent': Code_Precedent}),
					success: function( reponse ) {
						var statut = reponse[ 'statut' ];
						var texteMsg = reponse[ 'texteMsg' ];

						if ( statut == 'success' ) {
							afficherMessage( texteMsg, statut, 'body' );

							$('#idModalModifier').modal('hide'); // Cache la modale d'ajout.

							$('#corps_tableau').on('defiler', function() {
								Code = Code.replace(/\./g, '-');

								//defilerPage( '#RFC-'+rfc_id+'-'+Code, 1 );
								defilerPage( '#RFC-'+rfc_id, 1 );
							});

							trier( $( 'div#entete_tableau div.row div:first'), true );


/*			                ref_obj.find('.plmTree-code').text( Code );

			                if ( Niveau == 1 ) {
				                ref_obj.find('.plmTree-version').text( Version );
			                } else {
			                	var tmp_code = Code_Precedent.replace(/\./g,'-');

		                		$('#RFC-'+rfc_id+'-'+tmp_code).attr('data-type-mesure', Type);

		                		if ( Type == 1 ) {
			                		Img = 'file';
			                		$('#RFC-'+rfc_id+'-'+tmp_code+' .plmTree-spacer span').removeClass('glyphicon-chevron-down').addClass('glyphicon-file');
			                		$('#RFC-'+rfc_id+'-'+tmp_code+' .plmTree-actions button.btn-ajouter').remove();
			                		Bouton_Mesures = '<div class="plmTree-mesures"><button class="btn btn-outline-secondary btn-xs btn-associer" ' +
			                			'onclick="javascript:ModalAssocier(\'RFC-'+rfc_id+'-'+tmp_code + '\', \''+lng_id+'\', this);">' +
			                			'<strong>0</strong> <span class="glyphicon glyphicon-triangle-right"></span></button></div>';
			                		$('#RFC-'+rfc_id+'-'+tmp_code+' .plmTree-actions').before( Bouton_Mesures );
			                	} else {
			                		$('#RFC-'+rfc_id+'-'+tmp_code+' .plmTree-spacer span').removeClass('glyphicon-file').addClass('glyphicon-chevron-down');

				                	Bouton_Ajouter = '<button class="btn btn-outline-secondary btn-xs btn-ajouter" title="Ajouter" type="button" ' +
				                		'onclick="javascript:ModalAjouter(\'RFC-'+rfc_id+'-'+tmp_code + '\', ' + (Number(Niveau) + 1) + ');">' +
				                		'<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>';
				                	$('#RFC-'+rfc_id+'-'+tmp_code+' .plmTree-actions').append( Bouton_Ajouter );
				                	
				                	$('#RFC-'+rfc_id+'-'+tmp_code+' .plmTree-mesures').remove();
			                	}
			                }

			                ref_obj.find('.plmTree-label').text( Libelle ); */
						} else {
							afficherMessage( texteMsg, statut, '#idModalModifier' );							
						}
					}
				});
			});
		}
	});
}


function ModalAssocier( Id, Langue, Obj ) {
	var Langue = Langue || 'fr';

	var Titre;

	var Parent = $('#' + Id);
	var Tmp = Id.split('-');


	$.ajax({
		url: '../../../Loxense-ReferentielsConformite.php?Action=AJAX_Libeller',
		type: 'POST',
		async: false,
		dataType: 'json',
		success: function( reponse ) {
			var L_Code = reponse[ 'L_Reference' ];
			var Prefixe_Code = '';

			if ( Langue == 'fr' ) {
				var Langue_fr = 'selected';
				var Langue_en = '';
			} else {
				var Langue_fr = '';
				var Langue_en = 'selected';
			}

			var Code = Parent.find('.plmTree-code').text();
			var Libelle = Parent.find('.plmTree-label').text();

			var Code_Precedent = Code;

			Titre = reponse[ 'L_Titre_Associer_Mesure' ];

			var code_HTML = '';

			// Récupère les informations du Référentiel de rattachement.
			var Id_Parent = Tmp[0] + '-' + Tmp[1];
			var Code_Ref = $('#' + Id_Parent).find('.plmTree-code').text();
			var Version_Ref = $('#' + Id_Parent).find('.plmTree-version').text();
			var Libelle_Ref = $('#' + Id_Parent).find('.plmTree-label').text();

			code_HTML += '<div class="row">' +
				'<span class="col-lg-3 label-column">' + reponse['L_Referentiel'] + '</span>' +
				'<span class="col-lg-9">' +	Code_Ref + ' - ' + Version_Ref + ' - ' + Libelle_Ref + '</span>' +
				'</div>';


			// Récupère les informations du Thème de rattachement.
			var Id_Parent = Tmp[0] + '-' + Tmp[1] + '-' + Tmp[2];
			var Code_Theme = $('#' + Id_Parent).find('.plmTree-code').text();
			var Libelle_Theme = $('#' + Id_Parent).find('.plmTree-label').text();

			code_HTML += '<div class="row">' +
				'<span class="col-lg-3 label-column">' + reponse['L_ThemeReferentiel'] + '</span>' +
				'<span class="col-lg-9">' +	Code_Theme + ' - ' + Libelle_Theme + '</span>' +
				'</div>';



			// Récupère les informations de l'Objectif de rattachement.
			var Id_Parent = Tmp[0] + '-' + Tmp[1] + '-' + Tmp[2] + '-' + Tmp[3];
			var Code_Objectif = $('#' + Id_Parent).find('.plmTree-code').text();
			var Libelle_Objectif = $('#' + Id_Parent).find('.plmTree-label').text();

			code_HTML += '<div class="row">' +
				'<span class="col-lg-3 label-column">' + reponse['L_ObjectifReferentiel'] + '</span>' +
				'<span class="col-lg-9">' + Code_Objectif + ' - ' + Libelle_Objectif + '</span>' +
				'</div>';


			// Récupère les informations de l'Objectif de rattachement.
			code_HTML += '<div class="row">' +
				'<span class="col-lg-3 label-column">' + reponse['L_MesureReferentiel'] + '</span>' +
				'<span class="col-lg-9">' + Code + ' - ' + Libelle + '</span>' +
				'</div>' +
				'<ul class="nav nav-tabs">' +
				'<li role="presentation" class="active"><a id="onglet-risques" href="#">' + reponse['L_MesuresGeneriques'] + '</a></li>' +
				'</ul>' +
				'<div id="onglet-association"></div>' +
				'<input type="hidden" id="obj_id" value="' + Id + '">';

			var Bouton = reponse[ 'L_Associer' ];


			construireModal( 'idModalAssocier',
				Titre,
				code_HTML,
				'idBouton', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formModalAssocier',
				'modal-lg' );


			// Affiche la modale qui vient d'être créée
			$('#idModalAssocier').modal('show');


			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalAssocier').on('shown.bs.modal', function() {
				$.ajax({
					url: '../../../Loxense-ReferentielsConformite.php?Action=AJAX_Charger_Mesures_Associees',
					type: 'POST',
					async: false,
					dataType: 'json',
					data: $.param({'id': Tmp[4], 'langue': Langue}),
					success: function( reponse ) {
						$('#onglet-association').html( reponse['texteMsg'] );
					}
				});

				//$('#new_code').focus();
			});


			// Après avoir disparu à l'écran la fenêtre est supprimée.
			$('#idModalAssocier').on('hidden.bs.modal', function() {
				$('#idModalAssocier').remove(); // Supprime la modale d'ajout.
			});


			// Sauvegarde les modifications réalisées dans la fenêtre.
			$('#formModalAssocier').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				var Langue = $('#langue_libelle').val();
				var id_mesure_ref = $('#obj_id').val().split('-')[4];

				var ListeAjouter = [];
				var ListeSupprimer = [];


				$.each($('input.choix'), function( index, valeur ) {
					var nouveau_choix = $(valeur).is(':checked');
					var ancien_choix = $(valeur).attr('data-old');
					var id_mesure;

					if ( nouveau_choix != ancien_choix ) {
						id_mesure = $(valeur).attr('id').slice(4);

						if ( nouveau_choix == 1 ) ListeAjouter.push( id_mesure );
						else ListeSupprimer.push( id_mesure );
					}
				});


				if ( ListeSupprimer == '' && ListeAjouter == '' ) {
					afficherMessage( reponse['L_Aucune_Modification'], 'success' );

					$('#idModalAssocier').modal('hide'); // Cache la modale d'association.

					return;
				}


				$.ajax({
					url: '../../../Loxense-ReferentielsConformite.php?Action=AJAX_Associer_Mesures_Generiques',
					type: 'POST',
					dataType: 'json',
					data: $.param({'liste_ID_a_ajouter': ListeAjouter, 'liste_ID_a_supprimer': ListeSupprimer, 'msr_id': id_mesure_ref}),
					success: function( reponse ) {
						var statut = reponse[ 'statut' ];
						var texteMsg = reponse[ 'texteMsg' ];
						var localisation = 'body';

						if ( statut == 'success' ) {
							$('#idModalAssocier').modal('hide'); // Cache la modale d'association.

							$(Obj).find('strong').text( reponse['total'] );
						} else {
							localisation = '#idModalAssocier';
						}

						afficherMessage( texteMsg, statut, localisation );
					}
				});
			});
		}
	});
}
