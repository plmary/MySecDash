function ModalModifier( Id ){
	var ActionID = Id.split('-')[0];
	var MesureID = Id.split('-')[1].split('_')[1];

	$.ajax({
		url: '../../../Loxense-Actions.php?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
		data: $.param({'Creer_Action': 'N', 'ActionID': ActionID, 'MesureID': MesureID}),

		success: function( reponse ) {
			if ( reponse[ 'statut'] != 'success' ) {
				afficherMessage( reponse['texteMsg'], reponse['statut'], 'body' );
				return -1;
			}

			if ( reponse[ 'action']['act_date_fin_p'] == null ) reponse[ 'action']['act_date_fin_p'] = '';
			if ( reponse[ 'action']['act_date_debut_r'] == null ) reponse[ 'action']['act_date_debut_r'] = '';
			if ( reponse[ 'action']['act_date_fin_r'] == null ) reponse[ 'action']['act_date_fin_r'] = '';

			if ( reponse[ 'action']['act_description'] == null ) reponse[ 'action']['act_description'] = '';

            var Corps =
                '<div class="row">' +
                 '<label class="col-lg-2 col-form-label">' + reponse[ 'L_Acteur' ] + '</label>' +
                 '<div class="col-lg-10">' +
                  '<select id="idn_id" class="form-select">' +
                  reponse[ 'liste_utilisateurs' ] +
                  '</select>' +
                 '</div>' +
                '</div>' +

                '<div class="row">' +
                ' <label class="col-lg-2 col-form-label" for="act_libelle">' + reponse[ 'L_Libelle' ] + '</label>' +
                ' <div class="col-lg-10">' +
                '  <input id="act_libelle" class="form-control" type="text" maxlength="100" value="' + reponse['action']['act_libelle'] + '" required>' +
                ' </div>' +
                '</div>' +

                '<div class="row">' +
                ' <label class="col-lg-2 col-form-label" for="act_priorite">' + reponse[ 'L_Priorite' ] + '</label>' +
                ' <div class="col-lg-2">' +
                '  <input id="act_priorite" class="form-control" type="number" value="' + reponse['action']['act_priorite'] + '" required>' +
                ' </div>' +
                ' <label class="col-lg-2 col-form-label text-end" for="act_frequence">' + reponse[ 'L_Frequence' ] + '</label>' +
                ' <div class="col-lg-4">' +
                '  <select id="act_frequence" class="form-select" required>' + reponse[ 'Liste_Frequences' ] + '</select>' +
                ' </div>' +
                '</div>' +

                '<div class="row">' +
                ' <label class="col-lg-2 col-form-label" for="act_statut">' + reponse[ 'L_Statut' ] + '</label>' +
                ' <div class="col-lg-3">' +
                '  <select id="act_statut" class="form-select" required>' + reponse[ 'Liste_Statuts' ] + '</select>' +
                ' </div>' +
                '</div>' +

                '<div class="clearfix">&nbsp;</div>' +


                 // =====================
                 // Gestion des onglets.
                 '<ul class="nav nav-tabs">' +
                  '<li><a class="nav-link" id="selectionner_mesure" href="#">' + reponse[ 'L_Mesure'] + '</a></li>' +
                  '<li><a class="nav-link" id="gerer_dates" href="#">' + reponse[ 'L_Dates'] + '</a></li>' +
                  '<li><a class="nav-link" id="gerer_description" href="#">' + reponse[ 'L_Description'] + '</a></li>' +
                  '<li><a class="nav-link" id="gerer_preuves" href="#">' + reponse[ 'L_Preuves'] + '</a></li>' +
                 '</ul>' +
                
                 '<div id="corps_onglet" class="onglet-association">' +
                 
                  // Onglet : Sélection d'une mesure.
                  '<div id="onglet-selectionner_mesure" style="display: none;">' +
                   '<div class="row">' +
                   ' <div class="col-lg-12 mesure">' +
                   reponse[ 'Liste_Mesures' ] +
                   ' </div>' +
                   '</div>' +
                  '</div>' +

                  // Onglet : Gestion des dates
                  '<div id="onglet-gerer_dates" style="display: none;">' +
                    '<div class="row" style="padding-top: 12px">' + // Bloc une ligne
                    '<div class="row col-lg-6">' +
                    '<label class="col-lg-5 col-form-label" for="act_date_debut_p">' + reponse[ 'L_Date_Debut_p' ] + '</label>' +
                    '<div class="col-lg-4">' +
                    '<input id="act_date_debut_p" class="form-control" type="date" maxlength="10" placeholder="' + reponse[ 'L_Format_Date'] +
                    	'" min="' + reponse[ 'JourCourant'] + '" value="' + reponse[ 'action']['act_date_debut_p']  + '" required>' +
                    '</div>' +
                    '</div>' +

                    '<div class="row col-lg-6">' +
                    '<label class="col-lg-5 col-form-label" for="act_date_fin_p">' + reponse[ 'L_Date_Fin_p' ] + '</label>' +
                    '<div class="col-lg-4">' +
                    '<input id="act_date_fin_p" class="form-control" type="date" maxlength="10" placeholder="' + reponse[ 'L_Format_Date'] +
                    	'" min="' + reponse[ 'JourCourant'] + '" value="' + reponse[ 'action']['act_date_fin_p']  + '" required>' +
                    '</div>' +
                    '</div>' +
                    '</div>' + // Fin bloc une ligne

                    '<div class="row" style="padding-top: 12px">' + // Bloc une ligne
                    '<div class="row col-lg-6">' +
                    '<label class="col-lg-5 col-form-label" for="act_date_debut_r">' + reponse[ 'L_Date_Debut_r' ] + '</label>' +
                    '<div class="col-lg-4">' +
                    '<input id="act_date_debut_r" class="form-control" type="date" maxlength="10" placeholder="' + reponse[ 'L_Format_Date'] +
                    	'" min="' + reponse[ 'JourCourant'] + '" value="' + reponse[ 'action']['act_date_debut_r']  + '">' +
                    '</div>' +
                    '</div>' +

                    '<div class="row col-lg-6">' +
                    '<label class="col-lg-5 col-form-label" for="act_date_fin_r">' + reponse[ 'L_Date_Fin_r' ] + '</label>' +
                    '<div class="col-lg-4">' +
                    '<input id="act_date_fin_r" class="form-control" type="date" maxlength="10" placeholder="' + reponse[ 'L_Format_Date'] +
                    	'" min="' + reponse[ 'JourCourant'] + '" value="' + reponse[ 'action']['act_date_fin_r']  + '">' +
                    '</div>' +
                    '</div>' +
                    '</div>' + // Fin bloc une ligne
                  '</div>' +

                  '<div id="onglet-gerer_description" style="display: none;">' +
                    '<div class="form-horizontal">' + // Bloc horizontal
                    '<div class="row">' +
                    '<div class="col-lg-12">' +
                    '<textarea id="act_description" class="form-control" rows="6">' + reponse[ 'action']['act_description']  + '</textarea>' +
                    '</div>' +
                    '</div>' +
                    '</div>' + // Fin bloc horizontal
                  '</div>' +

                  '<div id="onglet-gerer_preuves" style="display: none;overflow-y: auto;min-height: 150px;max-height: 250px;">' +

    		        '<div class="row text-center" style="background-color: grey;color:white;padding:6px 0">' +
    		         '<div class="col-lg-12">' +
					  '<button class="btn btn-outline-secondary btn-ajouter-a" type="button" title="' + reponse['L_Preuve_Transferer'] + '">' +
					   '<i class="bi-file-earmark-arrow-up"></i>' +
					  '</button>' +
    		         '</div>' +
    		        '</div>' +

                    '<div id="liste-preuves" class="form-horizontal">' + // Bloc horizontal
                    reponse['Liste_Preuves'] +
                    '</div>' + // Fin bloc horizontal

                  '</div>' +

                 '</div>'; // corps_onglet

            construireModal( 'idModalModifier',
                reponse[ 'Titre_Modifier' ],
                Corps,
                'idBoutonAjouter', reponse[ 'L_Modifier' ],
                true, reponse[ 'L_Fermer' ],
                'formModifier', 'modal-xl' );

            $('#idModalModifier').modal('show'); // Affiche la modale qui vient d'être créée

            // Attend que la modale soit affichée avant de donner le focus au champ.
            $('#idModalModifier').on('shown.bs.modal', function() {
                $('.nav-tabs a:first').trigger('click');

                $('#idn_id').focus();
                //$('.selectpicker').selectpicker('show');
            });

            // Supprime la modale après l'avoir caché.
            $('#idModalModifier').on('hidden.bs.modal', function() {
                $('#idModalModifier').remove();
            });


            // Affiche la liste des Mesures disponibles pour cet utilisateur.
            $('#selectionner_mesure').on('click', function() {
                $('.nav-link').removeClass('active');
                $('a#selectionner_mesure').addClass('active');
                
                $('div[id^=onglet-]').hide();

                $('#onglet-selectionner_mesure').show();
                $('div#onglet-selectionner_mesure input:first').focus();
            });


            // Affiche les Dates de l'Action.
            $('#gerer_dates').on('click', function() {
                $('.nav-link').removeClass('active');
                $('a#gerer_dates').addClass('active');
                
                $('div[id^=onglet-]').hide();

                $('#onglet-gerer_dates').show();
                $('div#onglet-gerer_dates input:first').focus();
            });


            // Affiche la Description de l'Action.
            $('#gerer_description').on('click', function() {
                $('.nav-link').removeClass('active');
                $('a#gerer_description').addClass('active');
                
                $('div[id^=onglet-]').hide();

                $('#onglet-gerer_description').show();
                $('div#onglet-gerer_description textarea:first').focus();
    			document.getElementById($('div#onglet-gerer_description textarea:first').attr('id')).selectionStart =
    				$('div#onglet-gerer_description textarea:first').text().length;
            });


            // Affiche les Preuves associées à l'action.
            $('#gerer_preuves').on('click', function() {
                $('.nav-link').removeClass('active');
                $('a#gerer_preuves').addClass('active');

                $('div[id^=onglet-]').hide();

                $('#onglet-gerer_preuves').show();
                $('div#onglet-gerer_preuves button:first').focus();

				$('#idModalModifier').trigger( 'boutons-preuves' );
            });


            // Affiche la zone de téléchargement des preuves.
            $('#onglet-gerer_preuves .btn-ajouter-a').on('click', function() {
                //$('#zone-charger_preuves').show();

	            $('#idModalModifier').append('<div id="zone-charger_preuves" class="zone_ajout_contextuel_action">' +
	                    '<form id="formCharger" method="post" enctype="multipart/form-data" class="form-horizontal">' + // Bloc horizontal
	                    '<div class="row mb-3">' +
		                ' <label class="col-lg-2 col-form-label" for="prv_libelle">' + reponse[ 'L_Libelle' ] + '</label>' +
		                ' <div class="col-lg-10">' +
		                '  <input type="text" id="prv_libelle" class="form-control" required>' +
		                '  <input type="hidden" id="act_id" value="' + ActionID + '">' +
		                ' </div>' +
	                    '</div>' +
	                    '<div class="row">' +
	                     '<div class="col-lg-12">' +
	                       '<input type="file" id="fichier_interne" name="fichier" multiple />' +
	                     '</div>' +
	                    '</div>' +
	                    '<div class="row">' +
	                    '<div class="col-lg-12">' +
	                     '<div id="statut_tranfert" class="statut_tranfert text-center">&nbsp;</div>' +
	                    '</div>' +
	                    '</div>' +
	                    '<div class="text-center">' +
						 '<a class="btn btn-outline-secondary" href="javascript:fermerZoneAjoutAction();">'+ reponse['L_Fermer'] + '</a>&nbsp;' +
						 '<a class="btn btn-primary" href="javascript:sauverZoneAjoutAction( \'' + reponse['L_Fichier_Non_Autorise'] + ' \');">' +
						 reponse['L_Transferer_Fichier'] + '</a>' +
						'</div>' +
	                    '</form>' + // Fin bloc horizontal
	                    '<script>' +
	                    'function fermerZoneAjoutAction() {' +
	                    '$(\'#zone-charger_preuves\').remove();' +
	                    '}' +
	                    '</script>' +
	                  '</div>');

				$('#zone-charger_preuves input:first').focus();
			});


			$('#idModalModifier').on( 'boutons-preuves', function() {
				$('.btn-v-preuve').click( function( event ) {
                    var obj_courant = $(this);
                    event.preventDefault(); // Laisse le contrôle au Javascript.

					var crs_id = obj_courant.attr('data-crs_id');
					var prv_id = obj_courant.attr('data-prv_id');
					var act_id = obj_courant.attr('data-act_id');

					var prv_localisation = $('#PRV_'+prv_id+' .prv_localisation').text();
					var repertoire = $('#PRV_'+prv_id+' .prv_localisation').attr('data-rep');

					window.open( repertoire + '/' + crs_id + '___' + prv_localisation );
					//window.location = repertoire + '/' + crs_id + '___' + prv_localisation;
				});


				$('.btn-t-preuve').click( function( event ) {
                    var obj_courant = $(this);
                    event.preventDefault(); // Laisse le contrôle au Javascript.

					var crs_id = obj_courant.attr('data-crs_id');
					var prv_id = obj_courant.attr('data-prv_id');
					var act_id = obj_courant.attr('data-act_id');

					var prv_localisation = $('#PRV_'+prv_id+' .prv_localisation').text();
					var repertoire = $('#PRV_'+prv_id+' .prv_localisation').attr('data-rep');

					telechargerFichier( crs_id + '___' + prv_localisation );
				});


				$('.btn-s-preuve').click( function( event ) {
                    var obj_courant = $(this);
                    event.preventDefault(); // Laisse le contrôle au Javascript.

					var crs_id = obj_courant.attr('data-crs_id');
					var prv_id = obj_courant.attr('data-prv_id');
					var act_id = obj_courant.attr('data-act_id');

                    var prv_libelle = $('#PRV_'+prv_id+' .prv_libelle').text();
					var prv_localisation = $('#PRV_'+prv_id+' .prv_localisation').text();
                    var prv_confirmation = reponse['L_Confirmer_Supprimer_Preuve'].replace("%prv_libelle", prv_libelle ).replace("%prv_fichier", prv_localisation);

                    $('#idBoutonAjouter').attr('disabled', 'disabled');

                    $('#idModalModifier').append('<div id="zone-charger_preuves" class="zone_ajout_contextuel_action">' +
                            '<form id="formCharger" method="post" enctype="multipart/form-data" class="form-horizontal">' + // Bloc horizontal
                            '<div class="row">' +
                                prv_confirmation +
                            '</div>' +
                            '<div class="text-center">' +
                             '<a class="btn btn-outline-secondary" href="javascript:fermerZoneAjoutAction();">' + reponse['L_Non'] + '</a>&nbsp;' +
                             '<a class="btn btn-primary" href="javascript:supprimerPreuve( \'' + crs_id + '\',\'' + prv_id + '\',\'' + 
                                act_id + '\',\'' + prv_localisation + '\' );">' + reponse['L_Oui'] + '</a>' +
                            '</div>' +
                            '</form>' + // Fin bloc horizontal
                            '<script>' +
                            'function fermerZoneAjoutAction() {' +
                            '$(\'#zone-charger_preuves\').remove();' +
                            '$(\'#idBoutonAjouter\').removeAttr(\'disabled\');' +
                            '}' +
                            '</script>' +
                          '</div>');

                    $('#zone-charger_preuves input:first').focus();
				});
			});


//            $('#formModifier').submit( function( event ) { // Gère la soumission du formulaire.
            $('#idBoutonAjouter').click( function( event ) { // Gère la soumission du formulaire.
                event.preventDefault(); // Laisse le contrôle au Javascript.

                modifierAction( Id );
            } );
		}
	});
}


