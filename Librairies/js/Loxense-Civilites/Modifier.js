function ModalModifier( Id ){
    var Last_Name, First_Name;

    $.ajax({
        url: '../../../Loxense-Civilites.php?Action=AJAX_Charger',
        type: 'POST',
        dataType: 'json',
        async: false,
        data: $.param({'cvl_id': Id}), // les paramètres sont protégés avant envoi
        success: function( reponse ) {
            if ( reponse['statut'] == 'success' ) {
                Last_Name = protegerQuotes( reponse['last_name'] );
                First_Name = protegerQuotes( reponse['first_name'] );
            } else {
                afficherMessage( reponse['texteMsg'], statut );
                return;
            }
        }
    });


    $.ajax({
        url: '../../../Loxense-Civilites.php?Action=AJAX_Libeller',
        type: 'POST',
        dataType: 'json',
        success: function( reponse ) {
            var Corps =
                '<div class="row">' +
                '<label class="col-lg-2 col-form-label" for="cvl_nom">' + reponse[ 'L_Last_Name' ] + '</label>' +
                '<div class="col-lg-10">' +
                '<input id="cvl_nom" class="form-control" type="text" value="' + Last_Name + '" required>' +
                '</div>' +
                '</div>' +
                '<div class="row">' +
                '<label class="col-lg-2 col-form-label" for="cvl_prenom">' + reponse[ 'L_First_Name' ] + '</label>' +
                '<div class="col-lg-10">' +
                '<input id="cvl_prenom" class="form-control" type="text" value="' + First_Name + '" required>' +
                '</div>' +
                '</div>';

            construireModal( 'idModalCivilite',
                reponse[ 'Titre1' ],
                Corps,
                'idBoutonAjouter', reponse[ 'L_Modifier' ],
                true, reponse[ 'L_Fermer' ],
                'formModifierEntite' );

            $('#idModalCivilite').modal('show'); // Affiche la modale qui vient d'être créée

            // Attend que la modale soit affichée avant de donner le focus au champ.
            $('#idModalCivilite').on('shown.bs.modal', function() {
                $('#cvl_nom').focus();

                // On place le curseur après le dernier caractère.
                document.getElementById('cvl_nom').selectionStart = Last_Name.length;
            });

            // Détruit la modale quand cette dernière est désactivée.
            $('#idModalCivilite').on('hidden.bs.modal', function() {
                $('#idModalCivilite').remove();
            });

            $('#formModifierEntite').submit( function( event ) { // Gère la soumission du formulaire.
                event.preventDefault(); // Laisse le contrôle au Javascript.

                ModifierCivilite( Id );
            } );
        }
    });
}


function ModifierCivilite( Id ) {
    var Last_Name = $('#cvl_nom').val();
    var First_Name = $('#cvl_prenom').val();

    $.ajax({
        url: '../../../Loxense-Civilites.php?Action=AJAX_Modifier',
        type: 'POST',
        data: $.param({'cvl_id': Id,'last_name': Last_Name,'first_name': First_Name}), // les paramètres sont protégés avant envoi
        dataType: 'json', // le résultat est transmit dans un objet JSON
        success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
            var statut = reponse['statut'];
            var texteMsg = reponse['texteMsg'];

            if ( statut == 'success' ) {
                $('#idModalCivilite').modal('hide'); // Cache la modale d'ajout.

                // Met à jour les différents champs de l'occurrence modifiée.
                $('#CVL_' + Id).find('div[data-src="cvl_nom"]').find('span').text( Last_Name );
                $('#CVL_' + Id).find('div[data-src="cvl_prenom"]').find('span').text( First_Name );
            }

            afficherMessage( texteMsg, statut, 'body' );

        }
    });
}
