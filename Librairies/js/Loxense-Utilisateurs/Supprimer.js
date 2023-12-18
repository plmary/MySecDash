function ModalSupprimer( Id, ent_libelle, cvl_label, idn_login ){
    var Civilite;

    $.ajax({
        url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Libeller',
        type: 'POST',
        dataType: 'json',
        success: function( reponse ) {
            var recherche_occ = $('.row').find('[data-src="idn_login"]').find('span').each( function( index ){
                if ( $(this).text() == idn_login ) {
                    $(this).parent().parent().find('[data-src="cvl_label"]').find('span').each( function( index ){
                        Civilite = $(this).text();
                    })
                }
            });

            var Corps =
                '<div id="IDN-SUPR">' +
                reponse['L_Confirmer'].replace("%cvl", Civilite).replace("%idn", idn_login) +
                '</div>';

            construireModal( 'idModalSupprimer',
                reponse[ 'Titre2' ],
                Corps,
                'idBoutonSupprimer', reponse[ 'L_Supprimer' ],
                true, reponse[ 'L_Fermer' ],
                'formSupprimer' );

            $('#idModalSupprimer').modal('show'); // Affiche la modale qui vient d'être créée

            // Attend que la modale soit affichée avant de donner le focus au champ.
            $('#idModalSupprimer').on('shown.bs.modal', function() {
                $('#idBoutonSupprimer').focus();
            });

            // Supprime la modale après l'avoir caché.
            $('#idModalSupprimer').on('hidden.bs.modal', function() {
                $('#idModalSupprimer').remove();
            });

            $('#formSupprimer').submit( function( event ) { // Gère la soumission du formulaire.
                event.preventDefault(); // Laisse le contrôle au Javascript.

                supprimerIdentite( Id, ent_libelle, cvl_label, idn_login );
            } );
        }
    });
}


// Supprimer l'Entité dans la BDD et à l'écran.
function supprimerIdentite( Id, ent_libelle, cvl_label, idn_login ) {
    var total = $( '#totalOccurrences' ).text();
    total = Number(total) - 1;

    $.ajax({
        url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Supprimer',
        type: 'POST',
        data: $.param({'id': Id, 'ent_libelle': ent_libelle, 'cvl_label': cvl_label, 'idn_login': idn_login}),
        dataType: 'json',
        success: function( reponse ) {
            var statut = reponse[ 'statut' ];
            var texteMsg = reponse[ 'texteMsg' ];

            if ( statut == 'success' ) {
                $( '#IDN_' + Id ).remove();

                // Vérifie s'il y a une limitation à la création des Entités.
                gererBoutonAjouter( total, reponse['limitation'], reponse['libelle_limitation'] );

                $( '#totalOccurrences' ).text( ajouterZero( total ) );
            }

            afficherMessage( texteMsg, statut, 'body' );
        }
    });

    $('.modal').modal('hide');
}