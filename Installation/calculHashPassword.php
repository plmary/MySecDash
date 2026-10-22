<?php

$Nbr_Parametres = count($argv);

if ($Nbr_Parametres < 2) {
	print( "\n%%I - Pas d'action de précisée\n\n" .
		"Usage : " . $argv[0] . " [action] [Mot de passe] [Hash]\n" .
		"[action] = C => pour calculer un Hash\n" .
		"           V => pour vérifier un Hash\n" .
		"[Mot de passe] = à calculer ou vérifier\n" .
		"[Hash] = à préciser, si on cherche à vérifier que le [Hash] correspond au [Mot de passe] fournit\n\n" );

	exit();
} else {

	switch( strtoupper( $argv[1] ) ) {
		case 'C':
			if ($Nbr_Parametres == 2) {
				$Default_Password = 'Welcome !';
			} elseif ($Nbr_Parametres >= 3) {
				$Default_Password = $argv[2];
			}
			print( "\nRésultat :\n" .
				"Mot de passe = [" . $Default_Password . "]\n" .
				"Hash         = [" . password_hash($Default_Password, PASSWORD_BCRYPT) ."]\n\n" );

			break;
		case 'V':
			if ($Nbr_Parametres < 4) {
				print( "\n%%E - Nombre de paramètres invalide pour la commande de vérification\n\n" );

				break;
			}

			$Default_Password = $argv[2];
			$Hash = $argv[3];

			$Verification = password_verify($Default_Password, $Hash);

			print( "\nRésultat :\n" .
				"Mot de passe = [" . $Default_Password . "]\n" .
				"Hash         = [" . $Hash ."]\n" .
				"Contrôle     = [" . (($Verification == true) ? 'OK' : 'KO') . "]\n\n" );
			
			break;
		default:
			print( "\n%%E - Commande inconnue [" . $argv[1] . "]\n\n" );

			break;
	}
}

?>