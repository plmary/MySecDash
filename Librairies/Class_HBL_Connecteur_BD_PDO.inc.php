<?php

include_once( 'Constants.inc.php' );

class HBL_Connexioneur_BD extends PDO {
/**
* Cette classe gère les connexions à la base de données tout en offrant une couche d'abastraction.
* Effectivement, seule cette classe connait la localisation du fichier de paramètre externe.
*
* \license Copyright Loxense
* \author Pierre-Luc MARY
* \date 2015-05-14
*/

	public $LastInsertId; // Dernier ID créé

	public $RowCount; // Nombre d'occurrences modifiées
	
	protected $objPDO; // Objet PDO créé à la connexion de la base de données


	public function __construct() {
	/**
	* Connexion à la base de données via le constructeur de PDO.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-14
	*
	* \return Renvoi "true" en cas de succès de connexion à la base de données, sinon lève une exception.
	*/
		// Charge les différentes variables utiles à la connexion à la base de données.
		include( HBL_CONFIG_BD );
		
		$DSN = $_Driver . ':host=' . $_Host . ';port=' . $_Port . ';dbname=' . $_Base ;

		try {
			return PDO::__construct( $DSN, $_User, $_Password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION] );
		} catch( PDOException $e ) {
			//throw new Exception( $e->getMessage(), $e->getCode() );
			die( $e->getMessage()." ".$e->getCode() );
			return FALSE;
		}
	}


	protected $transactionBegin = FALSE;


	public function begin_Transaction() {
	/**
	* Mise en place d'une Transaction (permettant l'exécution de plusieurs requêtes SQL et de valider cet ensemble)
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-10-15
	*
	* \return Passe l'objet en mode Transaction
	*/
		$this->transactionBegin = TRUE;

		return $this->beginTransaction();
	}


	public function commit_Transaction(){
	/**
	* Valide l'ensemble des requêtes de mise à jour de la Transaction.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-10-15
	*
	* \return Passe l'objet en mode "autocommit"
	*/
		if ( ! $this->transactionBegin ) return;

		$this->transactionBegin = FALSE;

		$this->commit();

		return TRUE;
	}


	public function rollback_Transaction(){
	/**
	* Annule l'ensemble des requêtes de mise à jour de la Transaction.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-10-15
	*
	* \return Conserve l'objet en mode Transaction
	*/
		if ( ! $this->transactionBegin ) return;

		$this->transactionBegin = FALSE;

		$this->rollBack();

		return TRUE;
	}


	public function prepareSQL( $sql ) {
	/**
	* Automatise la préparation d'une requète en ajoutant la gestion des exceptions
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2016-10-24
	*
	* \param[in] $sql La requète à préparer
	*
	* \return Renvoi la requète préparée
	*/
		// évite les espaces insécables dans la chaîne de caractère :s
		$sql = str_replace(" ", " ", $sql);

		if ( ! $Query = $this->prepare( $sql ) ) {//print('Error : '.$sql);
			$Error = $Query->errorInfo();

			if ( $this->transactionBegin == TRUE ) $this->rollback_Transaction();

			throw new Exception( $Error[ 2 ], $Error[ 1 ] );
		}

		return $Query;
	}


	public function bindSQL( $Query, $Reference, $Value, $Type, $Length = 10 ){
	/**
	* Automatise l'association des paramètres sur une requète SQL.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2016-10-24
	*
	* \param[in] $Query La requète modifier, passé en référence
	* \param[in] $Reference la chaine de caractère référence à remplacer dans la requête
	* \param[in] $Value la valeur à mettre à la place de la référence
	* \param[in] $Type le type de variable à remplacer. Pour l'instant ne sont géré que les entiers et les chaines de caractères
	* \param[in] $Length la longueur maximal de la chaine de caractère à remplacer
	*
	*/
		// Si le type est un "Numérique".
		if( $Type === PDO::PARAM_INT || $Type === PDO::PARAM_BOOL || $Type === PDO::PARAM_LOB || $Type === PDO::PARAM_NULL ) {
			if ( ! $Query->bindParam( $Reference, $Value, $Type ) ) {
				$Error = $Query->errorInfo();

				if ( $this->transactionBegin == TRUE ) $this->rollback_Transaction();

				throw new Exception( $Error[ 2 ], $Error[ 1 ] );
			}
		}
		// Si le type est une "chaine de caractères".
		elseif($Type === PDO::PARAM_STR){
			if ( ! $Query->bindParam( $Reference, $Value, $Type, $Length ) ) {
				$Error = $Query->errorInfo();

				if ( $this->transactionBegin == TRUE ) $this->rollback_Transaction();

				throw new Exception( $Error[ 2 ], $Error[ 1 ] );
			}
		}
		else {
			if ( $this->transactionBegin == TRUE ) $this->rollback_Transaction();

			throw new Exception( "bindSQL - Format de donnée non géré");
		}

		// Permet les appels en cascade 
		return $this;
	}


	public function executeSQL( $Query ){
	/**
	* Automatise l'exécution d'une requète
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2016-10-24
	*
	* \param[in] $Query La requète à executer, passé en référence
	*
	*/	
		$Status = $Query->execute();
		if ( $Status === FALSE ) {
			$Error = $Query->errorInfo();
			if ( $Error[ 0 ] == 23505 ) { // Gestion des doublons.
				//throw new Exception("Application Error (".$Error[0].', '.$Error[2].')', $Error[ 0 ]);
				throw new Exception("Application Error", $Error[ 0 ]);
			}

			if ( $this->transactionBegin == TRUE ) $this->rollback_Transaction();

			$message = $Error[ 2 ] . ' (SQL: ' . $Error[ 0 ] . ')'; //(' . $Query->queryString . ')';

			throw new Exception( $message, $Error[ 1 ] );
		}

		$this->RowCount = $Query->rowCount();
		
		// permet les appels en cascade
		return $Query;
	}

}