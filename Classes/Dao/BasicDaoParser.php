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
 * This class is used by the Dao to parse objects to database model objects
 * (transfer objects) and vice versa.
 * All knowledge about the database model is in this class.
 * Extend this class to fit specific needs.
 *
 * Class Tx_Commerce_Dao_BasicDaoParser
 *
 * @author 2005-2008 Carsten Lausen <cl@e-netconsulting.de>
 */
class Tx_Commerce_Dao_BasicDaoParser {
	/**
	 * Constructor
	 *
	 * @return self
	 */
	public function __construct() {
	}

	/**
	 * Parse object to model
	 *
	 * @param Tx_Commerce_Dao_BasicDaoObject $object Object
	 *
	 * @return array
	 */
	public function parseObjectToModel(Tx_Commerce_Dao_BasicDaoObject $object) {
		$model = array();

			// parse attribs
		$propertyNames = array_keys(get_object_vars($object));
		foreach ($propertyNames as $attrib) {
			if ($attrib != 'id') {
				if (method_exists($object, 'get' . ucfirst($attrib))) {
					$model[$attrib] = call_user_func(array($object, 'get' . ucfirst($attrib)), NULL);
				} else {
					$model[$attrib] = $object->$attrib;
				}
			}
		}

		unset ($model['uid']);
		return $model;
	}

	/**
	 * Parse model to object
	 *
	 * @param array $model Model
	 * @param Tx_Commerce_Dao_BasicDaoObject $object Object
	 *
	 * @return void
	 */
	public function parseModelToObject(array $model, Tx_Commerce_Dao_BasicDaoObject &$object) {
			// parse attribs
		$propertyNames = array_keys(get_object_vars($object));
		foreach ($propertyNames as $attrib) {
			if ($attrib != 'id') {
				if (array_key_exists($attrib, $model)) {
					if (method_exists($object, 'set' . ucfirst($attrib))) {
						call_user_func(array($object, 'set' . ucfirst($attrib)), $model[$attrib]);
					} else {
						$object->$attrib = $model[$attrib];
					}
				}
			}
		}
	}

	/**
	 * Setter
	 *
	 * @param array $model Model
	 * @param int $pid Page id
	 *
	 * @return void
	 */
	public function setPid(array &$model, $pid) {
		$model['pid'] = $pid;
	}
}
