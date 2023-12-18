// ======================================================================
// Mise en place des écoutes sur les événements produits par les objets.
$(function() {
    // Ajouter l'entité dans la base.
    $(".btn-ajouter").on('click', function(){
        ModalAjouterEntite();
    });
});



// ============================================
// Fonctions répondant aux événements écoutés.

function ModalAjouterEntite(){
    $.ajax({
        url: '../../../Loxense-Entites.php?Action=AJAX_Libeller',
        type: 'POST',
        dataType: 'json',
        success: function( reponse ) {
            construireModal( 'idModalEntite',
                reponse[ 'Titre' ],
                '<div class="form-group">' +
                '<label class="col-lg-2 col-form-label" for="ent_libelle">' + reponse[ 'L_Libelle' ] + '</label>' +
                '<div class="col-lg-10">' +
                '<input id="ent_libelle" class="form-control" type="text" required autofocus>' +
                '</div>' +
                '</div>',
                'idBoutonAjouter', reponse[ 'L_Ajouter' ],
                true, reponse[ 'L_Fermer' ],
                'formAjouterEntite' );

            // Affiche la modale qui vient d'être créée
            $('#idModalEntite').modal('show');

            // Attend que la modale soit affichée avant de donner le focus au champ.
            $('#idModalEntite').on('shown.bs.modal', function() {
                $('#ent_libelle').focus();
            });

            $('#idModalEntite').on('hidden.bs.modal', function() {
                $('#idModalEntite').remove(); // Supprime la modale d'ajout.
            });

            $('#formAjouterEntite').submit( function( event ) { // Gère la soumission du formulaire.
                event.preventDefault(); // Laisse le contrôle au Javascript.

                AjouterEntite();
            } );


        }
    });

}


function AjouterEntite() {
    var Libelle = $('#ent_libelle').val();

    var total = $( '#totalOccurrences' ).text();
    total = Number(total) + 1;

    $.ajax({
        url: '../../../Loxense-Entites.php?Action=AJAX_Ajouter',
        type: 'POST',
        data: $.param({'libelle': Libelle}), // les paramètres sont protégés avant envoi
        dataType: 'json', // le résultat est transmit dans un objet JSON
        success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
            var statut = reponse['statut'];
            var texteMsg = reponse['texteMsg'];
            if ( statut == 'success' ) {

                $('#idModalEntite').modal('hide'); // Cache la modale d'ajout.

                afficherMessage( texteMsg, statut, 'body' );

                // Vérifie s'il y a une limitation à la création des Entités.
                if ( total >= reponse['limitation'] && reponse['limitation'] != 0 ) {
                    var old_title = $('div#titre_ecran button.btn-ajouter').attr('title');

                    $('div#titre_ecran button.btn-ajouter').attr('disabled', 'disabled').attr('title', reponse['libelle_limitation']).attr('data-old_title', old_title);
                } else {
                    var old_title = $('div#titre_ecran button.btn-ajouter').attr('data-old_title');

                    $('div#titre_ecran button.btn-ajouter').removeAttr('disabled').attr('title', old_title);
                }

                $( reponse[ 'texte' ] ).prependTo( '#corps_tableau' );

                $( '#totalOccurrences' ).text( ajouterZero( total ) );

                // Assigne l'événement "click" sur le bouton de Modification
                if ( reponse[ 'droit_modifier' ] == true ) {
                    $('#ENT_' + reponse[ 'id' ]).find('button.btn-modifier').click(function(event){
                        ouvrirChamp( event, 'ent_libelle', 'ENT_' + reponse[ 'id' ] );
                    });
                }

                // Assigne l'événement "click" sur le bouton de Suppression
                if ( reponse[ 'droit_supprimer' ] == true ) {
                    $('#ENT_' + reponse[ 'id' ] + ' .btn-supprimer').click(function(){
                        ModalSupprimer( reponse[ 'id' ], Libelle );
                    });
                }
            } else {
                afficherMessage( texteMsg, statut, '#idModalEntite', 0, 'n' );
            }
        }
    });
}
