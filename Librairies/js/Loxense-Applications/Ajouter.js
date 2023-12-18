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
        url: '../../../Loxense-Applications.php?Action=AJAX_Libeller',
        type: 'POST',
        dataType: 'json',
        success: function( reponse ) {
            var Corps =
                '<div class="row">' +
                '<label class="col-lg-2 col-form-label" for="app_libelle">' + reponse[ 'L_Libelle' ] + '</label>' +
                '<div class="col-lg-10">' +
                '<input id="app_libelle" class="form-control" type="text" required>' +
                '</div>' +
                '</div>' +
                '<div class="row">' +
                '<label class="col-lg-2 col-form-label" for="tap_id">' + reponse[ 'L_Type' ] + '</label>' +
                '<div class="col-lg-4">' +
                '<select id="tap_id" class="form-select" required>' + reponse[ 'Liste_Types_Application' ] + '</select>' +
                '</div>' +
                '</div>' +
                '<div class="row">' +
                '<label class="col-lg-2 col-form-label" for="app_localisation">' + reponse[ 'L_Localisation' ] + '</label>' +
                '<div class="col-lg-10">' +
                '<input id="app_localisation" class="form-control" type="text" required>' +
                '</div>' +
                '</div>';

            construireModal( 'idModalApplication',
                reponse[ 'Titre' ],
                Corps,
                'idBoutonAjouter', reponse[ 'L_Ajouter' ],
                true, reponse[ 'L_Fermer' ],
                'formAjouterApplication', 'modal-lg' );

            $('#idModalApplication').modal('show'); // Affiche la modale qui vient d'être créée

            // Attend que la modale soit affichée avant de donner le focus au champ.
            $('#idModalApplication').on('shown.bs.modal', function() {
                $('#app_libelle').focus();
            });

            // Supprime la modale après l'avoir caché.
            $('#idModalApplication').on('hidden.bs.modal', function() {
                $('#idModalApplication').remove();
            });

            $('#formAjouterApplication').submit( function( event ) { // Gère la soumission du formulaire.
                event.preventDefault(); // Laisse le contrôle au Javascript.

                AjouterApplication();
            } );
        }
    });
}


function AjouterApplication() {
    var Libelle = $('#app_libelle').val();
    var Type_Application = $('#tap_id').val();
    var Localisation = $('#app_localisation').val();

    var total = $( '#totalOccurrences' ).text();
    total = Number(total) + 1;

    $.ajax({
        url: '../../../Loxense-Applications.php?Action=AJAX_Ajouter',
        type: 'POST',
        data: $.param({'app_libelle': Libelle,'tap_id': Type_Application,'app_localisation': Localisation}), // les paramètres sont protégés avant envoi
        dataType: 'json', // le résultat est transmit dans un objet JSON
        success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
            var statut = reponse['statut'];
            var texteMsg = reponse['texteMsg'];

            if ( statut == 'success' ) {
                $('#idModalApplication').modal('hide'); // Cache la modale d'ajout.

                afficherMessage( texteMsg, statut, 'body' );

                $( reponse[ 'texte' ] ).prependTo( '#corps_tableau' );
                $( '#totalOccurrences' ).text( ajouterZero( total ) );

                // Assigne l'événement "click" sur le bouton de Modification
                if ( reponse[ 'droit_modifier' ] == 1 ) {
                    $('#APP_' + reponse[ 'id' ] + ' .btn-modifier').click( function( event ){
                        ModalModifier( reponse[ 'id' ] );
                    });
                }

                // Assigne l'événement "click" sur le bouton de Suppression
                if ( reponse[ 'droit_supprimer' ] == 1 ) {
                    $('#APP_' + reponse[ 'id' ] + ' .btn-supprimer').click(function(){
                        var Libelle = $('#APP_'+reponse['id']).find('div[data-src="app_libelle"]').find('span').text();
                        var Type = $('#APP_'+reponse['id']).find('div[data-src="app_code"]').find('span').text();
                        var Localisation = $('#APP_'+reponse['id']).find('div[data-src="app_localisation"]').find('span').text();

                        ModalSupprimer( reponse['id'], Libelle, Type, Localisation );
                    });
                }
            } else {
                afficherMessage( texteMsg, statut, '#idModalApplication', 0, 'n' );
            }
        }
    });
}
