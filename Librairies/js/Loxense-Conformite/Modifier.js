function ModalModifier( cnf_id ){
    var libelle_referentiel = protegerQuotes( $('#CNF_'+cnf_id+' div[data-src="libelle_referentiel"] span').text() );
    var libelle_code = protegerQuotes( $('#CNF_'+cnf_id+' div[data-src="libelle_code"] span').text() );

    $.ajax({
        url: '../../../Loxense-Conformite.php?Action=AJAX_Libeller',
        type: 'POST',
        dataType: 'json',
        data: $.param({'Afficher_Mesure': cnf_id}),
        success: function( reponse ) {
        	var Corps =
                '<div class="row">' +
                 '<label class="col-lg-2 col-form-label" for="info_libelle_referentiel">' + reponse[ 'L_Referentiel' ] + '</label>' +
                 '<div class="col-lg-9">' +
                  '<textarea id="info_libelle_referentiel" class="form-control" disabled>' + libelle_referentiel + '</textarea>' +
                 '</div>' +
                '</div>' +
                '<div class="row">' +
                 '<label class="col-lg-2 col-form-label" for="info_libelle_code">' + reponse[ 'L_Mesure' ] + '</label>' +
                 '<div class="col-lg-9">' +
                  '<textarea id="info_libelle_code" class="form-control" disabled>' + libelle_code + '</textarea>' +
                 '</div>' +
                '</div>' +
                '<div class="row">' +
                 '<label class="col-lg-2 col-form-label" for="info_libelle_code">' + reponse[ 'L_Description_MOE' ] + '</label>' +
                 '<div class="col-lg-9">' +
                  '<textarea id="cnf_description" class="form-control">' + reponse[ 'Description_Mesure' ] + '</textarea>' +
                 '</div>' +
                '</div>' +
                '<div class="row">' +
                 '<label class="col-lg-2 col-form-label" for="info_libelle_code">' + reponse[ 'L_Statut' ] + '</label>' +
                 '<div class="col-lg-3">' +
                  '<select id="cnf_etat_code" class="form-select">' + reponse[ 'Liste_Statuts_Mesure' ] + '</select>' +
                 '</div>' +
                '</div>' +

                '<ul class="nav nav-tabs">' +
                 '<li><a class="nav-link active" id="onglet-actions" href="#">' + reponse[ 'L_Actions'] + '</a></li>' +
                 '<li><button class="btn btn-sm btn-outline-secondary btn-ajouter-amc" type="button" title="' + reponse['L_Ajouter'] + '"><i class="bi-plus"></i></button></li>' +
                '</ul>' +

                //'<div id="page-maj-action" class="corps_onglet" style="display: none;">' +
                '<div id="page-maj-action" style="display: none;">' +
                '<div class="row">' +
                '<label class="col-form-label col-lg-2" for="amc_libelle">' + reponse['L_Libelle'] + '</label>' +
                '<div class="col-lg-9">' +
                '<input type="text" id="amc_libelle" class="form-control">' +
                '</div>' +
                '</div>' + // .row
                '<div class="row">' +
                '<label class="col-form-label col-lg-2" for="amc_description">' + reponse['L_Description'] + '</label>' +
                '<div class="col-lg-9">' +
                '<textarea id="amc_description" class="form-control"></textarea>' +
                '</div>' +
                '</div>' + // .row
                '<div class="row">' +
                '<label class="col-form-label col-lg-2" for="idn_id">' + reponse['L_Acteur'] + '</label>' +
                '<div class="col-lg-5">' +
                '<select id="idn_id" class="form-select">' +
                '</select>' +
                '</div>' +
                '<label class="col-form-label col-lg-1" for="amc_priorite">' + reponse['L_Priorite'] + '</label>' +
                '<div class="col-lg-1">' +
                '<input type="number" id="amc_priorite" class="form-control">' +
                '</div>' +
                '</div>' + // .row
                '<div class="row">' +
                '<label class="col-form-label col-lg-2" for="amc_date_debut_p">' + reponse['L_Date_Debut_P'] + '</label>' +
                '<div class="col-lg-2">' +
                '<input type="date" id="amc_date_debut_p" class="form-control">' +
                '</div>' +
                '<label class="col-form-label col-lg-3" for="amc_date_fin_p">' + reponse['L_Date_Fin_P'] + '</label>' +
                '<div class="col-lg-2">' +
                '<input type="date" id="amc_date_fin_p" class="form-control">' +
                '</div>' +
                '</div>' + // .row
                '<div class="row">' +
                '<label class="col-form-label col-lg-2" for="amc_date_debut_r">' + reponse['L_Date_Debut_R'] + '</label>' +
                '<div class="col-lg-2">' +
                '<input type="date" id="amc_date_debut_r" class="form-control">' +
                '</div>' +
                '<label class="col-form-label col-lg-3" for="amc_date_fin_r">' + reponse['L_Date_Fin_R'] + '</label>' +
                '<div class="col-lg-2">' +
                '<input type="date" id="amc_date_fin_r" class="form-control">' +
                '</div>' +
                '</div>' + // .row
                '<div class="row">' +
                '<label class="col-form-label col-lg-2" for="amc_statut_code">' + reponse['L_Statut'] + '</label>' +
                '<div class="col-lg-3">' +
                '<select id="amc_statut_code" class="form-select">' +
                '<option>Aucun</option>' + 
                '</select>' +
                '</div>' +
                '<label class="col-form-label col-lg-2" for="amc_frequence_code">' + reponse['L_Frequence'] + '</label>' +
                '<div class="col-lg-3">' +
                '<select id="amc_frequence_code" class="form-select">' +
                '<option>Aucune</option>' + 
                '</select>' +
                '</div>' +
                '</div>' + // .row
                '<div class="row">' +
                '<div class="col-lg-2">&nbsp;</div>' +
                '<div class="col-lg-4">' +
                '<button id="idBoutonFermerAction" class="btn btn-outline-secondary btn-automatique">' + reponse['L_Fermer'] + '</button>&nbsp;' +
                '<button id="idBoutonCreerAction" class="btn btn-primary btn-automatique" data-execution="creer">' + reponse['L_Creer'] + '</button>' +
                '</div>' +
                '</div>' + // .row
                '</div>' + // #page-maj-action

                
                // ===================================================================
                '<div id="page-actions-1" class="row titre" style="display: none;">' +
                '<div class="col-lg-3">' + reponse['L_Libelle'] + '</div>' +
                '<div class="col-lg-2">' + reponse['L_Acteur'] + '</div>' +
                '<div class="col-lg-2">' + reponse['L_Date_Debut'] + '</div>' +
                '<div class="col-lg-2">' + reponse['L_Date_Fin'] + '</div>' +
                '<div class="col-lg-2">' + reponse['L_Statut'] + '</div>' +
                '<div class="col-lg-1">' + reponse['L_Action'] + '</div>' +
                '</div>' + // .row
        		'<div id="page-actions-2" class="corps_onglet" style="max-height: 100px; display: none;">';

        	for( occurrence in reponse['Liste_Actions'] ) {
            	amc_date_debut = (reponse['Liste_Actions'][occurrence].amc_date_debut_r == null) ? reponse['Liste_Actions'][occurrence].amc_date_debut_p : reponse['Liste_Actions'][occurrence].amc_date_debut_r; 
            	amc_date_fin = (reponse['Liste_Actions'][occurrence].amc_date_fin_r == null) ? reponse['Liste_Actions'][occurrence].amc_date_fin_p : reponse['Liste_Actions'][occurrence].amc_date_fin_r;

            	if ( reponse['Liste_Actions'][occurrence].idn_id == null ) reponse['Liste_Actions'][occurrence].idn_id = '';
            	if ( reponse['Liste_Actions'][occurrence].cvl_prenom == null ) reponse['Liste_Actions'][occurrence].cvl_prenom = '';
            	if ( reponse['Liste_Actions'][occurrence].cvl_nom == null ) reponse['Liste_Actions'][occurrence].cvl_nom = '';

            	idn_nom = reponse['Liste_Actions'][occurrence].cvl_nom + ' ' + reponse['Liste_Actions'][occurrence].cvl_prenom; 

            	Corps += '<div class="row liste" id="AMC_' + reponse['Liste_Actions'][occurrence].amc_id + '">' +
                	'<div class="col-lg-3 amc_libelle">' + reponse['Liste_Actions'][occurrence].amc_libelle + '</div>' +
                	'<div class="col-lg-2 idn_nom">' + idn_nom + '</div>' +
                	'<div class="col-lg-2 amc_date_debut">' + amc_date_debut + '</div>' +
                	'<div class="col-lg-2 amc_date_fin">' + amc_date_fin + '</div>' +
                	'<div class="col-lg-2 amc_statut_code">' + reponse['Liste_Actions'][occurrence].amc_statut_code + '</div>' +
                	'<div class="col-lg-1">' +
                	'<button class="btn btn-outline-secondary btn-sm btn-modifier-amc" data-id="' + reponse['Liste_Actions'][occurrence].amc_id + '" title="' + reponse['L_Modifier'] + '" type="button"><i class="bi-pencil-fill"></i></button>' +
                	'&nbsp;<button class="btn btn-outline-secondary btn-sm btn-supprimer-amc" data-id="' + reponse['Liste_Actions'][occurrence].amc_id + '" title="' + reponse['L_Supprimer'] + '" type="button"><i class="bi-x-circle"></i></button>' +
                	'</div>' +
                	'</div>'; // .row
            }
            Corps += '</div>'; // #page-actions

            construireModal( 'idModal',
                reponse[ 'Titre_Modifier' ],
                Corps,
                'idBoutonPrincipal', reponse[ 'L_Modifier' ],
                true, reponse[ 'L_Fermer' ],
                'idForm', 'modal-xl' );

            $('#idModal').modal('show'); // Affiche la modale qui vient d'être créée

            // Attend que la modale soit affichée avant de donner le focus au champ.
            $('#idModal').on('shown.bs.modal', function() {
                $('#cnf_description').focus();

                $('.nav-tabs a:first').trigger('click');

                $('#rechercher_actions').on( 'keydown', function( event ) {
                    if ( event.which == 13 ) {
                        event.preventDefault();
                    }
                });

                $('#rechercher_actions').on( 'keyup', function( event ) {
                    var tag_recherche = $('#rechercher_actions').val().toUpperCase();

                    $('div.checkbox label').each( function( index, element ) {
                        if ( $(element).text().toUpperCase().search( tag_recherche ) != -1 
                         || $(element).parent().attr('title').toUpperCase().search( tag_recherche ) != -1 ) $(element).parent().show();
                        else $(element).parent().hide();
                    });
                });

                $('#ajouter_tag').on( 'click', function( event ) {
                    ajouterTag();
                });

                $('.btn-ajouter-amc').on( 'click', function( event ) {
                    event.preventDefault(); // Laisse le contrôle au Javascript.

		        	$('#amc_libelle').val('');
		        	$('#idn_id').val('');
		        	$('#amc_description').val('');
		        	$('#amc_date_debut_p').val('');
		        	$('#amc_date_fin_p').val('');
		        	$('#amc_date_debut_r').val('');
		        	$('#amc_date_fin_r').val('');
		        	$('#amc_statut_code').val('');
		        	$('#amc_frequence_code').val('');
		        	$('#amc_priorite').val('');

		        	ajouterAction( cnf_id );
                });

                $('.btn-modifier-amc').on( 'click', function( event ) {
                    event.preventDefault(); // Laisse le contrôle au Javascript.

                	var amc_id = $(this).attr('data-id');

                	modifierAction( amc_id, cnf_id );
                });

                $('.btn-supprimer-amc').on( 'click', function( event ) {
                    event.preventDefault(); // Laisse le contrôle au Javascript.

                	var amc_id = $(this).attr('data-id');

                	supprimerAction( amc_id, cnf_id );
                });

                $('#idBoutonFermerAction').on( 'click', function() {
                    event.preventDefault(); // Laisse le contrôle au Javascript.

                	$('#onglet-actions').trigger( 'click' );

					$('div.modal-footer button').show();
            	});
            });

            
            // Supprime la modale après l'avoir caché.
            $('#idModal').on('hidden.bs.modal', function() {
                $('#idModal').remove();

                $('#rechercher_actions').off( 'keydown' );
            });


            $('#idForm').submit( function( event ) { // Gère la soumission du formulaire.
                event.preventDefault(); // Laisse le contrôle au Javascript.
            });

            
            $('#idBoutonPrincipal').on('click', function() {
                event.preventDefault(); // Laisse le contrôle au Javascript.

            	if ( $('#page-maj-action').attr('style') == '' ) {
            		afficherMessage( reponse['L_Quitter_MAJ_Action'], 'error', '#page-maj-action', 0, 'n' );
            	}

                $.ajax({
                    url: '../../../Loxense-Conformite.php?Action=AJAX_Modifier',
                    type: 'POST',
                    dataType: 'json',
                    data: $.param({'cnf_id': cnf_id, 'cnf_etat_code': $('#cnf_etat_code').val(),
                    	'cnf_description': $('#cnf_description').val()}),
                    success: function( reponse ) {
                    	if ( reponse['statut'] == 'success' ) {
                    		$('#CNF_'+cnf_id+' div[data-src="libelle_etat"]').text( $('#cnf_etat_code :selected').text() );

                    		$('#idModal').modal('hide');
                        	
                        	afficherMessage( reponse['TexteMsg'], reponse['Statut'], '#idForm', 0, 'n' );
                    	} else {
                        	afficherMessage( reponse['TexteMsg'], reponse['Statut'], 'body' );
                    	}
                    }
                });
        	
            	
            	//$('#idModal').modal('hide');
            });

            
            $('#onglet-actions').on('click', function() {
                $('.nav-tabs li').removeClass('active');
                $('.nav-tabs li a#onglet-actions').parent().addClass('active');
                
                $('div[id^=page-]').hide();

                $('div[id^=page-actions]').show();
                $('div#page-actions-2 input:first').focus();
            });
        }
    });
}


