<?php

include_once( 'Constants.inc.php' );
include( HBL_CONFIG_BD );


class SGBDR {
	public function sauvegardeBase( $Version_Loxense ) {
		/**
		 * Sauvegarde la base de données PostgreSQL.
		 *
		 * @license Copyright Loxense
		 * @author Pierre-Luc MARY
		 * @date 2017-12-14
		 *
		 * @param[in] $Type_Sauvegarde Spécifie le type de sauvegarde (1=Données seulement, 2=Structure seulement)
		 *
		 * @return Retourne "vrai" si la sauvegarde c'est terminée normalement.
		 */
		include( HBL_CONFIG_BD );
		
		$_Version = str_replace('-', '_', str_replace('.', '_', $Version_Loxense));
		
		$Nom_Fichier = DIR_SAUVEGARDES . DIRECTORY_SEPARATOR . 'Loxense-' . $_Version . '-Base-' . date("Y_m_d-H_i_s") . '.sql';
		
		if ( ! putenv("PGPASSWORD=" . $_Password) ) {
			return array( 0, '"putenv" error' );
		}
		
		$dumpcmd = array(
			DIR_BIN_PG . "pg_dump",
			"-U", escapeshellarg($_User), // Précise l'Utilisateur
			"-h", escapeshellarg($_Host), // Précise le nom de la machine
			"-p", escapeshellarg($_Port), // Précise le port de la machine
			"-b", // Exporte les "blobs"
			"-C", // Supprime la base avant sa recréation
			"-c", // Supprime les objets avant leurs recréation
			"--if-exists", // Contrôle l'existance d'un objet avant création
			"-f", escapeshellarg($Nom_Fichier), // Précise le nom du fichier de sauvegarde
			escapeshellarg($_Base) // Précise le nom de la base
		);

		$result = exec( join(' ', $dumpcmd), $cmdout, $cmdresult );
		putenv("PGPASSWORD");
		
		if ( $cmdresult != 0 ) {
			return array( 0, $Nom_Fichier );
		} else {
			return array( 1, $Nom_Fichier );
		}
	}
	
	
	public function restaureBase( $Version, $Date ) {
		/**
		 * Restaure une base de données PostgreSQL.
		 *
		 * @license Copyright Loxense
		 * @author Pierre-Luc MARY
		 * @date 2017-12-19
		 *
		 * @param[in] $Version Version de Loxense du fichier de sauvegarde
		 * @param[in] $Date Date de la sauvegarde
		 *
		 * @return Retourne "vrai" si la sauvegarde c'est terminée normalement.
		 */
		include( HBL_CONFIG_BD );
		
		$Nom_Fichier = DIR_SAUVEGARDES . DIRECTORY_SEPARATOR . 'Loxense-' . $Version . '-Base-' . $Date . '.sql';
		
		// Recréé la base, toutes les tables et injecte les données.
		putenv("PGPASSWORD=" . $_Password);
		
		$dumpcmd = array(
			DIR_BIN_PG . "psql",
			"-U", escapeshellarg( $_User ), // Précise l'Utilisateur de la base de données
			"-h", escapeshellarg( $_Host ), // Précise le nom du serveur hébergeant la base de données
			"-p", escapeshellarg( $_Port ), // Précise le port d'écoute du serveur
			"-f", escapeshellarg( $Nom_Fichier ), // Nom du fichier de sauvegarde à restaurer
			'postgres' // Connexion à la base générique
		);
		
		$result = exec( join( ' ', $dumpcmd ), $cmdout, $cmdresult );
		
		putenv("PGPASSWORD");
		
		/*
		 print_r($dumpcmd);print('<br>');
		 print_r($cmdout);print('<br>');
		 print_r($cmdresult);print('<br>');
		 print_r($result);print('<br>');
		 */
		
		if ( $cmdresult != 0 ) {
			return array( 0, $cmdresult, $cmdout, $result );
		} else {
			return array( 1, $cmdresult, $cmdout, $result );
		}
	}
}

?>