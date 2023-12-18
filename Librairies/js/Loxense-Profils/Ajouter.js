// ======================================================================
// Mise en place des écoutes sur les événements produits par les objets.
$(function() {
    // Ajouter l'entité dans la base.
    $(".btn-ajouter").on('click', function(){
        ModalAjouter();
    });
});



// ============================================
// Fonctions répondant aux événements écoutés.

function ModalAjouter(){
    $.ajax({
        url: '../../../Loxense-Profils.php?Action=AJAX_Libeller',
        type: 'POST',
        dataType: 'json',
        success: function( reponse ) {
            var Corps =
                '<div class="form-group">' +
                '<label class="col-lg-2 col-form-label" for="prf_libelle">' + reponse[ 'L_Libelle' ] + '</label>' +
                '<div class="col-lg-10">' +
                '<input id="prf_libelle" class="form-control" type="text" required>' +
                '</div>' +
                '</div>';

            construireModal( 'idModalProfil',
                reponse[ 'Titre' ],
                Corps,
                'idBoutonAjouter', reponse[ 'L_Ajouter' ],
                true, reponse[ 'L_Fermer' ],
                'formAjouterProfil' );

            $('#idModalProfil').modal('show'); // Affiche la modale qui vient d'être créée

            // Attend que la modale soit affichée avant de donner le focus au champ.
            $('#idModalProfil').on('shown.bs.modal', function() {
                $('#prf_libelle').focus();
            });

            // Supprime la modale après l'avoir caché.
            $('#idModalProfil').on('hidden.bs.modal', function() {
                $('#idModalProfil').remove();
            });

            $('#formAjouterProfil').submit( function( event ) { // Gère la soumission du formulaire.
                event.preventDefault(); // Laisse le contrôle au Javascript.

                AjouterProfil();
            } );
        }
    });
}


function AjouterProfil() {
    var Libelle = $('#prf_libelle').val();

    var total = $( '#totalOccurrences' ).text();
    total = Number(total) + 1;

    $.ajax({
        url: '../../../Loxense-Profils.php?Action=AJAX_Ajouter',
        type: 'POST',
        async: false,
        data: $.param({'libelle': Libelle}), // les paramètres sont protégés avant envoi
        dataType: 'json', // le résultat est transmit dans un objet JSON
        success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
            var statut = reponse['statut'];
            var texteMsg = reponse['texteMsg'];

            if ( statut == 'success' ) {
                $('div#entete_tableau div.profils').append(
                    '<div class="col-lg-1" data-id="' + reponse['id'] + '">' +
                    '<a href="#" class="modifiable">' + Libelle + '</a>' +
                    '<button class="bi-x-circle btn btn-outline-secondary btn-sm" title="' + reponse['libelle_delete_profil'] + '"></button>' +
                    '</div>'
                    );

                $('#idModalProfil').modal('hide'); // Cache la modale d'ajout.

                afficherMessage( texteMsg, statut, 'body' );

                $('div#corps_tableau div.row').each( function( Index ) {
                    var Id_Profil = reponse['id'];
                    var Id_Application = $(this).attr('data-id');

                    $(this).append( creerCellule( Id_Profil, Id_Application, '', reponse['libelles_droits'] ) );

                    $('div#corps_tableau div.row[data-id="'+Id_Application+'"] div.cellule[data-prf="'+Id_Profil+'"]').on('mouseover',function() {
                        var Profil_Courant = $(this).attr('data-prf');
                        var Application_Courante = $(this).attr('data-app');

                        $('div#corps_tableau div.row div.cellule[data-prf="'+Profil_Courant+'"]').css('background-color', '#dcafdd');
                        $(this).css('background-color', '#ffcc00'); //'#dcafdd');
                        $('div#entete_tableau div.profils div[data-id="'+Profil_Courant+'"]').css('color', '#ffcc00'); //'#dcafdd');
                        //$('div#corps_tableau div.row[data-id="'+Application_Courante+'"]').css('color', '#ffcc00'); // 'white');
                    });

                    $('div#corps_tableau div.row[data-id="'+Id_Application+'"] div.cellule[data-prf="'+Id_Profil+'"]').on('mouseout',function() {
                        var Profil_Courant = $(this).attr('data-prf');
                        var Application_Courante = $(this).attr('data-app');
                        
                        $('div#corps_tableau div.row div.cellule[data-prf="'+Profil_Courant+'"]').css('background-color','');
                        $('div#entete_tableau div.profils div[data-id="'+Profil_Courant+'"]').css('color', 'white'); //'#dcafdd');
                        //$('div#corps_tableau div.row[data-id="'+Application_Courante+'"]').css('color', 'black'); //'#dcafdd');
                    });
                });


                // Mise à jour du compteur totalisant le nombre de profils en stock.
                Total_Profils = $('div#entete_tableau div.row div.titre').attr('data-total_prf');
                Total_Profils = Number(Total_Profils) + 1;
                $('div#entete_tableau div.row div.titre').attr('data-total_prf', Total_Profils);

                if ( Total_Profils >= reponse['limitation'] ) {
                    $('button.btn-ajouter').attr('disabled', 'disabled');
                } else {
                    $('button.btn-ajouter').removeAttr('disabled');
                }

                activerBoutons();
                activerBoutonsSuppression();
                activerBoutonsModification();
            } else {
                afficherMessage( texteMsg, statut, '#idModalProfil', 0, 'n' );
            }
        }
    });
}


function ajouterColonne( Id_Profil ) {
    var Acces = Acces | '';
    var Cellule;
    var Id_Application;

    var Ligne = $('div#corps_tableau div.row');

    Ligne.each( function( Index ) {
        //$('div:last', this).after( 'test' );
        Id_Application = $(this).attr('data-id');
        Id_Bouton = Id_Profil+'-'+Id_Application;

        $.ajax({
            url: '../../../Loxense-Profils.php?Action=AJAX_Lister_Control_Acces',
            type: 'POST',
            async: false,
            dataType: 'json', // le résultat est transmit dans un objet JSON
            data: $.param({'id_profil': Id_Profil, 'id_application': Id_Application}),
            success: function( reponse ){
                var statut = reponse['statut'];

                if( statut == 'success' ){
                }
            }
        }); 

        $(this).children('div:last').after( Cellule );
    });

    // Mise à jour du compteur totalisant le nombre de profils en stock.
    Total_Profils = $('div#entete_tableau div.row div.titre').attr('data-total_prf');

    Total_Profils += 1;
    $('div#entete_tableau div.row div.titre').attr('data-total_prf', Total_Profils);

    if ( Total_Profils >= reponse['limitation'] ) {
        $('button.btn-ajouter').attr('disabled', 'disabled');
    } else {
        $('button.btn-ajouter').removeAttr('disabled');
    }

    activerBoutons();
    activerBoutonsSuppression();
    activerBoutonsModification();
}