function modifierAction( Id ) {
	var idn_id, acteur;
	var act_libelle;
	var act_priorite;
	var act_frequence_code, act_frequence_libelle;
	var act_statut_code, act_statut_libelle;
	var mcr_id, mcr_libelle;
	var date_debut, act_date_debut_p, act_date_fin_p;
	var date_fin, act_date_debut_r, act_date_fin_r;
	var act_description;
	var spp_nom;

	idn_id = $('#idn_id').val();
	acteur = $('#idn_id option:selected').text();

	spp_nom = $('#spp_nom').val();

	act_id = Id.split('-')[0];
	act_libelle = $('#act_libelle').val();

	act_priorite = $('#act_priorite').val();

	act_frequence_code = $('#act_frequence').val();
	act_frequence_libelle = $('#act_frequence option:selected').text();

	act_statut_code = $('#act_statut').val();
	act_statut_libelle = $('#act_statut option:selected').text();

	mcr_id = $('input[name="mcr_id"]:checked').val();
	mcr_libelle = $('input[name="mcr_id"]:checked').parent().text().split(' => ')[0];
	spp_nom = $('input[name="mcr_id"]:checked').attr('data-spp_nom');

	act_date_debut_p = $('#act_date_debut_p').val();
	act_date_fin_p = $('#act_date_fin_p').val();

	act_date_debut_r = $('#act_date_debut_r').val();
	act_date_fin_r = $('#act_date_fin_r').val();

	if ( act_date_debut_r != '' ) date_debut = act_date_debut_r;
	else date_debut = act_date_debut_p;

	if ( act_date_fin_r != '' ) date_fin = act_date_fin_r;
	else date_fin = act_date_fin_p;

	act_description = $('#act_description').val();


	$.ajax({
		url: '../../../Loxense-Actions.php?Action=AJAX_Modifier',
		type: 'POST',
		dataType: 'json',
		data: $.param({
			'idn_id': idn_id,
			'acteur': acteur,
			'act_id': act_id,
			'act_libelle': act_libelle,
			'act_priorite': act_priorite,
			'act_frequence_code': act_frequence_code,
			'act_frequence_libelle': act_frequence_libelle,
			'act_statut_code': act_statut_code,
			'act_statut_libelle': act_statut_libelle,
			'mcr_id': mcr_id,
//			'mcr_libelle': mcr_libelle,
			'act_date_debut_p': act_date_debut_p,
			'act_date_fin_p': act_date_fin_p,
			'act_date_debut_r': act_date_debut_r,
			'act_date_fin_r': act_date_fin_r,
			'act_description': act_description
		 }),

		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				$('#idModalModifier').modal('hide');

				$('#ACT_'+Id).find('[data-src="mcr_libelle"] span').text( mcr_libelle );
				$('#ACT_'+Id).find('[data-src="spp_nom"] span').text( spp_nom );
				$('#ACT_'+Id).find('[data-src="act_libelle"] span').text( act_libelle );
				$('#ACT_'+Id).find('[data-src="acteur"] span').text( acteur );
				$('#ACT_'+Id).find('[data-src="act_priorite"] span').text( act_priorite );
				$('#ACT_'+Id).find('[data-src="date_debut"] span').text( date_debut );
				$('#ACT_'+Id).find('[data-src="date_fin"] span').text( date_fin );
				$('#ACT_'+Id).find('[data-src="act_statut_libelle"] span').text( act_statut_libelle );

				afficherMessage( reponse['texteMsg'], reponse['statut'], 'body' );
			}
		}
	});
}


