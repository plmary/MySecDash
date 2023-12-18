$(function() {
	$("#onglet-administrateur").click(function() {
		var onglet = $(this);
		$('a.nav-link').removeClass('active');
		//$(onglet).parent().addClass('active');
		$(onglet).addClass('active');


		$.ajax({
			url: '../../../Loxense-Principal.php?Action=AJAX_Tableau_Bord_Admin',
			type: 'POST',
			//data: $.param({'s_ent_id': $("#s_ent_id").val()}),
			dataType: 'json',

			success: function(reponse){
				if ( reponse['statut'] == 'success' ) {
					$( '#corps_tableau' ).html( reponse['texteHTML'] );
					redimensionnerWindow();
				} else {
					afficherMessage( reponse['texteMsg'], reponse['statut'] );
				}

			}
		});
	});


	$("#onglet-utilisateur").click(function() {
		var onglet = $(this);
		$('a.nav-link').removeClass('active');
		$(onglet).addClass('active');

		$.ajax({
			url: '../../../Loxense-Principal.php?Action=AJAX_Tableau_Bord_Utilisateur',
			type: 'POST',
			//data: $.param({'s_ent_id': $("#s_ent_id").val()}),
			dataType: 'json',

			success: function(reponse){
				if ( reponse['statut'] == 'success' ) {
					$( '#corps_tableau' ).html( reponse['texteHTML'] );
					//$(".knob").knob();
					redimensionnerWindow();
		
					$('#iRechCarto').keyup( function( event ) {
						$('p.titre').each( function(myIndex,myElement) {
							var parent = $(myElement).parent().attr('id');
							
							if ( $(myElement).text().indexOf($('#iRechCarto').val()) == -1 ) {
								$('div#'+parent).hide();
							} else {
								$('div#'+parent).show();
							}

							//alert(myIndex+', text="'+$(myElement).text()+'", parent="'+$(myElement).parent().attr('id')+'"');
						});
					});
					
					$('div.tableau_synthese p.titre').click(function(){
							var parent = $(this).parent().attr('id');
							window.location = Parameters['URL_BASE']+'/Loxense-CartographiesRisques.php';
					});
				} else {
					afficherMessage( reponse['texteMsg'], reponse['statut'] );
				}

			}
		});
	});

	if ( $('#titre_ecran').attr('data-admin') == 1 ) {
		$("#onglet-administrateur").trigger('click');
	} else {
		$("#onglet-utilisateur").trigger('click');
	}

}); 