function ModalAssocierTags( Id ) {
    var spp_code = $('#SPP_'+Id).find('div[data-src="spp_code"]').find('span').text();
    var spp_nom = $('#SPP_'+Id).find('div[data-src="spp_nom"]').find('span').text();
    var spp_type_code = $('#SPP_'+Id).find('div[data-src="libelle_type"]').find('span').text();

    $.ajax({
        url: '../../../Loxense-Conformite.php?Action=AJAX_Libeller',
        type: 'POST',
        dataType: 'json',
        data: $.param({'Associer_SPP': Id}),
        success: function( reponse ) {
            var Corps =
                '<div class="well">' +
                '<div class="row">' +
                '<span class="col-lg-2">' + reponse[ 'L_Code' ] + '</span>' +
                '<span class="col-lg-8">' + reponse[ 'L_Nom' ] + '</span>' +
                '<span class="col-lg-2">' + reponse[ 'L_Type' ] + '</span>' +
                '</div>' +
                '<div class="row">' +
                '<span class="col-lg-2" style="background-color: #dcafdd"><strong>' + spp_code + '</strong></span>' +
                '<span class="col-lg-8" style="background-color: #dcafdd"><strong>' + spp_nom + '</strong></span>' +
                '<span class="col-lg-2" style="background-color: #dcafdd"><strong>' + spp_type_code + '</strong></span>' +
                '</div>' +
                '</div>' +
                '<div id="corps_onglets" class="corps_onglet">' +
                '<div id="onglet-tags" class="corps_onglet">' +
                reponse['Rechercher_Tags'] +
                reponse['Liste_Tags'] +
                '</div>' + // Fin #onglet-tags
                '</div>'; // Fin #corps_onglet

            var ID_Btn_Modifier = '';
            if ( reponse['crs_modifiable'] == true ) ID_Btn_Modifier = 'idBoutonPrincipal';

            construireModal( 'idModal',
                reponse[ 'Titre_Associer_Tags' ],
                Corps,
                ID_Btn_Modifier, reponse[ 'L_Modifier' ],
                true, reponse[ 'L_Fermer' ],
                'idForm', 'modal-lg' );


            $('#idModal').modal('show'); // Affiche la modale qui vient d'être créée

            // Attend que la modale soit affichée avant de donner le focus au champ.
            $('#idModal').on('shown.bs.modal', function() {
                $('select:first').focus();

                $('#rechercher_tags').on( 'keydown', function( event ) {
                    if ( event.which == 13 ) {
                        event.preventDefault();
                    }
                });

                $('#rechercher_tags').on( 'keyup', function( event ) {
                    var tag_recherche = $('#rechercher_tags').val().toUpperCase();

                    $('div.checkbox label').each( function( index, element ) {
                        if ( $(element).text().toUpperCase().search( tag_recherche ) != -1 
                         || $(element).parent().attr('title').toUpperCase().search( tag_recherche ) != -1 ) $(element).parent().show();
                        else $(element).parent().hide();
                    });
                });

                $('#ajouter_tag').on( 'click', function( event ) {
                    ajouterTag();
                });
            });

            // Supprime la modale après l'avoir caché.
            $('#idModal').on('hidden.bs.modal', function() {
                $('#idModal').remove();

                $('#rechercher_tags').off( 'keydown' );
            });

            
            $('#idForm').submit( function( event ) { // Gère la soumission du formulaire.
                event.preventDefault(); // Laisse le contrôle au Javascript.

                modifierAssociationTags( Id );
            } );
        }
    });
}


