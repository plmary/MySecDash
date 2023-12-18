// ======================================================================
// Mise en place des écoutes sur les événements produits par les objets.
$(document).ready(function() {
    // Ajouter une Action dans la base.
    $(".btn-ajouter").on('click', function(){
        ModalAjouter();
    });
});


// ============================================
// Fonctions répondant aux événements écoutés.
function ModalAjouter(){
    $.ajax({
        url: '../../../Loxense-Actions.php?Action=AJAX_Libeller',
        type: 'POST',
        dataType: 'json',
        data: $.param({'Creer_Action':'O'}),
        success: function( reponse ) {
            var Corps =
                '<div class="row">' +
                ' <label class="col-lg-2 col-form-label">' + reponse[ 'L_Acteur' ] + '</label>' +
                ' <div class="col-lg-10">' +
                '  <input id="idn_id" class="form-control" type="text" value="' + reponse[ 'Utilisateur' ] + '" disabled>' +
                ' </div>' +
                '</div>' +

                '<div class="row">' +
                ' <label class="col-lg-2 col-form-label" for="act_libelle">' + reponse[ 'L_Libelle' ] + '</label>' +
                ' <div class="col-lg-10">' +
                '  <input id="act_libelle" class="form-control" type="text" maxlength="100" required>' +
                ' </div>' +
                '</div>' +

                '<div class="row">' +
                ' <label class="col-lg-2 col-form-label" for="act_priorite">' + reponse[ 'L_Priorite' ] + '</label>' +
                ' <div class="col-lg-2">' +
                '  <input id="act_priorite" class="form-control" type="number" value="0" required>' +
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
                ' </div>' +

                '<div class="clearfix">&nbsp;</div>' +


                 // =====================
                 // Gestion des onglets.
                 '<ul class="nav nav-tabs">' +
                  '<li><a class="nav-link" id="selectionner_mesure" href="#">' + reponse[ 'L_Mesure'] + '</a></li>' +
                  '<li><a class="nav-link" id="gerer_dates" href="#">' + reponse[ 'L_Dates'] + '</a></li>' +
                  '<li><a class="nav-link" id="gerer_description" href="#">' + reponse[ 'L_Description'] + '</a></li>' +
                 '</ul>' +
                
                 '<div id="corps_onglet" class="onglet-association">' +
                 
                  // Onglet : Sélection d'une mesure.
                  '<div id="onglet-selectionner_mesure" style="display: none;">' +
                   '<div class="row form-group">' +
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
                    '<input id="act_date_debut_p" class="form-control" type="date" maxlength="10" placeholder="' + reponse[ 'L_Format_Date']  + '" min="' + reponse[ 'JourCourant']  + '" value="' + reponse[ 'JourCourant']  + '" required>' +
                    '</div>' +
                    '</div>' +

                    '<div class="row col-lg-6">' +
                    '<label class="col-lg-5 col-form-label" for="act_date_fin_p">' + reponse[ 'L_Date_Fin_p' ] + '</label>' +
                    '<div class="col-lg-4">' +
                    '<input id="act_date_fin_p" class="form-control" type="date" maxlength="10" placeholder="' + reponse[ 'L_Format_Date']  + '" min="' + reponse[ 'JourCourant']  + '" required>' +
                    '</div>' +
                    '</div>' +
                    '</div>' + // Fin bloc une ligne

                    '<div class="row" style="padding-top: 12px">' + // Bloc une ligne
                    '<div class="row col-lg-6">' +
                    '<label class="col-lg-5 col-form-label" for="act_date_debut_r">' + reponse[ 'L_Date_Debut_r' ] + '</label>' +
                    '<div class="col-lg-4">' +
                    '<input id="act_date_debut_r" class="form-control" type="date" maxlength="10" placeholder="' + reponse[ 'L_Format_Date']  + '" min="' + reponse[ 'JourCourant']  + '">' +
                    '</div>' +
                    '</div>' +

                    '<div class="row col-lg-6">' +
                    '<label class="col-lg-5 col-form-label" for="act_date_fin_r">' + reponse[ 'L_Date_Fin_r' ] + '</label>' +
                    '<div class="col-lg-4">' +
                    '<input id="act_date_fin_r" class="form-control" type="date" maxlength="10" placeholder="' + reponse[ 'L_Format_Date']  + '" min="' + reponse[ 'JourCourant']  + '">' +
                    '</div>' +
                    '</div>' +
                    '</div>' + // Fin bloc une ligne
                  '</div>' +

                  '<div id="onglet-gerer_description" style="display: none;">' +
                    '<div class="form-horizontal">' + // Bloc horizontal
                    '<div class="form-group">' +
                    '<div class="col-lg-12">' +
                    '<textarea id="act_description" class="form-control" rows="6"></textarea>' +
                    '</div>' +
                    '</div>' +
                    '</div>' + // Fin bloc horizontal
                  '</div>' +

                 '</div>'; // corps_onglet

            construireModal( 'idModalAjouter',
                reponse[ 'Titre_Ajouter' ],
                Corps,
                'idBoutonAjouter', reponse[ 'L_Ajouter' ],
                true, reponse[ 'L_Fermer' ],
                'formAjouter', 'modal-xl' );

            $('#idModalAjouter').modal('show'); // Affiche la modale qui vient d'être créée

            // Attend que la modale soit affichée avant de donner le focus au champ.
            $('#idModalAjouter').on('shown.bs.modal', function() {
                $('.nav-tabs a:first').trigger('click');

                $('#act_libelle').focus();
                //$('.selectpicker').selectpicker('show');
            });

            // Supprime la modale après l'avoir caché.
            $('#idModalAjouter').on('hidden.bs.modal', function() {
                $('#idModalAjouter').remove();
            });


            // Affiche la liste des critères d'évaluation du risque.
            $('#selectionner_mesure').on('click', function() {
                $('.nav-link').removeClass('active');
                $('a#selectionner_mesure').addClass('active');
                
                $('div[id^=onglet-]').hide();

                $('#onglet-selectionner_mesure').show();
                $('div#onglet-selectionner_mesure input:first').focus();
            });


            // Affiche la liste des critères d'évaluation du risque.
            $('#gerer_dates').on('click', function() {
                $('.nav-link').removeClass('active');
                $('a#gerer_dates').addClass('active');
                
                $('div[id^=onglet-]').hide();

                $('#onglet-gerer_dates').show();
                $('div#onglet-gerer_dates input:first').focus();
            });


            // Affiche la liste des critères d'évaluation du risque.
            $('#gerer_description').on('click', function() {
                $('.nav-link').removeClass('active');
                $('a#gerer_description').addClass('active');
                
                $('div[id^=onglet-]').hide();

                $('#onglet-gerer_description').show();
                $('div#onglet-gerer_description textarea:first').focus();
            });


            $('#idBoutonAjouter').on('click', function() {
                validerEcran();
            });


            $('#formAjouter').submit( function( event ) { // Gère la soumission du formulaire.
                event.preventDefault(); // Laisse le contrôle au Javascript.

                if ( validerEcran() ) {
                    ajouterOccurrence();
                }
            } );
        }
    });
}


