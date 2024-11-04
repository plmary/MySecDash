<?php

/**
* Définit les constantes indispensables à la connaissance de l'architecture de Loxense.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @package MySecDash
* @date 2023-12-03
*
*/

// =============================================
// Définition des chemins internes à MySecDash.
define( 'SERVEUR', 'mysecdash1.local' );
define( 'CHEMIN_APPLICATION', realpath( dirname( __FILE__ ) ) );

define( 'URL_BASE',         'https://' . SERVEUR );
define( 'URL_IMAGES',       URL_BASE . '/Images' );
define( 'URL_LIBRAIRIES',   URL_BASE . '/Librairies' );
define( 'URL_PREUVES',      URL_BASE . '/Preuves' );
define( 'URL_INFORMATIONS', URL_BASE . '/Informations' );
define( 'URL_REFERENTIELS', URL_LIBRAIRIES . '/Referentiels' );

define( 'DIR_SESSION',      CHEMIN_APPLICATION . DIRECTORY_SEPARATOR . 'Temp' );
define( 'DIR_IMAGES',       CHEMIN_APPLICATION . DIRECTORY_SEPARATOR . 'Images' );
define( 'DIR_EDITIONS',     CHEMIN_APPLICATION . DIRECTORY_SEPARATOR . 'Editions');
define( 'DIR_LIBRAIRIES',   CHEMIN_APPLICATION . DIRECTORY_SEPARATOR . 'Librairies' );
define( 'DIR_PREUVES',      CHEMIN_APPLICATION . DIRECTORY_SEPARATOR . 'Preuves' );
define( 'DIR_INFORMATIONS', CHEMIN_APPLICATION . DIRECTORY_SEPARATOR . 'Informations' );
define( 'DIR_LIBELLES',     DIR_LIBRAIRIES . DIRECTORY_SEPARATOR . 'Libelles' );
define( 'DIR_RESTREINT',    DIR_LIBRAIRIES . DIRECTORY_SEPARATOR . 'Restreint' );
define( 'DIR_SAUVEGARDES',  CHEMIN_APPLICATION . DIRECTORY_SEPARATOR . 'Sauvegardes' );
define( 'DIR_REFERENTIELS', DIR_LIBRAIRIES . DIRECTORY_SEPARATOR . 'Referentiels' );
define( 'DIR_RAPPORTS',     CHEMIN_APPLICATION . DIRECTORY_SEPARATOR . 'Rapports');
define( 'DIR_TEMPLATES',    DIR_LIBRAIRIES . DIRECTORY_SEPARATOR . 'Templates' );



// ===============================
// Ressources externes à Loxense.
define( 'URL_CHARTJS',      URL_BASE . '/node_modules/chart.js/dist' );
define( 'DIR_CHARTJS',      CHEMIN_APPLICATION . DIRECTORY_SEPARATOR . 'node_modules' . DIRECTORY_SEPARATOR . 'chart.js' .
	DIRECTORY_SEPARATOR . 'dist' );
define( 'DIR_BIN_PG',       '/Users/pierre-lucmary/PostgreSQL/pg11/bin/' ); // Mac OS (Version 11)
//define( 'DIR_BIN_PG',       '/usr/bin/' ); // Debian (dernière version)


// ============================================================
// Définition des répertoires pour la gestion des habilitations.
define( 'HBL_DIR_LIBRARIES', DIR_LIBRAIRIES );
define( 'HBL_DIR_LABELS',    DIR_LIBELLES );


// ============================================================
// Définition des fichiers de configuration internes à Loxense.
define( 'HBL_CONFIG_BD',   DIR_RESTREINT . DIRECTORY_SEPARATOR . 'Config_Access_DB.inc.php' );
define( 'HBL_CONFIG_LDAP', DIR_RESTREINT . DIRECTORY_SEPARATOR . 'Config_LDAP.inc.php' );
define( 'CHF_CLES', DIR_RESTREINT . DIRECTORY_SEPARATOR . 'Config_Cles.inc.php' );


// ==========================================
// Définition des statuts internes à Loxense.
define( 'FLAG_ERREUR', 0 );
define( 'FLAG_SUCCES', 1 );

?>