<?php
/*
 * Ce script vérifie que les répertoires sont présents, que les fichiers soient accessibles et qu'à minima que le fichier de
 * configuration soit cohérent.
 * 
 * Auteur : Pierre-Luc MARY
 * Le : 5 novembre 2024
 * 
 */

$Produit = 'MySecDash';

// Vérifie qu'à minima le script s'exécute au bon endroit.
if ( ! file_exists( '../Constants.inc.php' ) ) {
	print('%% -- Erreur -- Vous ne semblez pas dans le répertoire d\'installation du package ' . $Produit . "\n" );
	exit();
} else {
	if ( chdir('..') == FALSE ) {
		print('%% -- Erreur -- Impossible d\'aller à la racine du package ' . $Produit . '(' . 'x' . ')' . "\n");
		exit();
	}
}

/*
print('Contrôle 1 : vérification de la présence des répertoires obligatoires');
$Repertoires_Obligatoire = ['Images', 'Installation', 'Librairies'];
foreach($Repertoires_Obligatoire as $Repertoire) {
	if ( ! file_exists( $Repertoire ) ) {
		print( '%% -- Erreur -- répertoire "' . $Repertoire . '" obligatoire manquant. ' .
			'Il semble que vous ayez eu un problème à l\'installation du package ' . $Produit );
		exit();
	}
}

print('Contrôle 2 : création des répertoires tampons');
$Repertoires_A_Creer = ['Rapports', 'Temp'];
foreach($Repertoires_Obligatoire as $Repertoire) {
	if ( ! file_exists( $Repertoire ) ) {
		print( '%% Création du répertoire : "' . $Repertoire . '"' );
		mkdir( $Repertoire, 0775 );
	}
}
*/

print('Contrôle 3 : création des liens symboliques sur les dernières versions' . "\n");
// Gère le répertoire "Librairies"
$Repertoire = 'Librairies';
if ( chdir($Repertoire) == FALSE ) {
	print("%% -- Erreur -- Impossible d'aller dans le repertoire : $Repertoire\n");
	exit();
}

$Liste_Liens_A_Detruire = ['bootstrap-dist',  'bootstrap-select',  'summernote-dist'];
foreach( $Liste_Liens_A_Detruire as $Lien ) {
	if ( file_exists( $Lien ) ) {
		unlink( $Lien );
	}
}

$Liste_Max_Version_Fichiers = [];

$Repertoire_A_Lire = opendir( '.' );
while( $Fichier = readdir( $Repertoire_A_Lire ) ) {
	$tFichier = explode( '-', $Fichier );

	if ( $tFichier[0] == 'bootstrap' ) {
		//print($Fichier."\n");
		if ( $tFichier[1] == 'select' ) {
			if ( array_key_exists( 'bootstrap-select', $Liste_Max_Version_Fichiers ) ) {
				if ( $Liste_Max_Version_Fichiers['bootstrap-select']['version'] < $tFichier[2] ) {
					$Liste_Max_Version_Fichiers['bootstrap-select']['version'] = $tFichier[2];
					$Liste_Max_Version_Fichiers['bootstrap-select']['fichier'] = $Fichier;
				}
			} else {
				$Liste_Max_Version_Fichiers['bootstrap-select']['version'] = $tFichier[2];
				$Liste_Max_Version_Fichiers['bootstrap-select']['fichier'] = $Fichier;
			}
		} else {
			if ( array_key_exists( 'bootstrap-dist', $Liste_Max_Version_Fichiers ) ) {
				if ( $Liste_Max_Version_Fichiers['bootstrap-dist']['version'] < $tFichier[1] ) {
					$Liste_Max_Version_Fichiers['bootstrap-dist']['version'] = $tFichier[1];
					$Liste_Max_Version_Fichiers['bootstrap-dist']['fichier'] = $Fichier;
				}
			} else {
				$Liste_Max_Version_Fichiers['bootstrap-dist']['version'] = $tFichier[1];
				$Liste_Max_Version_Fichiers['bootstrap-dist']['fichier'] = $Fichier;
			}
		}
	}

	if ( $tFichier[0] == 'summernote' ) {
		if ( array_key_exists( 'summernote-dist', $Liste_Max_Version_Fichiers ) ) {
			if ( $Liste_Max_Version_Fichiers['summernote-dist']['version'] < $tFichier[1] ) {
				$Liste_Max_Version_Fichiers['summernote-dist']['version'] = $tFichier[1];
				$Liste_Max_Version_Fichiers['summernote-dist']['fichier'] = $Fichier;
			}
		} else {
			$Liste_Max_Version_Fichiers['summernote-dist']['version'] = $tFichier[1];
			$Liste_Max_Version_Fichiers['summernote-dist']['fichier'] = $Fichier;
		}
	}
}
closedir( $Repertoire_A_Lire );

