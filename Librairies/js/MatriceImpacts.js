function chargerSimpleMatrice(Liste_Niveaux_Impact, Liste_Types_Impact, Liste_Matrice_Impacts, L_Type, L_Niveau) {
	// Mise à jour du corps du tableau.
	Occurrence = '';

	if (Liste_Niveaux_Impact != undefined) {
		for (let Niveau_Impact of Liste_Niveaux_Impact) {
			Occurrence += '<div class="row niveau-impact" data-poids="'+Niveau_Impact.nim_poids+'" data-nim_id="'+Niveau_Impact.nim_id+'">' +
				'<div class="d-grid gap-2 col col-titre-tableau-matrice">' + // style="background-color: #'+Niveau_Impact.nim_couleur+';">' +
				Niveau_Impact.nim_poids+' - '+Niveau_Impact.nim_nom_code+
				'</div> <!-- .col -->';

			if (Liste_Types_Impact != undefined) {
				for (let Type_Impact of Liste_Types_Impact) {
					if (Liste_Matrice_Impacts[Niveau_Impact.nim_id+'-'+Type_Impact.tim_id] != undefined) {
						Description = Liste_Matrice_Impacts[Niveau_Impact.nim_id+'-'+Type_Impact.tim_id].mim_description;
						mim_id = Liste_Matrice_Impacts[Niveau_Impact.nim_id+'-'+Type_Impact.tim_id].mim_id;
					} else {
						Description = '';
						mim_id = '';
					}
					Occurrence += '<div class="col cellule-impact" style="background-color: #'+Niveau_Impact.nim_couleur+';" data-tim_id="'+Type_Impact.tim_id+'">' +
						'<span id="description-'+Niveau_Impact.nim_id+'-'+Type_Impact.tim_id+'">'+Description+'</span>' +
						'</div> <!-- .cellule_impact -->';
				}
			}
			Occurrence += '</div> <!-- .row -->';
		}
	}


	Tableau_Complet = '<div id="entete-tableau-matrice" class="container-fluid" style="top: 133.65px;">' +
		'<div class="row">' +
		'<div class="col rotation-10 representation">' +
		L_Type + '<hr>' + L_Niveau +
		'</div> <!-- .representation -->';

	if (Liste_Types_Impact.length > 0) {
		for (let Type_Impact of Liste_Types_Impact) {
			Tableau_Complet += '<div class="d-grid gap-2 col type-impact" data-tim_poids="'+Type_Impact.tim_poids+'" data-tim_id="'+Type_Impact.tim_id+'">' +
				Type_Impact.tim_nom_code+
				'</div> <!-- .col -->';
		}
	}

	Tableau_Complet += '</div> <!-- .row -->' +
		'</div> <!-- #entete_tableau -->' +

		'<div id="corps_tableau" class="container-fluid">' + Occurrence + '</div>';

	return Tableau_Complet;
}


function chargerTableauMatrice(Liste_Niveaux_Impact, Liste_Types_Impact, Liste_Matrice_Impacts, L_Type, L_Niveau) {
	// Mise à jour du corps du tableau.
	Occurrences = '';

	if (Liste_Niveaux_Impact != undefined) {
		for (let Niveau_Impact of Liste_Niveaux_Impact) {
			Occurrences += '<tr class="niveau-impact" data-poids="'+Niveau_Impact.nim_poids+'" data-nim_id="'+Niveau_Impact.nim_id+'">' +
				'<td class="d-grid gap-2 col-titre-tableau-matrice" style="background-color: #'+Niveau_Impact.nim_couleur+';">' +
				Niveau_Impact.nim_poids+' - '+Niveau_Impact.nim_nom_code+
				'</td> <!-- .col -->';

			if (Liste_Types_Impact != undefined) {
				for (let Type_Impact of Liste_Types_Impact) {
					if (Liste_Matrice_Impacts[Niveau_Impact.nim_id+'-'+Type_Impact.tim_id] != undefined) {
						Description = Liste_Matrice_Impacts[Niveau_Impact.nim_id+'-'+Type_Impact.tim_id].mim_description;
						mim_id = Liste_Matrice_Impacts[Niveau_Impact.nim_id+'-'+Type_Impact.tim_id].mim_id;
						nim_numero = Liste_Matrice_Impacts[Niveau_Impact.nim_id+'-'+Type_Impact.tim_id].nim_numero;
						nim_couleur = Liste_Matrice_Impacts[Niveau_Impact.nim_id+'-'+Type_Impact.tim_id].nim_couleur;
					} else {
						Description = '';
						mim_id = '';
					}
					Occurrences += '<td class="align-top cellule-impact" ' +
						'style="background-color: #'+Niveau_Impact.nim_couleur+';" ' +
						'data-tim_id="'+Type_Impact.tim_id+'" data-nim_numero="'+nim_numero+'" ' +
						'data-nim_couleur="'+nim_couleur+'" id="mim_id-'+mim_id+'" data-mim_id="'+mim_id+'" ' +
						'data-nim_poids="'+Niveau_Impact.nim_poids+'" ' +
						'data-tim_nom="'+Type_Impact.tim_nom_code+'">' +
						'<span id="select-description-'+mim_id+'">'+
						Description +
						'</span>' +
						'</td>';
				}
			}
			Occurrences += '</tr>';
		}
	}


	Tableau_Complet = '<div id="tableau-matrice">' +
		'<table class="table-border-100">' +
//		'<div class="popover-arrow" style="z-index: 1070; display: block; width: 9px; height: 9px; position: absolute; left: 0px; transform: translate3d(60px, 0px, 0px); top: calc(-1 * 9);"></div>' + 
		'<thead>' +
		'<tr>' +
		'<th class="bleu-fonce rotation-10">' +
		L_Type + '<hr>' + L_Niveau +
		'</th>';

	if (Liste_Types_Impact.length > 0) {
		for (let Type_Impact of Liste_Types_Impact) {
			Tableau_Complet += '<th class="type-impact titre-fond-bleu" data-tim_poids="'+Type_Impact.tim_poids+'" data-tim_id="'+Type_Impact.tim_id+'">' +
				Type_Impact.tim_nom_code +
				'</th>';
		}
	}

	Tableau_Complet += '</tr>' +
		'</thead>' +
		'<tbody>' +
		Occurrences +
		'</tbody>' +
		'</table>' +
		'</div>';

	return Tableau_Complet;
}
