$(document).ready(function() {
    // Gestion du bouton de "connexion"
    //$("#login-form").submit( function( event ) {
    $("#login-form").submit( function( event ) {
        event.preventDefault(); // Donne le contrôle au Javascript (plutôt qu'à Bootstrap)

        var Code_Utilisateur = $('input[name="Code_Utilisateur"]').val();
        var Mot_Passe = $('input[name="Mot_Passe"]').val();

        $.ajax({
            url: Parameters['URL_BASE'] + '/MySecDash-Connexion.php?action=AJAX_CNX',
            type: 'POST',
            dataType: 'json',
            data: $.param({'Code_Utilisateur': Code_Utilisateur, 'Mot_Passe': Mot_Passe}),
            success: function(reponse){
                var script_suivant = Parameters['URL_BASE'] + '/MyContinuity-Principal.php';  //'/MySecDash-Principal.php';

                if(reponse['statut'] == 'error') {
                    afficherMessage( reponse['texteMsg'], reponse['statut'], '#login-form', '', 'o' ); // Fonction dans "main.js"
                } else if (reponse['statut'] == 'warning') {
                    changerMdP( script_suivant );
                } else {
                    window.location = script_suivant;
                }
            }
        });
    });
});