function validerEcran() {
    if ( $('input[name="mcr_id"]:checked').length == 0 ) {
        $('#selectionner_mesure').trigger('click');

        $('input[name="mcr_id"]').parent().parent().addClass('has-error');

        $('input[name="mcr_id"]').focus();

        return false;
    }


    var date_debut_p = $('#act_date_debut_p').val();

    if ( ! validerDate( date_debut_p ) || date_debut_p == '' ) {
        $('#gerer_dates').trigger('click');

        $('#act_date_debut_p').parent().addClass('has-error');

        $('#act_date_debut_p').focus();

        return false;
    }


    var date_fin_p = $('#act_date_fin_p').val();

    if ( ! validerDate( date_fin_p ) || date_fin_p == '' ) {
        $('#gerer_dates').trigger('click');

        $('#act_date_fin_p').parent().addClass('has-error');

        $('#act_date_fin_p').focus();

        return false;
    }


    if ( $('#act_date_debut_r').val() != '' ) {
        if ( ! validerDate( $('#act_date_debut_r').val() ) ) {
            $('#gerer_dates').trigger('click');

            $('#act_date_debut_r').parent().addClass('has-error');

            $('#act_date_debut_r').focus();

            return false;
        }
    }


    if ( $('#act_date_fin_r').val() != '' ) {
        if ( ! validerDate( $('#act_date_fin_r').val() ) ) {
            $('#gerer_dates').trigger('click');

            $('#act_date_fin_r').parent().addClass('has-error');

            $('#act_date_fin_r').focus();

            return false;
        }
    }


    if ( $('#act_description').val() == '' ) {
        $('#gerer_description').trigger('click');
        $('#act_description').parent().addClass('has-error');
        $('#act_description').focus();

        return false;
    }

    return true;
}