function modifierAssociationTags( Id, Message ) {
    // Gestion des Etiquettes.
    var Message = Message || 'oui';
    var tgs_id;
    var ListeAjouterTGS = Array();
    var ListeSupprimerTGS = Array();
    var TotalTGS = 0;

    $('input[id^=chk-TGS-]').each( function( index, element ) {
        vln_id = $(this).attr('id').split('-')[2];

        // Cette Etiquette est à ajouter.
        if ( $(this).attr('data-old') == 0 && $(this).is(':checked') === true ) {
            ListeAjouterTGS.push( vln_id );
            TotalTGS += 1;
        }


        // Cette Etiquette est à supprimer.
        if ( $(this).attr('data-old') == 1 && $(this).is(':checked') === false ) {
            ListeSupprimerTGS.push( vln_id );
        }


        // Comptabilise cet Actif Primordial pour le total.
        if ( $(this).attr('data-old') == 1 && $(this).is(':checked') === true ) {
            TotalTGS += 1;
        }
    });

    $.ajax({
        url: '../../../Loxense-Conformite.php?Action=AJAX_Associer_Actifs_Supports_TGS',
        type: 'POST',
        data: $.param({'spp_id': Id, 'liste_ajouter_tgs': ListeAjouterTGS, 'liste_supprimer_tgs': ListeSupprimerTGS}),
        dataType: 'json', // le résultat est transmit dans un objet JSON

        success: function(reponse) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
            var statut = reponse['statut'];
            var texteMsg = reponse['texteMsg'];

            if ( statut == 'success' ) {
                $('#idModal').modal('hide'); // Cache la modale d'ajout.

                if ( Message == 'oui' ) afficherMessage( texteMsg, statut, 'body' );

                $('#SPP_'+Id).find('button.total_tags_associes').text( TotalTGS );
            } else {
                afficherMessage( texteMsg, statut, '#idModal', 0, 'n' );
            }
        }
    });
}


