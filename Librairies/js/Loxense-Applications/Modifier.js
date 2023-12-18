function ModalModifier( Id ){
    var Libelle, Type, Code, Localisation, Liste;

    var Champ = $('div#APP_' + Id + ' div[data-src="app_code"]').text();

    $.ajax({
        url: '../../../Loxense-Applications.php?Action=AJAX_Lister_Type_App',
        type: 'POST',
        async: false,
        dataType: 'json',
        data: $.param({'libelle': Champ}), // les paramètres sont protégés avant envoi
        success: function(reponse){
            var statut = reponse['statut'];
            var texteMsg = reponse['texteMsg'];

            if (statut == 'success') {
                Liste = texteMsg;
            }
        }
    });


    $.ajax({
        url: '../../../Loxense-Applications.php?Action=AJAX_Charger',
        type: 'POST',
        dataType: 'json',
        async: false,
        data: $.param({'app_id': Id}), // les paramètres sont protégés avant envoi
        success: function( reponse ) {
            if ( reponse['statut'] == 'success' ) {
                Libelle = protegerQuotes( reponse['libelle'] );
                Type = reponse['type'];
                Code = reponse['code'];
                Localisation = protegerQuotes( reponse['localisation'] );
            } else {
                afficherMessage( reponse['texteMsg'], statut );
                return;
            }
        }
    });


    $.ajax({
        url: '../../../Loxense-Applications.php?Action=AJAX_Libeller',
        type: 'POST',
        dataType: 'json',
        success: function( reponse ) {
            var Corps =
                '<div class="row">' +
                '<label class="col-lg-2 col-form-label" for="app_libelle">' + reponse[ 'L_Libelle' ] + '</label>' +
                '<div class="col-lg-10">' +
                '<input id="app_libelle" class="form-control" type="text" value="' + Libelle + '" required>' +
                '</div>' +
                '</div>' +
                '<div class="row">' +
                '<label class="col-lg-2 col-form-label" for="app_code">' + reponse[ 'L_Type' ] + '</label>' +
                '<div class="col-lg-4">' +
                '<select id="app_code" class="form-select" required>' +
                Liste +
                '</select>' +
                '</div>' +
                '</div>' +
                '<div class="row">' +
                '<label class="col-lg-2 col-form-label" for="app_localisation">' + reponse[ 'L_Localisation' ] + '</label>' +
                '<div class="col-lg-10">' +
                '<input id="app_localisation" class="form-control" type="text" value="' + Localisation + '">' +
                '</div>' +
                '</div>';

            construireModal( 'idModalApplication',
                reponse[ 'Titre1' ],
                Corps,
                'idBoutonAjouter', reponse[ 'L_Modifier' ],
                true, reponse[ 'L_Fermer' ],
                'formModifierApplication', 'modal-lg' );

            $('#idModalApplication').modal('show'); // Affiche la modale qui vient d'être créée

            // Attend que la modale soit affichée avant de donner le focus au champ.
            $('#idModalApplication').on('shown.bs.modal', function() {
                $('#app_libelle').focus();

                // On place le curseur après le dernier caractère.
                document.getElementById('app_libelle').selectionStart = Libelle.length;
            });

            // Détruit la modale quand cette dernière est désactivée.
            $('#idModalApplication').on('hidden.bs.modal', function() {
                $('#idModalApplication').remove();
            });

            $('#formModifierApplication').submit( function( event ) { // Gère la soumission du formulaire.
                event.preventDefault(); // Laisse le contrôle au Javascript.

                ModifierApplication( Id );
            } );
        }
    });
}


function ModifierApplication( Id ) {
    var Libelle = $('#app_libelle').val();
    var Type = $('#app_code').val();
    var Code = $('#app_code option:selected' ).text()
    var Localisation = $('#app_localisation').val();

    $.ajax({
        url: '../../../Loxense-Applications.php?Action=AJAX_Modifier',
        type: 'POST',
        data: $.param({'app_id': Id,'libelle': Libelle,'type': Type,'localisation': Localisation}), // les paramètres sont protégés avant envoi
        dataType: 'json', // le résultat est transmit dans un objet JSON
        success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
            var statut = reponse['statut'];
            var texteMsg = reponse['texteMsg'];

            if ( statut == 'success' ) {
                $('#idModalApplication').modal('hide'); // Cache la modale d'ajout.

                // Met à jour les différents champs de l'occurrence modifiée.
                $('#APP_' + Id).find('div[data-src="app_libelle"]').find('span').text( Libelle );
                $('#APP_' + Id).find('div[data-src="app_code"]').find('span').text( Code );
                $('#APP_' + Id).find('div[data-src="app_localisation"]').find('span').text( Localisation );
            }

            afficherMessage( texteMsg, statut, 'body' );

        }
    });
}
