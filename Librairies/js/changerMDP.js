// ============================================
// Fonctions répondant aux événements écoutés.

function changerMdP() {
    if ( $('#i_O_Password').val() == '' ) {
        $('#i_O_Password').focus();
        return;
    }

    if ( $('#i_N_Password').val() == '' ) {
        $('#i_N_Password').focus();
        return;
    }

    if ( $('#i_C_Password').val() == '' ) {
        $('#i_C_Password').focus();
        return;
    }

    $.ajax({
        url: Parameters['URL_BASE'] + '/MySecDash-Connexion.php?action=CMDP2X',
        type: 'POST', // la méthode indiquée dans le formulaire (get ou post)
        data: $.param({'O_Password': $('#i_O_Password').val(), 'N_Password': $('#i_N_Password').val(), 'C_Password': $('#i_C_Password').val()}), // le nom du fichier indiqué dans le formulaire
        dataType: 'json', // le résultat est transmit dans un objet JSON
        success: function(reponse) {
            var statut = reponse['statut'];
            var titreMsg = reponse['titreMsg'];
            var texteMsg = reponse['texteMsg'];

            if ( statut == 'success' ) {
                $('#changerMDP').modal('hide');

                $('body').notif({title: titreMsg,
                    content: texteMsg,
                    cls: 'success',
                    timeout: 2000});
            } else {
                $('body').notif({title: titreMsg,
                    content: texteMsg,
                    cls: 'error'});
            }
        },
        error: function(reponse) {
            alert('Erreur serveur "changerMDP" : ' + reponse['responseText']);
        }
    });
}