// ==================


function ajouterAction( cnf_id ) {
    $('div[id^=page-]').hide();

	$('div.modal-footer button').hide();

    $('div[id^=page-maj-action]').show();
    $('#amc_libelle').focus();

    $('#amc_priorite').val(0);

    $.ajax({
        url: '../../../Loxense-Conformite.php?Action=AJAX_Libeller',
        type: 'POST',
        data: $.param({'Preparer_Creation_Action': cnf_id}),
        dataType: 'json', // le résultat est transmit dans un objet JSON

        success: function(reponse) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
            var statut = reponse['statut'];
            var texteMsg = reponse['texteMsg'];

            if ( statut == 'success' ) {
            	$('#idBoutonCreerAction').text( reponse['L_Creer'] ).attr('data-execution', 'creer');
            	
            	Code_HTML = '';
            	
            	for( occurrence in reponse['Liste_Utilisateurs'] ) {
            		Code_HTML += '<option value="' + reponse['Liste_Utilisateurs'][occurrence].idn_id + '"' +
            			'>' + reponse['Liste_Utilisateurs'][occurrence].cvl_nom + ' ' +
            			reponse['Liste_Utilisateurs'][occurrence].cvl_prenom +'</option>'; 
            	}
            	Code_HTML = '<option value="">Aucun</option>' + Code_HTML; 

            	$('#idn_id').html( Code_HTML );

            	Code_HTML = '';

            	for( occurrence in reponse['Liste_Statuts_Action'] ) {
            		Code_HTML += '<option value="' + reponse['Liste_Statuts_Action'][occurrence].lbr_code + '">' +
            			reponse['Liste_Statuts_Action'][occurrence].lbr_libelle + '</option>'; 
            	}
            	
            	$('#amc_statut_code').html( Code_HTML );

            	Code_HTML = '';

            	for( occurrence in reponse['Liste_Frequences_Action'] ) {
            		Code_HTML += '<option value="' + reponse['Liste_Frequences_Action'][occurrence].lbr_code + '">' +
            			reponse['Liste_Frequences_Action'][occurrence].lbr_libelle + '</option>'; 
            	}
            	
            	$('#amc_frequence_code').html( Code_HTML );

            	$('#idBoutonCreerAction').off( 'click' );
            	$('#idBoutonCreerAction').on( 'click', function() {
            		var amc_libelle = $('#amc_libelle').val();
            		var erreur = 0;
            		
            		if ( amc_libelle == '' ) {alert("lib");
            			$('#amc_libelle').css('border-color', 'red').focus();

            			erreur = 1;
            		}

            		if ( $('#amc_date_debut_p').val() == '' ) {
            			$('#amc_date_debut_p').css('border-color', 'red');

            			if ( erreur == 0 ) {
                			$('#amc_date_debut_p').focus();            				
            			}

            			erreur = 1;
            		}
            		
            		if ( $('#amc_date_fin_p').val() == '' ) {
            			$('#amc_date_fin_p').css('border-color', 'red');

            			if ( erreur == 0 ) {
                			$('#amc_date_fin_p').focus();            				
            			}

            			erreur = 1;
                	}

            		if ( erreur == 0 ) {
            		    $.ajax({
            		        url: '../../../Loxense-Conformite.php?Action=AJAX_Creer_Action',
            		        type: 'POST',
            		        data: $.param({
            		        	'amc_libelle': amc_libelle,
            		        	'idn_id': $('#idn_id').val(),
            		        	'cnf_id': cnf_id,
            		        	'amc_description': $('#amc_description').val(),
            		        	'amc_date_debut_p': $('#amc_date_debut_p').val(),
            		        	'amc_date_fin_p': $('#amc_date_fin_p').val(),
            		        	'amc_date_debut_r': $('#amc_date_debut_r').val(),
            		        	'amc_date_fin_r': $('#amc_date_fin_r').val(),
            		        	'amc_statut_code': $('#amc_statut_code').val(),
            		        	'amc_frequence_code': $('#amc_frequence_code').val(),
            		        	'amc_priorite': $('#amc_priorite').val()
            		        	}),
            		        dataType: 'json', // le résultat est transmit dans un objet JSON

            		        success: function(reponse) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
            		            var statut = reponse['statut'];
            		            var texteMsg = reponse['texteMsg'];

            		            if ( statut == 'success' ) {
                		        	amc_date_debut = ($('#amc_date_debut_r').val() == '') ? $('#amc_date_debut_p').val() : $('#amc_date_debut_r').val(); 
                		        	amc_date_fin = ($('#amc_date_fin_r').val() == '') ? $('#amc_date_fin_p').val() : $('#amc_date_fin_r').val(); 

                		        	Code_HTML = '<div id="AMC_'+reponse['amc_id']+'" class="row liste">' +
                		        		'<div class="col-lg-3 amc_libelle">' + amc_libelle + '</div>' +
                		        		'<div class="col-lg-2 idn_nom">' + $('#idn_id :selected').text() + '</div>' +
                		        		'<div class="col-lg-2 amc_date_debut">' + amc_date_debut + '</div>' +
                		        		'<div class="col-lg-2 amc_date_fin">' + amc_date_fin + '</div>' +
                		        		'<div class="col-lg-2 amc_statut_code">' + $('#amc_statut_code :selected').text() + '</div>' +
                		        		'<div class="col-lg-1">';

                		        	if ( reponse[ 'droit_modifier' ] == 1 ) {
                	                	Code_HTML += '<button class="btn btn-outline-secondary btn-sm btn-modifier-amc" data-id="'+reponse['amc_id']+'" title="'+reponse['L_Modifier']+'" type="button"><i class="bi-pencil-fill"></i></button>&nbsp;';
                	                }

                		        	if ( reponse[ 'droit_supprimer' ] == 1 ) {
                	                	Code_HTML += '<button class="btn btn-outline-secondary btn-sm btn-supprimer-amc" data-id="'+reponse['amc_id']+'" title="'+reponse['L_Supprimer']+'" type="button"><i class="bi-x-circle"></i></button>';
                	                }

                		        	Code_HTML += '</div>';

                		        	$('#page-actions-2').prepend( Code_HTML );

                	                if ( reponse[ 'droit_modifier' ] == 1 ) {
                	                    // Assigne l'événement "click" sur tous les boutons de Modification
                	                    $('#AMC_'+reponse['amc_id']+' .btn-modifier-amc').click( function( event ){
                	                    	modifierAction( reponse['amc_id'], cnf_id );
                	                    });
                	                }
                		        	
                	                if ( reponse[ 'droit_supprimer' ] == 1 ) {
                	                    // Assigne l'événement "click" sur tous les boutons de Modification
                	                    $('#AMC_'+reponse['amc_id']+' .btn-supprimer-amc').click( function( event ){
                	                    	supprimerAction( reponse['amc_id'], cnf_id );
                	                    });
                	                }
                		        	
            		            	$('#onglet-actions').trigger( 'click' );
            		            	
            		            	afficherMessage( texteMsg, statut, '#idModal', 5, 'o' );
            		            } else {
            		                afficherMessage( texteMsg, statut, '#idModal', 0, 'n' );
            		            }

								$('div.modal-footer button').show();
            		        }
            		    });
            		}
            	});
            } else {
                afficherMessage( texteMsg, statut, '#idModal', 0, 'n' );
            }
        }
    });
}
	

