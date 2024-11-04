$(function() {
	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first'), false );

	// Active l'écoute du "click" sur les libellés de l'entête du tableau.
	$('.triable').click( function() {
		$('div#entete_tableau div.recherche input[name^="rech-"]').off( "keypress" );
		$('div#entete_tableau div.recherche select[name^="rech-"]').off( "keypress" );
		trier( this, true );
	});
});


function trier( myElement, changerTri ) {
	// AJAX changeant la valeur du filtre
	var sens_recherche = $( myElement ).attr( 'data-sens-tri' );
	var changer_tri = changerTri || false;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Trier',
		type: 'POST',
		data: $.param({'trier': sens_recherche}),
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ){
			var statut = reponse['statut'];

			if( statut == 'success' ){
				var texteMsg = reponse['texteHTML'];

				$('div#corps_tableau').html( reponse[ 'texteHTML'] );

				if ( changer_tri == true ) {
					var Element = sens_recherche.split('-');
					if ( Element[ Element.length - 1 ] == 'desc' ) {
						sens_recherche = Element[ 0 ];
					} else {
						sens_recherche = Element[ 0 ] + '-desc';
					}
				}

				// Postionne la couleur sur la colonne active sur le tri.
				$('div#entete_tableau div.row div.triable').removeClass('active');
				$(myElement).addClass('active');
				//$('div#entete_tableau div.triable[data-sens-tri="hac_date"]').addClass('active');

				$(myElement).attr( 'data-sens-tri', sens_recherche );

				$('#totalOccurrences').text( ajouterZero(reponse[ 'total' ] ) );

				// Met en place une écoute sur les champs de recherche.
				$('div#entete_tableau div.recherche [name^="rech-"]').on( "keypress", function( event ) {
					if ( event.which == KEY_RETURN ) {
						event.preventDefault();
						rechercher();
					}
				});

				$('div#entete_tableau div.recherche select[name^="rech-"]').on( "change", function( event ) {
					event.preventDefault();
					rechercher();
				});

				$('input[name="rech-date_1"]').val( reponse['date_debut'] );


				redimensionnerWindow();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	});
}


function rechercher() {
	var Date_Debut = $('div#entete_tableau div.recherche input[name="rech-date_1"]').val();
	var Date_Fin = $('div#entete_tableau div.recherche input[name="rech-date_2"]').val();
	var Label_ATP = $('div#entete_tableau div.recherche select[name="rech-libelle_tpa"]').val();
	var Label_OTP = $('div#entete_tableau div.recherche select[name="rech-libelle_tpo"]').val();
	var User = $('div#entete_tableau div.recherche input[name="rech-user"]').val();
	var IP_User = $('div#entete_tableau div.recherche input[name="rech-ip_user"]').val();
	var Detail = $('div#entete_tableau div.recherche input[name="rech-detail"]').val();

	// Inverse le sens inscrit pour conserver le sens actuel.
	var sens_recherche = $( 'div#entete_tableau div.row div.active' ).attr( 'data-sens-tri' );
	var tmp = sens_recherche.split('-');
	if ( tmp.length == 2 ) {
		sens_recherche = tmp[0];
	} else {
		sens_recherche += '-desc';
	}

//alert(Date_Debut+', '+Date_Fin+', '+Label_ATP+', '+Label_OTP+', '+User+', '+IP_User+', '+Detail+', '+sens_recherche);

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Trier',
		type: 'POST',
		data: $.param({
			'trier': sens_recherche,
			'date_debut': Date_Debut,
			'date_fin': Date_Fin,
			'libelle_tpa': Label_ATP,
			'libelle_tpo': Label_OTP,
			'user': User,
			'ip_user': IP_User,
			'detail': Detail
			}),
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ){
			var statut = reponse['statut'];

			if( statut == 'success' ){
				$('div#corps_tableau').html( reponse[ 'texteHTML'] );

				$('#totalOccurrences').text( ajouterZero(reponse[ 'total' ] ) );

				redimensionnerWindow();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	});
}