foreach( $Liste_Max_Version_Fichiers as $Lien => $Source ) {
	symlink( $Source['fichier'], $Lien );
}


// Gère le répertoire "css"
$Repertoire = 'css';
if ( chdir($Repertoire) == FALSE ) {
	print("%% -- Erreur -- Impossible d'aller dans le repertoire : $Repertoire\n");
	exit();
}

$Liste_Liens_A_Detruire = [ 'bootstrap-icons' ];
foreach( $Liste_Liens_A_Detruire as $Lien ) {
	if ( file_exists( $Lien ) ) {
		unlink( $Lien );
	}
}

$Liste_Max_Version_Fichiers = [];

$Repertoire_A_Lire = opendir( '.' );
while( $Fichier = readdir( $Repertoire_A_Lire ) ) {
	$tFichier = explode( '-', $Fichier );
	
	if ( $tFichier[0] == 'bootstrap' ) {
		print($Fichier."\n");
		if ( $tFichier[1] == 'icons' ) {
			if ( array_key_exists( 'bootstrap-icons', $Liste_Max_Version_Fichiers ) ) {
				if ( $Liste_Max_Version_Fichiers['bootstrap-icons']['version'] < $tFichier[2] ) {
					$Liste_Max_Version_Fichiers['bootstrap-icons']['version'] = $tFichier[2];
					$Liste_Max_Version_Fichiers['bootstrap-icons']['fichier'] = $Fichier;
				}
			} else {
				$Liste_Max_Version_Fichiers['bootstrap-icons']['version'] = $tFichier[2];
				$Liste_Max_Version_Fichiers['bootstrap-icons']['fichier'] = $Fichier;
			}
		}
	}
}
closedir( $Repertoire_A_Lire );

foreach( $Liste_Max_Version_Fichiers as $Lien => $Source ) {
	symlink( $Source['fichier'], $Lien );
}


// Gère le répertoire "js"
$Repertoire = '../js';
if ( chdir($Repertoire) == FALSE ) {
	print("%% -- Erreur -- Impossible d'aller dans le repertoire : $Repertoire\n");
	exit();
}

$Liste_Liens_A_Detruire = [ 'jquery.min.js' ];
foreach( $Liste_Liens_A_Detruire as $Lien ) {
	if ( file_exists( $Lien ) ) {
		unlink( $Lien );
	}
}

$Liste_Max_Version_Fichiers = [];

$Repertoire_A_Lire = opendir( '.' );
while( $Fichier = readdir( $Repertoire_A_Lire ) ) {
	$tFichier = explode( '-', $Fichier );
	
	if ( $tFichier[0] == 'jquery' ) {
		print($Fichier."\n");
		if ( array_key_exists( 'jquery.min.js', $Liste_Max_Version_Fichiers ) ) {
			if ( $Liste_Max_Version_Fichiers['jquery.min.js']['version'] < $tFichier[1] ) {
				$Liste_Max_Version_Fichiers['jquery.min.js']['version'] = $tFichier[1];
				$Liste_Max_Version_Fichiers['jquery.min.js']['fichier'] = $Fichier;
			}
		} else {
			$Liste_Max_Version_Fichiers['jquery.min.js']['version'] = $tFichier[1];
			$Liste_Max_Version_Fichiers['jquery.min.js']['fichier'] = $Fichier;
		}
	}
}
closedir( $Repertoire_A_Lire );

foreach( $Liste_Max_Version_Fichiers as $Lien => $Source ) {
	symlink( $Source['fichier'], $Lien );
}


?>