function modifierAction( amc_id, cnf_id ) {
    $('div[id^=page-]').hide();

	$('div.modal-footer button').hide();

    $('div[id^=page-maj-action]').show();
    $('#amc_libelle').focus();
    
    $.ajax({
        url: '../../../Loxense-Conformite.php?Action=AJAX_Libeller',
        type: 'POST',
        data: $.param({'Preparer_Creation_Action': cnf_id, 'Preparer_Modification_Action': amc_id}),
        dataType: 'json', // le résultat est transmit dans un objet JSON

        success: function(reponse) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
            var statut = reponse['statut'];
            var texteMsg = reponse['texteMsg'];

            if ( statut == 'success' ) {
            	$('#idBoutonCreerAction').text( reponse['L_Modifier'] ).attr('data-execution', 'modifier');
            	
            	$('#amc_libelle').val( reponse['Infos_Action'].amc_libelle );
            	$('#amc_description').val( reponse['Infos_Action'].amc_description );
            	$('#amc_priorite').val( reponse['Infos_Action'].amc_priorite );
            	$('#amc_date_debut_p').val( reponse['Infos_Action'].amc_date_debut_p );
            	$('#amc_date_fin_p').val( reponse['Infos_Action'].amc_date_fin_p );
            	$('#amc_date_debut_r').val( reponse['Infos_Action'].amc_date_debut_r );
            	$('#amc_date_fin_r').val( reponse['Infos_Action'].amc_date_fin_r );


            	Code_HTML = '';
            	
            	for( occurrence in reponse['Liste_Utilisateurs'] ) {
                	if ( reponse['Infos_Action'].idn_id == reponse['Liste_Utilisateurs'][occurrence].idn_id ) {
                		Selected = ' selected';
                	} else {
                		Selected = '';
                	}

            		Code_HTML += '<option value="' + reponse['Liste_Utilisateurs'][occurrence].idn_id + '"' +
            			Selected + '>' + reponse['Liste_Utilisateurs'][occurrence].cvl_nom + ' ' +
            			reponse['Liste_Utilisateurs'][occurrence].cvl_prenom +'</option>'; 
            	}
            	Code_HTML = '<option value="">Aucun</option>' + Code_HTML; 

            	$('#idn_id').html( Code_HTML );

            	
            	Code_HTML = '';
            	
            	for( occurrence in reponse['Liste_Statuts_Action'] ) {
                	if ( reponse['Infos_Action'].amc_statut_code == reponse['Liste_Statuts_Action'][occurrence].lbr_code ) {
                		Selected = ' selected';
                	} else {
                		Selected = '';
                	}

            		Code_HTML += '<option value="' + reponse['Liste_Statuts_Action'][occurrence].lbr_code + '"' +
            			Selected + '>' + reponse['Liste_Statuts_Action'][occurrence].lbr_libelle + '</option>'; 
            	}
            	
            	$('#amc_statut_code').html( Code_HTML );

            	
            	Code_HTML = '';

            	for( occurrence in reponse['Liste_Frequences_Action'] ) {
                	if ( reponse['Infos_Action'].amc_frequence_code == reponse['Liste_Frequences_Action'][occurrence].lbr_code ) {
                		Selected = ' selected';
                	} else {
                		Selected = '';
                	}

            		Code_HTML += '<option value="' + reponse['Liste_Frequences_Action'][occurrence].lbr_code +
            			'"' + Selected + '>' + reponse['Liste_Frequences_Action'][occurrence].lbr_libelle +
            			'</option>'; 
            	}
            	
            	$('#amc_frequence_code').html( Code_HTML );

            	$('#idBoutonCreerAction').off( 'click' );
            	$('#idBoutonCreerAction').on( 'click', function() {
            		var amc_libelle = $('#amc_libelle').val();
            		
            		if ( amc_libelle == '' ) {
            			$('#amc_libelle').css('border-color', 'red').focus();
            		} else {
            			$.ajax({
            		        url: '../../../Loxense-Conformite.php?Action=AJAX_Modifier_Action',
            		        type: 'POST',
            		        data: $.param({
            		        	'amc_id': amc_id,
            		        	'amc_libelle': amc_libelle,
            		        	'idn_id': $('#idn_id').val(),
            		        	'cnf_id': cnf_id,
            		        	'amc_description': $('#amc_description').val(),
            		        	'amc_date_debut_p': $('#amc_date_debut_p').val(),
            		        	'amc_date_fin_p': $('#amc_date_fin_p').val(),
            		        	'amc_date_debut_r': $('#amc_date_debut_r').val(),
            		        	'amc_date_fin_r': $('#amc_date_fin_r').val(),
            		        	'amc_statut_code': $('#amc_statut_code').val(),
            		        	'amc_frequence_code': $('#amc_frequence_code').val(),
            		        	'amc_priorite': $('#amc_priorite').val()
            		        	}),
            		        dataType: 'json', // le résultat est transmit dans un objet JSON

            		        success: function(reponse) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
            		            var statut = reponse['statut'];
            		            var texteMsg = reponse['texteMsg'];

            		            if ( statut == 'success' ) {
                		        	amc_date_debut = ($('#amc_date_debut_r').val() == '') ? $('#amc_date_debut_p').val() : $('#amc_date_debut_r').val(); 
                		        	amc_date_fin = ($('#amc_date_fin_r').val() == '') ? $('#amc_date_fin_p').val() : $('#amc_date_fin_r').val(); 

                		        	$('#AMC_'+amc_id+' div.amc_libelle').html( amc_libelle );
            		            	$('#AMC_'+amc_id+' div.amc_date_debut').html( amc_date_debut );
            		            	$('#AMC_'+amc_id+' div.amc_date_fin').html( amc_date_fin );
            		            	$('#AMC_'+amc_id+' div.amc_statut_code').html( $('#amc_statut_code :selected').text() );

            		            	$('#onglet-actions').trigger( 'click' );
            		            }

								$('div.modal-footer button').show();

            		            afficherMessage( texteMsg, statut, 'body', 5, 'n' );
            		        }
            		    });
            			
            		}
            	});
            } else {
                afficherMessage( texteMsg, statut, '#idModal', 0, 'n' );
            }
        }
    });
}


