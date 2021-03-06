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
 * Database Class for tx_commerce_article_prices. All database calle should
 * be made by this class. In most cases you should use the methodes
 * provided by tx_commerce_article_price to get informations for articles.
 * Inherited from Tx_Commerce_Domain_Repository_Repository
 *
 * Basic abtract Class for Database Query for
 * Database retrival class fro product
 * inherited from Tx_Commerce_Domain_Repository_Repository
 *
 * Class Tx_Commerce_Domain_Repository_ArticlePriceRepository
 *
 * @author 2005-2011 Ingo Schmitt <is@marketing-factory.de>
 */
class Tx_Commerce_Domain_Repository_ArticlePriceRepository extends Tx_Commerce_Domain_Repository_Repository {
	/**
	 * Table concerning the prices
	 *
	 * @var string
	 */
	protected $databaseTable = 'tx_commerce_article_prices';

	/**
	 * Get data
	 *
	 * @param int $uid UID for Data
	 *
	 * @return array assoc Array with data
	 * @todo implement access_check concering category tree
	 * Special Implementation for prices, as they don't have a localisation'
	 */
	public function getData($uid) {
		$uid = (int) $uid;

		$proofSql = '';
		$database = $this->getDatabaseConnection();

		if (is_object($GLOBALS['TSFE']->sys_page)) {
			$proofSql = $this->enableFields($this->databaseTable, $GLOBALS['TSFE']->showHiddenRecords);
		}

		$result = $database->exec_SELECTquery('*', $this->databaseTable, 'uid = ' . $uid . $proofSql);

		// Result should contain only one Dataset
		if ($database->sql_num_rows($result) == 1) {
			$returnData = $database->sql_fetch_assoc($result);
			$database->sql_free_result($result);

			return $returnData;
		}

		$this->error('exec_SELECTquery(\'*\',' . $this->databaseTable . ',\'uid = ' . $uid . '\'); returns no or more than one Result');
		return FALSE;
	}


	/**
	 * Get database connection
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}
}
