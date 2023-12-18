$(function() {
    // Charge les données du tableau.
    trier( $( 'div#entete_tableau div.row div:first'), true, $('ul.nav-tabs li a:first').attr('id') );

    $('ul.nav-tabs li:first a').addClass('active');


    // Active l'écoute du "click" sur les libellés de l'entête du tableau.
    $('.triable').click( function() {
        trier( this, true, $('ul.nav-tabs li a.active').attr('id') );
    });


    // Active l'écoute du click sur les onglets.
    $('ul.nav-tabs li a').on( 'click', function() {
        $('ul.nav-tabs li a').removeClass('active');

        $(this).addClass('active');

        trier( $( 'div#entete_tableau div.row div:first'), false, $(this).attr('id') );

        $('div#corps_tableau input:first').focus();
    });
});


function controler_mdp() {
    var MdP_tmp = $('#mdp_limitation_tmp').val();

    $.ajax({
        url: '../../../Loxense-Parametres.php?Action=AJAX_Controler_MdP',
        type: 'POST',
        data: $.param({'mdp_limitation_tmp': MdP_tmp}),
        dataType: 'json',

        success: function( reponse ){
            var statut = reponse['statut'];

            if ( statut == 'error' ) {
                afficherMessage( reponse['texteMsg'], statut );
                return false;
            } else {
                trier( $( 'div#entete_tableau div.row div:first'), false, 'Limitations' );
            }
        }
    });
}


function trier( myElement, changerTri, groupe ) {
    // AJAX changeant la valeur du filtre
    var sens_recherche = $( myElement ).attr( 'data-sens-tri' ) || '';
    var changer_tri = changerTri || false;

    $.ajax({
        url: '../../../Loxense-Parametres.php?Action=AJAX_Trier',
        type: 'POST',
        data: $.param({'trier': sens_recherche, 'groupe': groupe}),
        dataType: 'json', // le résultat est transmit dans un objet JSON
        success: function( reponse ){
            var statut = reponse['statut'];

            if( statut == 'success' ){
                var texteMsg = reponse['texteHTML'];

                $('div#corps_tableau').html( reponse[ 'texteHTML'] );

                if ( reponse['ctrl_mdp'] == 1 ) {
                    $('#ctrl_mdp').on( 'click', function() {
                        controler_mdp();

                        return false;
                    });
                }

                if ( changer_tri == true ) {
                    if ( sens_recherche != '' ) {
                        var Element = sens_recherche.split('-');
                        if ( Element[ Element.length - 1 ] == 'desc' ) {
                            sens_recherche = Element[ 0 ];
                        } else {
                            sens_recherche = Element[ 0 ] + '-desc';
                        }
                    }
                }

                $(myElement).attr( 'data-sens-tri', sens_recherche );

                $('#totalOccurrences').text( ajouterZero(reponse[ 'total' ] ) );


                if ( reponse[ 'droit_modifier' ] == 1 ) {
                    // Assigne l'événement "click" sur tous les boutons de Modification
                    $('.btn-modifier').click( function( event ){
                        var Id = $(this).attr('data-id');

                        ouvrirChamp( event, Id );
                    });
                }


                $('input[name="authentification_type"]').on( 'click', function() {
                    activeTypeAuthentification();
                });


                $('#generer_conf_ldap').on('click', function() {
                    var LDAP_IP_Address = $('input[data-name="ldap_ip_address"]').val();
                    var LDAP_IP_Port = $('input[data-name="ldap_ip_port"]').val();
                    var LDAP_Protocol_Version = $('input[data-name="ldap_protocol_version"]').val();
                    var LDAP_Organization = $('input[data-name="ldap_organization"]').val();
                    var LDAP_RDN_Prefix = $('input[data-name="ldap_rdn_prefix"]').val();
                    var LDAP_SSL = $('input[data-name="ldap_ssl"]').is(':checked');

                    $.ajax({
                        url: '../../../Loxense-Parametres.php?Action=AJAX_Generer_Conf_LDAP',
                        type: 'POST',
                        data: $.param({
                            'ldap_ip_address': LDAP_IP_Address,
                            'ldap_ip_port': LDAP_IP_Port,
                            'ldap_protocol_version': LDAP_Protocol_Version,
                            'ldap_organization': LDAP_Organization,
                            'ldap_rdn_prefix': LDAP_RDN_Prefix,
                            'ldap_ssl': LDAP_SSL
                        }),
                        dataType: 'json', // le résultat est transmit dans un objet JSON
                        success: function( reponse ){
                            var statut = reponse['statut'];
                            var texteMsg = reponse['texteMsg'];

                            afficherMessage( texteMsg, statut );
                        }
                    });

                });

                
                redimensionnerWindow();
            } else {
                var texteMsg = reponse['texteMsg'];

                afficherMessage( texteMsg, statut );
            }
        }
    });
}

function activeTypeAuthentification() {
    var Type = $('input[name="authentification_type"]:checked').val();

    if (Type == 'D') {
        $('input[data-name="min_password_size"]').removeAttr('disabled');
        $('select[data-name="password_complexity"]').removeAttr('disabled');
        $('input[data-name="account_lifetime"]').removeAttr('disabled');
        $('input[data-name="max_attempt"]').removeAttr('disabled');
        $('input[data-name="default_password"]').removeAttr('disabled');

        $('input[data-name="ldap_ip_address"]').attr('disabled','disabled');
        $('input[data-name="ldap_ip_port"]').attr('disabled','disabled');
        $('input[data-name="ldap_protocol_version"]').attr('disabled','disabled');
        $('input[data-name="ldap_organization"]').attr('disabled','disabled');
        $('input[data-name="ldap_rdn_prefix"]').attr('disabled','disabled');
        $('input[data-name="ldap_ssl"]').attr('disabled','disabled');
    } else if (Type == 'L') {
        $('input[data-name="ldap_ip_address"]').removeAttr('disabled');
        $('input[data-name="ldap_ip_port"]').removeAttr('disabled');
        $('input[data-name="ldap_protocol_version"]').removeAttr('disabled');
        $('input[data-name="ldap_organization"]').removeAttr('disabled');
        $('input[data-name="ldap_rdn_prefix"]').removeAttr('disabled');
        $('input[data-name="ldap_ssl"]').removeAttr('disabled');

        $('input[data-name="min_password_size"]').attr('disabled','disabled');
        $('select[data-name="password_complexity"]').attr('disabled','disabled');
        $('input[data-name="account_lifetime"]').attr('disabled','disabled');
        $('input[data-name="max_attempt"]').attr('disabled','disabled');
        $('input[data-name="default_password"]').attr('disabled','disabled');
    }

}