function sauverZoneAjoutAction( L_Fichier_Non_Autorise ) {
	var Libelle = $('#prv_libelle').val();
	if ( Libelle == '' ) {
		$('#prv_libelle').addClass('is-invalid').focus();
		return;
	}

	var act_id = $('#act_id').val();

    var NomFichier = $('#fichier_interne').val();
	if ( NomFichier == '' ) {
		$('#fichier_interne').addClass('is-invalid').focus();
		return;
	}

    var DecoupageNom = NomFichier.split('.');
    var Element = DecoupageNom.length;

    if ( Element > 1 ) {
        if ( controlerSiExtentionFichierBureautique( DecoupageNom[ Element - 1 ] ) == false ) {
            afficherMessage( L_Fichier_Non_Autorise + '<strong>' + afficherExtentionsFichierBureautique() + '</strong>', 'error', '#idModalModifier' );
            return;
        }
    }

	var formData = new FormData($("#formCharger")[0]);

	$.ajax({
		url: '../../../Loxense-Actions.php?Action=AJAX_Charger_Preuve&Libelle=' + Libelle + '&act_id=' + act_id,
		type: 'POST',
		dataType: 'json',
		mimeType: "multipart/form-data",
		data: formData, //$.param({'fichier': $('#formModifier input[name="fichier"]').val()}),
		cache: false,
		contentType: false,
		processData: false,
		success: function( reponse ) {
			var MyClass;

			//afficherMessage( reponse['texteMsg'], reponse['statut'], '#formCharger' ); //zone-charger_preuves' );
			if ( reponse['statut'] == 'success' ) {
				MyClass = 'vert_normal';
			} else {
				MyClass = 'orange_normal';
			}

			var Message = '<b class="' + MyClass + '">' + reponse['texteMsg'] + '</b>';
			var crs_id = reponse['crs_id'];
			var prv_id = reponse['prv_id'];

			$('#statut_tranfert').html( Message );

			$("#fichier_interne").val('');
			$('#prv_libelle').val('');

			$('#liste-preuves').prepend(
				'<div class="row liste" id="PRV_'+ reponse['prv_id'] + '">' +
				'<div class="col-lg-5 prv_libelle">' +
				Libelle +
				'</div>' +
				'<div class="col-lg-5 prv_localisation" data-rep="' + reponse['URL_PREUVES'] + '">' +
				reponse['prv_localisation'].split('___')[1] +
				'</div>' +
				'<div class="col-lg-2">' +
				'<a class="btn btn-outline-secondary btn-sm btn-v-preuve" title="'+reponse['L_Preuve_Visualiser']+'" data-act_id="'+act_id+'" data-crs_id="'+crs_id+'" data-prv_id="'+prv_id+'"><i class="bi-eye-fill"></i></a>&nbsp;' +
				'<a class="btn btn-outline-secondary btn-sm btn-t-preuve" title="'+reponse['L_Preuve_Telecharger']+'" data-act_id="'+act_id+'" data-crs_id="'+crs_id+'" data-prv_id="'+prv_id+'"><i class="bi-download"></i></a>&nbsp;' +
				'<a class="btn btn-outline-secondary btn-sm btn-s-preuve" title="'+reponse['L_Preuve_Supprimer']+'" data-act_id="'+act_id+'" data-crs_id="'+crs_id+'" data-prv_id="'+prv_id+'"><i class="bi-x-circle"></i></a>' +
				'</div>' +
				'</div>'
				);

			$('#idModalModifier').trigger( 'boutons-preuves' );
		}
	});
}