function ajouterOccurrence() {
    var mcr_id = $('input[name="mcr_id"]:checked').val();
    var act_libelle = $('#act_libelle').val();
    var act_date_debut_p = $('#act_date_debut_p').val();
    var act_date_fin_p = $('#act_date_fin_p').val();
    var act_date_debut_r = $('#act_date_debut_r').val();
    var act_date_fin_r = $('#act_date_fin_r').val();
    var act_date_fin_r = $('#act_date_fin_r').val();
    var act_description = $('#act_description').val();
    var act_priorite = $('#act_priorite').val();
    var act_statut = $('#act_statut').val();
    var act_statut_libelle = $('#act_statut option:selected').text();
    var act_frequence = $('#act_frequence').val();

    $.ajax({
        url: '../../../Loxense-Actions.php?Action=AJAX_Ajouter',
        type: 'POST',
        dataType: 'json',
        data: $.param({'mcr_id': mcr_id, 'act_libelle': act_libelle, 'act_frequence': act_frequence,
            'act_date_debut_p': act_date_debut_p, 'act_date_fin_p': act_date_fin_p,
            'act_date_debut_r': act_date_debut_r, 'act_date_fin_r': act_date_fin_r,
            'act_description': act_description, 'act_priorite': act_priorite, 'act_statut': act_statut,
            'act_statut_libelle': act_statut_libelle}),
        success: function( reponse ) {
            if ( reponse['statut'] == 'success' ) {
                var test_existance = $('#corps_tableau div[id$="-MCR_'+mcr_id+'"] div[data-src="act_libelle"] span').text();

                if ( test_existance == '' ) {
                    $('#corps_tableau div[id$="-MCR_'+mcr_id+'"]').remove();
                }

                $('#idModalAjouter').modal('hide'); // Cache la modale d'ajout.

                $( reponse[ 'texteHTML' ] ).prependTo( '#corps_tableau' );

                $('#corps_tableau .row').off('mouseenter');
                $('#corps_tableau .row').off('mouseleave');

                $('#corps_tableau .row').on('mouseenter', function( event ) {
                    $(this).find('div:last').addClass('text-right').removeClass('invisible');
                });

                $('#corps_tableau .row').on('mouseleave', function( event ) {
                    $(this).find('div:last').addClass('invisible');
                });

                if ( reponse[ 'droit_modifier' ] == 1 ) {
                    // Assigne l'événement "click" sur tous les boutons de Modification
                    $('.btn-modifier').click( function( event ){
                        var Id = $(this).attr('data-id');

                        ModalModifier( Id );
                    });
                }


                if ( reponse[ 'droit_supprimer' ] == 1 ) {
                    // Assigne l'événement "click" sur tous les boutons de Suppression
                    $('.btn-supprimer').click(function(){
                        var Id = $(this).attr('data-id');

                        var mcr_libelle = $('#ACT_'+Id).find('div[data-src="mcr_libelle"]').find('span').text();
                        var spp_nom = $('#ACT_'+Id).find('div[data-src="spp_nom"]').find('span').text();
                        var act_libelle = $('#ACT_'+Id).find('div[data-src="act_libelle"]').find('span').text();
                        var acteur = $('#ACT_'+Id).find('div[data-src="acteur"]').find('span').text();
                        var act_priorite = $('#ACT_'+Id).find('div[data-src="act_priorite"]').find('span').text();
                        var date_debut = $('#ACT_'+Id).find('div[data-src="date_debut"]').find('span').text();
                        var date_fin = $('#ACT_'+Id).find('div[data-src="date_fin"]').find('span').text();
                        var act_statut_libelle = $('#ACT_'+Id).find('div[data-src="act_statut_libelle"]').find('span').text();

                        ModalSupprimer( Id, mcr_libelle, spp_nom, act_libelle, acteur, act_priorite, date_debut, date_fin, act_statut_libelle );
                    });
                }


                var total = $( '#totalOccurrences' ).text();
                total = Number(total) + 1;
                $( '#totalOccurrences' ).text( ajouterZero( total ) );

                afficherMessage( reponse['texteMsg'], reponse['statut'], 'body' );
            } else {
                afficherMessage( reponse['texteMsg'], reponse['statut'], '#idModalAjouter' );
            }
        }
    });
}
