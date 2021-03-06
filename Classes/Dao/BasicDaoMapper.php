<?php
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * This class used by the Dao for database storage.
 * It defines how to insert, update, find and delete a transfer object in
 * the database.
 * Extend this class to fit specific needs.
 * This class has no knowledge about the internal design of the model transfer
 * object.
 * Object <-> model (transfer object) mapping and all model design is done by
 * the parser.
 * The class needs a parser for object <-> model (transfer object) mapping.
 *
 * Class Tx_Commerce_Dao_BasicDaoMapper
 *
 * @author 2006-2008 Carsten Lausen <cl@e-netconsulting.de>
 */
class Tx_Commerce_Dao_BasicDaoMapper {
	/**
	 * Table for persistence
	 *
	 * @var null|string
	 */
	protected $dbTable = '';

	/**
	 * Database connection
	 *
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $database;

	/**
	 * Parser
	 *
	 * @var Tx_Commerce_Dao_BasicDaoParser
	 */
	protected $parser;

	/**
	 * Create pid
	 *
	 * @var int
	 */
	protected $createPid = 0;

	/**
	 * Error
	 *
	 * @var array
	 */
	protected $error = array();

	/**
	 * Constructor
	 *
	 * @param Tx_Commerce_Dao_BasicDaoParser $parser Parser
	 * @param int $createPid Create pid
	 * @param string $dbTable Table
	 *
	 * @return self
	 */
	public function __construct(Tx_Commerce_Dao_BasicDaoParser &$parser, $createPid = 0, $dbTable = NULL) {
		$this->init();
		$this->parser = &$parser;
		if (!empty($createPid)) {
			$this->createPid = $createPid;
		}
		if (!empty($dbTable)) {
			$this->dbTable = $dbTable;
		}
	}

	/**
	 * Initialization
	 *
	 * @return void
	 */
	protected function init() {
		$this->database = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Load object
	 *
	 * @param Tx_Commerce_Dao_BasicDaoObject $object Object
	 *
	 * @return void
	 */
	public function load(Tx_Commerce_Dao_BasicDaoObject &$object) {
		if ($object->issetId()) {
			$this->dbSelectById($object->getId(), $object);
		}
	}

	/**
	 * Save object
	 *
	 * @param Tx_Commerce_Dao_BasicDaoObject $object Object
	 *
	 * @return void
	 */
	public function save(Tx_Commerce_Dao_BasicDaoObject &$object) {
		if ($object->issetId()) {
			$this->dbUpdate($object->getId(), $object);
		} else {
			$this->dbInsert($object);
		}
	}

	/**
	 * Remove object
	 *
	 * @param Tx_Commerce_Dao_BasicDaoObject $object Object
	 *
	 * @return void
	 */
	public function remove(Tx_Commerce_Dao_BasicDaoObject &$object) {
		if ($object->issetId()) {
			$this->dbDelete($object->getId(), $object);
		}
	}

	/**
	 * Db add object
	 *
	 * @param Tx_Commerce_Dao_BasicDaoObject $object Object
	 *
	 * @return void
	 */
	protected function dbInsert(Tx_Commerce_Dao_BasicDaoObject &$object) {
		$dbTable = $this->dbTable;
		$dbModel = $this->parser->parseObjectToModel($object);

		// set pid
		$this->parser->setPid($dbModel, $this->createPid);

		// execute query
		$this->database->exec_INSERTquery($dbTable, $dbModel);

		// any errors
		$error = $this->database->sql_error();
		if (!empty($error)) {
			$this->addError(array(
				$error,
				$this->database->INSERTquery($dbTable, $dbModel),
				'$dbModel' => $dbModel
			));
		}

		// set object id
		$object->setId($this->database->sql_insert_id());
	}

	/**
	 * Db update object
	 *
	 * @param int $uid Uid
	 * @param Tx_Commerce_Dao_BasicDaoObject $object Object
	 *
	 * @return void
	 */
	protected function dbUpdate($uid, Tx_Commerce_Dao_BasicDaoObject &$object) {
		$dbTable = $this->dbTable;
		$dbWhere = 'uid = ' . (int) $uid;
		$dbModel = $this->parser->parseObjectToModel($object);

		// execute query
		$this->database->exec_UPDATEquery($dbTable, $dbWhere, $dbModel);

		// any errors
		$error = $this->database->sql_error();
		if (!empty($error)) {
			$this->addError(array(
				$error,
				$this->database->UPDATEquery($dbTable, $dbWhere, $dbModel),
				'$dbModel' => $dbModel
			));
		}
	}

	/**
	 * Db delete object
	 *
	 * @param int $uid Uid
	 * @param Tx_Commerce_Dao_BasicDaoObject $object Object
	 *
	 * @return void
	 */
	protected function dbDelete($uid, Tx_Commerce_Dao_BasicDaoObject &$object) {
		$dbWhere = 'uid = ' . (int) $uid;

		// execute query
		$this->database->exec_DELETEquery($this->dbTable, $dbWhere);

		// any errors
		$error = $this->database->sql_error();
		if (!empty($error)) {
			$this->addError(array(
				$error,
				$this->database->DELETEquery($this->dbTable, $dbWhere)
			));
		}

		// remove object itself
		$object->destroy();
	}

	/**
	 * DB select object by id
	 *
	 * @param int $uid Uid
	 * @param Tx_Commerce_Dao_BasicDaoObject $object Object
	 *
	 * @return void
	 */
	protected function dbSelectById($uid, Tx_Commerce_Dao_BasicDaoObject &$object) {
		$dbFields = '*';
		$dbTable = $this->dbTable;
		$dbWhere = 'uid = ' . (int) $uid;
		$dbWhere .= 'AND deleted = 0';

		// execute query
		$res = $this->database->exec_SELECTquery($dbFields, $dbTable, $dbWhere);

		// insert into object
		$model = $this->database->sql_fetch_assoc($res);
		if ($model) {
			// parse into object
			$this->parser->parseModelToObject($model, $object);
		} else {
			// no object found, empty obj and id
			$object->clear();
		}

		// free results
		$this->database->sql_free_result($res);
	}

	/**
	 * Add error message
	 *
	 * @param array $error Error
	 *
	 * @return void
	 */
	protected function addError(array $error) {
		$this->error[] = $error;
	}

	/**
	 * Check if error was raised
	 *
	 * @return bool
	 */
	protected function isError() {
		return !empty($this->error);
	}

	/**
	 * Get error
	 *
	 * @return array|bool
	 */
	protected function getError() {
		return $this->error ?: FALSE;
	}
}