function telechargerFichier( NomFichier ) {
/*	$.ajax({
		url: '../../../Loxense-Actions.php?Action=AJAX_Telecharger_Fichier',
		type: 'POST',
		dataType: 'json',
		data: $.param({'nom_fichier': NomFichier}),
		success: function( reponse ) {
				afficherMessage( reponse['texteMsg'], reponse['statut'], '#idModalModifier' );
		}
	});
*/
	window.location.href = '../../../Loxense-Actions.php?Action=AJAX_Telecharger_Fichier&Nom_Fichier='+NomFichier;
}


function supprimerPreuve( crs_id, prv_id, act_id, prv_localisation ) {
    $.ajax({
        url: '../../../Loxense-Actions.php?Action=AJAX_Supprimer_Preuve',
        type: 'POST',
        dataType: 'json',
        data: $.param({
            'crs_id': crs_id,
            'prv_id': prv_id,
            'act_id': act_id,
            'prv_localisation': prv_localisation
            }),

        success: function( reponse ) {
            if ( reponse[ 'statut'] == 'success' ) {
                afficherMessage( reponse['texteMsg'], reponse['statut'], '#idModalModifier' );

                $('#PRV_'+prv_id).remove();

                $('#idBoutonAjouter').removeAttr('disabled');
                $('#zone-charger_preuves').remove();
            }
        }
    });
}