function supprimerAction( amc_id, cnf_id ) {
    $('div[id^=page-]').hide();

	$('div.modal-footer button').hide();

    $('div[id^=page-maj-action]').show();
    $('#amc_libelle').focus();
    
    $.ajax({
        url: '../../../Loxense-Conformite.php?Action=AJAX_Libeller',
        type: 'POST',
        data: $.param({'Preparer_Creation_Action': cnf_id, 'Preparer_Modification_Action': amc_id}),
        dataType: 'json', // le résultat est transmit dans un objet JSON

        success: function(reponse) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
            var statut = reponse['statut'];
            var texteMsg = reponse['texteMsg'];

            if ( statut == 'success' ) {
            	$('#idBoutonCreerAction').text( reponse['L_Supprimer'] ).attr('data-execution', 'supprimer');
            	
            	$('#amc_libelle').val( reponse['Infos_Action'].amc_libelle ).attr('disabled', 'disabled');
            	$('#amc_description').val( reponse['Infos_Action'].amc_description ).attr('disabled', 'disabled');
            	$('#amc_priorite').val( reponse['Infos_Action'].amc_priorite ).attr('disabled', 'disabled');
            	$('#amc_date_debut_p').val( reponse['Infos_Action'].amc_date_debut_p ).attr('disabled', 'disabled');
            	$('#amc_date_fin_p').val( reponse['Infos_Action'].amc_date_fin_p ).attr('disabled', 'disabled');
            	$('#amc_date_debut_r').val( reponse['Infos_Action'].amc_date_debut_r ).attr('disabled', 'disabled');
            	$('#amc_date_fin_r').val( reponse['Infos_Action'].amc_date_fin_r ).attr('disabled', 'disabled');


            	Code_HTML = '';
            	
            	for( occurrence in reponse['Liste_Utilisateurs'] ) {
                	if ( reponse['Infos_Action'].idn_id == reponse['Liste_Utilisateurs'][occurrence].idn_id ) {
                		Selected = ' selected';
                	} else {
                		Selected = '';
                	}

            		Code_HTML += '<option value="' + reponse['Liste_Utilisateurs'][occurrence].idn_id + '"' +
            			Selected + '>' + reponse['Liste_Utilisateurs'][occurrence].cvl_nom + ' ' +
            			reponse['Liste_Utilisateurs'][occurrence].cvl_prenom +'</option>'; 
            	}
            	Code_HTML = '<option value="">Aucun</option>' + Code_HTML; 

            	$('#idn_id').html( Code_HTML ).attr('disabled', 'disabled');

            	
            	Code_HTML = '';
            	
            	for( occurrence in reponse['Liste_Statuts_Action'] ) {
                	if ( reponse['Infos_Action'].amc_statut_code == reponse['Liste_Statuts_Action'][occurrence].lbr_code ) {
                		Selected = ' selected';
                	} else {
                		Selected = '';
                	}

            		Code_HTML += '<option value="' + reponse['Liste_Statuts_Action'][occurrence].lbr_code + '"' +
            			Selected + '>' + reponse['Liste_Statuts_Action'][occurrence].lbr_libelle + '</option>'; 
            	}
            	
            	$('#amc_statut_code').html( Code_HTML ).attr('disabled', 'disabled');

            	
            	Code_HTML = '';

            	for( occurrence in reponse['Liste_Frequences_Action'] ) {
                	if ( reponse['Infos_Action'].amc_frequence_code == reponse['Liste_Frequences_Action'][occurrence].lbr_code ) {
                		Selected = ' selected';
                	} else {
                		Selected = '';
                	}

            		Code_HTML += '<option value="' + reponse['Liste_Frequences_Action'][occurrence].lbr_code +
            			'"' + Selected + '>' + reponse['Liste_Frequences_Action'][occurrence].lbr_libelle +
            			'</option>'; 
            	}
            	
            	$('#amc_frequence_code').html( Code_HTML ).attr('disabled', 'disabled');

            	
            	$('#idBoutonCreerAction').off( 'click' );
            	$('#idBoutonCreerAction').on( 'click', function() {
            		var amc_libelle = $('#amc_libelle').val();
            		
        			$.ajax({
        		        url: '../../../Loxense-Conformite.php?Action=AJAX_Supprimer_Action',
        		        type: 'POST',
        		        data: $.param({
        		        	'amc_id': amc_id,
        		        	'cnf_id': cnf_id,
        		        	'amc_libelle': $('#amc_libelle').val(),
        		        	'amc_date_debut_p': $('#amc_date_debut_p').val(),
        		        	'amc_date_fin_p': $('#amc_date_fin_p').val()
        		        	}),
        		        dataType: 'json', // le résultat est transmit dans un objet JSON

        		        success: function(reponse) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
        		            var statut = reponse['statut'];
        		            var texteMsg = reponse['texteMsg'];

        		            if ( statut == 'success' ) {
        		            	$('#AMC_'+amc_id).remove();

        		            	$('#onglet-actions').trigger( 'click' );

        		            	$('#amc_libelle').removeAttr( 'disabled' );
        		            	$('#amc_description').removeAttr( 'disabled' );
        		            	$('#amc_priorite').removeAttr( 'disabled' );
        		            	$('#amc_date_debut_p').removeAttr( 'disabled' );
        		            	$('#amc_date_fin_p').removeAttr( 'disabled' );
        		            	$('#amc_date_debut_r').removeAttr( 'disabled' );
        		            	$('#amc_date_fin_r').removeAttr( 'disabled' );
        		            	$('#idn_id').removeAttr( 'disabled' );
        		            	$('#amc_statut_code').removeAttr( 'disabled' );
        		            	$('#amc_frequence_code').removeAttr( 'disabled' );
        		            }

							$('div.modal-footer button').show();

        		            afficherMessage( texteMsg, statut, 'body', 0, 'n' );
        		        }
        		    });
            	});
            } else {
                afficherMessage( texteMsg, statut, '#idModal', 0, 'n' );
            }
        }
    });
}