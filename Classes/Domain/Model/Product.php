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
 * Basic class for handling products
 * Libary for Frontend-Rendering of products. This class
 * should be used for all Frontend renderings. No database calls
 * to the commerce tables should be made directly.
 * This Class is inhertited from Tx_Commerce_Domain_Model_AbstractEntity, all
 * basic database calls are made from a separate database Class
 * Do not acces class variables directly, allways use the get and set methods,
 * variables will be changed in php5 to private
 *
 * Class Tx_Commerce_Domain_Model_Product
 *
 * @author 2005-2012 Ingo Schmitt <is@marketing-factory.de>
 */
class Tx_Commerce_Domain_Model_Product extends Tx_Commerce_Domain_Model_AbstractEntity {
	/**
	 * Database class name
	 *
	 * @var string
	 */
	protected $databaseClass = 'Tx_Commerce_Domain_Repository_ProductRepository';

	/**
	 * Database connection
	 *
	 * @var Tx_Commerce_Domain_Repository_ProductRepository
	 */
	public $databaseConnection;

	/**
	 * Field list
	 *
	 * @var array
	 */
	protected $fieldlist = array(
		'uid',
		'pid',
		'title',
		'subtitle',
		'description',
		'images',
		'teaser',
		'teaserimages',
		'relatedpage',
		'l18n_parent',
		'manufacturer_uid',
		't3ver_oid',
		't3ver_id',
		't3ver_label',
		't3ver_wsid',
		't3ver_stage',
		't3ver_state',
		't3ver_tstamp'
	);

	/* Data Variables */

	/**
	 * Title
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * Pid
	 *
	 * @var int
	 */
	public $pid = 0;

	/**
	 * Subtitle
	 *
	 * @var string
	 */
	protected $subtitle = '';

	/**
	 * Description
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * Teaser
	 *
	 * @var string
	 */
	public $teaser = '';

	/**
	 * Teaser images
	 *
	 * @var string
	 */
	public $teaserimages = '';

	/**
	 * Teaser images as array
	 *
	 * @var array
	 */
	protected $teaserImagesArray = array();

	/**
	 * Images
	 *
	 * @var string
	 */
	protected $images = '';

	/**
	 * Images as array
	 *
	 * @var array
	 */
	protected $images_array = array();

	/**
	 * Array of child articles
	 *
	 * @var array
	 */
	protected $articles = array();

	/**
	 * Array of article uids
	 *
	 * @var array
	 */
	protected $articles_uids = array();

	/**
	 * If articles are loaded, so load articles can simply
	 * return with the values from the object
	 *
	 * @var bool
	 */
	protected $articlesLoaded = FALSE;

	/**
	 * Attributes
	 *
	 * @var array
	 */
	public $attributes = array();

	/**
	 * Attribute uids
	 *
	 * @var array
	 */
	public $attributes_uids = array();

	/**
	 * Related page
	 *
	 * @var string
	 */
	public $relatedpage = '';

	/**
	 * Related products
	 *
	 * @var array
	 */
	public $relatedProducts = array();

	/**
	 * Related product uids
	 *
	 * @var array
	 */
	public $relatedProduct_uids = array();

	/**
	 * Related products loaded
	 *
	 * @var bool
	 */
	public $relatedProducts_loaded = FALSE;

	/**
	 * Maximum Articles to render for this product. Normally PHP_INT_MAX
	 *
	 * @var int
	 */
	public $renderMaxArticles = PHP_INT_MAX;

	/* Versioning */

	/**
	 * Version oid
	 *
	 * @var int
	 */
	public $t3ver_oid = 0;

	/**
	 * Version id
	 *
	 * @var int
	 */
	public $t3ver_id = 0;

	/**
	 * Version label
	 *
	 * @var string
	 */
	public $t3ver_label = '';

	/**
	 * Version workspace id
	 *
	 * @var int
	 */
	public $t3ver_wsid = 0;

	/**
	 * Version state
	 *
	 * @var int
	 */
	public $t3ver_state = 0;

	/**
	 * Version stage
	 *
	 * @var int
	 */
	public $t3ver_stage = 0;

	/**
	 * Version timestamp
	 *
	 * @var int
	 */
	public $t3ver_tstamp = 0;

	/**
	 * Constructor, basically calls init
	 *
	 * @param int $uid Product uid
	 * @param int $languageUid Language uid
	 *
	 * @return self
	 */
	public function __construct($uid, $languageUid = 0) {
		if ((int) $uid) {
			$this->init($uid, $languageUid);
		}
	}

	/**
	 * Class initialization
	 *
	 * @param int $uid Uid of product
	 * @param int $langUid Language uid, default 0
	 *
	 * @return bool TRUE if initialization was successful
	 */
	public function init($uid, $langUid = 0) {
		$uid = (int) $uid;
		$langUid = (int) $langUid;

		if ($uid > 0) {
			$this->uid = $uid;
			$this->lang_uid = $langUid;
			$this->databaseConnection = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($this->databaseClass);

			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['commerce/lib/class.tx_commerce_product.php']['postinit'])) {
				\TYPO3\CMS\Core\Utility\GeneralUtility::deprecationLog('
					hook
					$GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'commerce/lib/class.tx_commerce_product.php\'][\'postinit\']
					is deprecated since commerce 1.0.0, it will be removed in commerce 1.4.0, please use instead
					$GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'commerce/Classes/Domain/Model/Product.php\'][\'postinit\']
				');
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['commerce/lib/class.tx_commerce_product.php']['postinit'] as $classRef) {
					$hookObj = \TYPO3\CMS\Core\Utility\GeneralUtility::getUserObj($classRef);
					if (method_exists($hookObj, 'postinit')) {
						$hookObj->postinit($this);
					}
				}
			}
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['commerce/Classes/Domain/Model/Product.php']['postinit'])) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['commerce/Classes/Domain/Model/Product.php']['postinit'] as $classRef) {
					$hookObj = \TYPO3\CMS\Core\Utility\GeneralUtility::getUserObj($classRef);
					if (method_exists($hookObj, 'postinit')) {
						$hookObj->postinit($this);
					}
				}
			}

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Get list of article uids
	 *
	 * @param int $uid Article uid
	 *
	 * @return Tx_Commerce_Domain_Model_Article Article uids
	 */
	public function getArticle($uid) {
		return $this->articles[$uid];
	}

	/**
	 * Get list of articles of this product filtered by given attribute UID
	 * and attribute value
	 *
	 * @param int $attributeUid Attribute uid
	 * @param mixed $attributeValue Attribute value
	 *
	 * @return array of article uids Article uids
	 */
	public function getArticlesByAttribute($attributeUid, $attributeValue) {
		return $this->getArticlesByAttributeArray(
			array(
				array(
					'AttributeUid' => $attributeUid,
					'AttributeValue' => $attributeValue
				)
			)
		);
	}

	/**
	 * Get list of articles of this product filtered by given attribute UID
	 * and attribute value
	 *
	 * @param array $attributes Attributes array(
	 *		array('AttributeUid'=>$attributeUID, 'AttributeValue'=>$attributeValue),
	 *		array('AttributeUid'=>$attributeUID, 'AttributeValue'=>$attributeValue),
	 *		...
	 *	)
	 * @param bool|int $proofUid Proof if script is running without instance
	 *	and so without a single product
	 *
	 * @return array of article uids
	 */
	public function getArticlesByAttributeArray(array $attributes, $proofUid = 1) {
		$whereUid = $proofUid ? ' and tx_commerce_articles.uid_product = ' . $this->uid : '';

		$first = 1;
		if (is_array($attributes)) {
			$database = $this->getDatabaseConnection();
			$attributeUids = array();
			foreach ($attributes as $uidValuePair) {
				// Initialize arrays to prevent warningn in array_intersect()
				$next = array();
				$addwheretmp = '';

				// attribute char is not used, thats why we check for id
				if (is_string($uidValuePair['AttributeValue'])) {
					$addwheretmp .= ' OR (tx_commerce_attributes.uid = ' . (int) $uidValuePair['AttributeUid'] .
									' AND tx_commerce_articles_article_attributes_mm.value_char="' .
									$database->quoteStr($uidValuePair['AttributeValue'], 'tx_commerce_articles_article_attributes_mm') . '" )';
				}

				// Nach dem charwert immer ueberpruefen, solange value_char noch nicht drin ist.
				if (is_float($uidValuePair['AttributeValue']) || (int) $uidValuePair['AttributeValue']) {
					$addwheretmp .= ' OR (tx_commerce_attributes.uid = ' . (int) $uidValuePair['AttributeUid'] .
									' AND tx_commerce_articles_article_attributes_mm.default_value in ("' .
									$database->quoteStr($uidValuePair['AttributeValue'], 'tx_commerce_articles_article_attributes_mm') . '" ) )';
				}

				if (is_float($uidValuePair['AttributeValue']) || (int) $uidValuePair['AttributeValue']) {
					$addwheretmp .= ' OR (tx_commerce_attributes.uid = ' . (int) $uidValuePair['AttributeUid'] .
									' AND tx_commerce_articles_article_attributes_mm.uid_valuelist in ("' .
									$database->quoteStr($uidValuePair['AttributeValue'], 'tx_commerce_articles_article_attributes_mm') . '") )';
				}

				$addwhere = ' AND (0 ' . $addwheretmp . ') ';

				$result = $database->exec_SELECT_mm_query(
					'distinct tx_commerce_articles.uid', 'tx_commerce_articles', 'tx_commerce_articles_article_attributes_mm',
					'tx_commerce_attributes',
					$addwhere . ' AND tx_commerce_articles.hidden = 0 and tx_commerce_articles.deleted = 0' . $whereUid
				);

				if (($result) && ($database->sql_num_rows($result) > 0)) {
					while (($data = $database->sql_fetch_assoc($result))) {
						$next[] = $data['uid'];
					}
					$database->sql_free_result($result);
				}

				// Return only the first article that exists in all arrays that's why the
				// first array get set and then array intersect checks the matching
				if ($first) {
					$attributeUids = $next;
					$first = 0;
				} else {
					$attributeUids = array_intersect($attributeUids, $next);
				}
			}

			if (count($attributeUids) > 0) {
				sort($attributeUids);

				return $attributeUids;
			}
		}

		return array();
	}

	/**
	 * Returns list of articles (from this product) filtered by price
	 *
	 * @param int $priceMin Smallest unit (e.g. cents)
	 * @param int $priceMax Biggest unit (e.g. cents)
	 * @param bool|int $usePriceGrossInstead Normally we check for net price,
	 *        switch to gross price
	 * @param bool|int $proofUid If script is running without instance and
	 *        so without a single product
	 *
	 * @return array of article uids
	 */
	public function getArticlesByPrice($priceMin = 0, $priceMax = 0, $usePriceGrossInstead = 0, $proofUid = 1) {
		// first get all real articles, then create objects and check prices do not
		// get prices directly from DB because we need to take (price) hooks into
		// account
		$table = 'tx_commerce_articles';
		$where = '1=1';
		if ($proofUid) {
			$where .= ' and tx_commerce_articles.uid_product = ' . $this->uid;
		}

		$where .= ' and article_type_uid=1';
		$where .= $GLOBALS['TSFE']->sys_page->enableFields($table, $GLOBALS['TSFE']->showHiddenRecords);
		$groupBy = '';
		$orderBy = 'sorting';
		$limit = '';

		$database = $this->getDatabaseConnection();

		$rows = $database->exec_SELECTgetRows('uid', $table, $where, $groupBy, $orderBy, $limit);
		$rawArticleUidList = array();
		foreach ($rows as $row) {
			$rawArticleUidList[] = $row['uid'];
		}

		// Run price test
		$articleUidList = array();
		foreach ($rawArticleUidList as $rawArticleUid) {
			/**
			 * Article
			 *
			 * @var Tx_Commerce_Domain_Model_Article $article
			 */
			$article = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
				'Tx_Commerce_Domain_Model_Article',
				$rawArticleUid,
				$this->lang_uid
			);
			$article->loadData();
			$myPrice = $usePriceGrossInstead ? $article->getPriceGross() : $article->getPriceNet();
			if (($priceMin <= $myPrice) && ($myPrice <= $priceMax)) {
				$articleUidList[] = $article->getUid();
			}
		}

		return count($articleUidList) ? $articleUidList : array();
	}

	/**
	 * Get number of articles of this product
	 *
	 * @return int Number of articles
	 */
	public function getArticlesCount() {
		return count($this->articles);
	}

	/**
	 * Get list of article objects
	 *
	 * @return array Article objects
	 */
	public function getArticleObjects() {
		return $this->articles;
	}

	/**
	 * Get list of article uids
	 *
	 * @param int $index Index
	 *
	 * @return int Article uid
	 */
	public function getArticleUid($index) {
		return $this->articles_uids[$index];
	}

	/**
	 * Get list of article uids
	 *
	 * @return array Article uids
	 */
	public function getArticleUids() {
		return $this->articles_uids;
	}

	/**
	 * Get attribute matrix of products and articles
	 * Both products and articles have a mm relation to the attribute table
	 * This method gets the attributes of a product or an article and compiles them
	 * to an unified array of attributes
	 * This method handles the different types of values of an attribute: character
	 * values, int values and value lists
	 *
	 * @param mixed $articleList Array of restricted product articles
	 *	(usually shall, must, ...), FALSE for all, FALSE for product attribute list
	 * @param mixed $attributeListInclude Array of restricted attributes,
	 *	FALSE for all
	 * @param bool $valueListShowValueInArticleProduct TRUE if 'showvalue' field
	 *	of value list table should be cared of
	 * @param string $sortingTable Name of table with sorting field of table to order
	 *	records
	 * @param bool $localizationAttributeValuesFallbackToDefault TRUE if a
	 *	fallback to default value should be done if a localization of an attribute
	 *	value or value char is not available in localized row
	 * @param string $parentTable Name of parent table
	 *
	 * @return mixed Array if attributes where found, else FALSE
	 */
	public function getAttributeMatrix($articleList = FALSE, $attributeListInclude = FALSE,
		$valueListShowValueInArticleProduct = TRUE, $sortingTable = 'tx_commerce_articles_article_attributes_mm',
		$localizationAttributeValuesFallbackToDefault = FALSE, $parentTable = 'tx_commerce_articles'
	) {
		$database = $this->getDatabaseConnection();

		// Early return if no product is given
		if (!$this->uid > 0) {
			return FALSE;
		}

		if ($parentTable == 'tx_commerce_articles') {
			// mm table for article->attribute
			$mmTable = 'tx_commerce_articles_article_attributes_mm';
		} else {
			// mm table for product->attribute
			$mmTable = 'tx_commerce_products_attributes_mm';
		}

		// Execute main query
		$attributeDataArrayRessource = $database->sql_query(
			$this->getAttributeMatrixQuery($parentTable, $mmTable, $sortingTable, $articleList, $attributeListInclude)
		);

		// Accumulated result array
		$targetDataArray = array();

		// Attributes uids are added to this array if there is no language overlay for
		// an attribute to prevent fetching of non-existing language overlays in
		// subsequent rows for the same attribute
		$attributeLanguageOverlayBlacklist = array();

		// Compile target data array
		while (($attributeDataRow = $database->sql_fetch_assoc($attributeDataArrayRessource))) {
			// AttributeUid affected by this reord
			$currentAttributeUid = $attributeDataRow['attributes_uid'];

			// Don't handle this row if a prior row was already unable to fetch a language
			// overlay of the attribute
			if (
				$this->lang_uid > 0
				&& count(array_intersect(array($currentAttributeUid), $attributeLanguageOverlayBlacklist))
			) {
				continue;
			}

			// Initialize array for this attribute uid and fetch attribute language overlay
			// for localization
			if (!isset($targetDataArray[$currentAttributeUid])) {
				// Initialize target row and fill in attribute values
				$targetDataArray[$currentAttributeUid]['title'] = $attributeDataRow['attributes_title'];
				$targetDataArray[$currentAttributeUid]['unit'] = $attributeDataRow['attributes_unit'];
				$targetDataArray[$currentAttributeUid]['values'] = array();
				$targetDataArray[$currentAttributeUid]['valueuidlist'] = array();
				$targetDataArray[$currentAttributeUid]['valueformat'] = $attributeDataRow['attributes_valueformat'];
				$targetDataArray[$currentAttributeUid]['Internal_title'] = $attributeDataRow['attributes_internal_title'];
				$targetDataArray[$currentAttributeUid]['icon'] = $attributeDataRow['attributes_icon'];

				// Fetch language overlay of attribute if given
				// Overwrite title, unit and Internal_title (sic!) of attribute
				if ($this->lang_uid > 0) {
					$overwriteValues = array();
					$overwriteValues['uid'] = $currentAttributeUid;
					$overwriteValues['pid'] = $attributeDataRow['attributes_pid'];
					$overwriteValues['sys_language_uid'] = $attributeDataRow['attritubes_sys_language_uid'];
					$overwriteValues['title'] = $attributeDataRow['attributes_title'];
					$overwriteValues['unit'] = $attributeDataRow['attributes_unit'];
					$overwriteValues['internal_title'] = $attributeDataRow['attributes_internal_title'];

					$languageOverlayRecord = $this->getFrontendController()->sys_page->getRecordOverlay(
						'tx_commerce_attributes', $overwriteValues, $this->lang_uid, $this->translationMode
					);
					if ($languageOverlayRecord) {
						$targetDataArray[$currentAttributeUid]['title'] = $languageOverlayRecord['title'];
						$targetDataArray[$currentAttributeUid]['unit'] = $languageOverlayRecord['unit'];
						$targetDataArray[$currentAttributeUid]['Internal_title'] = $languageOverlayRecord['internal_title'];
					} else {
						// Throw away array if there is no lang overlay, add to blacklist
						unset($targetDataArray[$currentAttributeUid]);
						$attributeLanguageOverlayBlacklist[] = $currentAttributeUid;
						continue;
					}
				}
			}

			// There is a nasty difference between article and product attributes regarding
			// default_value field:
			// For attributes: default_value must be an int value and string values are
			// stored in value_char
			// For products: Everything is stored in default_value
			$defaultValue = FALSE;
			if ($parentTable == 'tx_commerce_articles') {
				if ($attributeDataRow['default_value'] > 0) {
					$defaultValue = TRUE;
				}
			} else {
				if (strlen($attributeDataRow['default_value']) > 0) {
					$defaultValue = TRUE;
				}
			}

			// Handle value, default_value and value lists of attributes
			if (strlen($attributeDataRow['value_char']) || $defaultValue) {
				// Localization of value_char
				if ($this->lang_uid > 0) {
					// Get uid of localized article
					// (lang_uid = selected lang and l18n_parent = current article)
					$localizedArticleUid = $database->exec_SELECTgetRows(
						'uid', $parentTable,
						'l18n_parent=' . $attributeDataRow['parent_uid'] . ' AND sys_language_uid=' . $this->lang_uid .
						$this->getFrontendController()->sys_page->enableFields($parentTable, $GLOBALS['TSFE']->showHiddenRecords)
					);

					// Fetch the article-attribute mm record with localized article uid
					// and current attribute
					$localizedArticleUid = (int) $localizedArticleUid[0]['uid'];
					if ($localizedArticleUid > 0) {
						$selectFields = array();
						$selectFields[] = 'default_value';
						// Again difference between product->attribute and article->attribute
						if ($parentTable == 'tx_commerce_articles') {
							$selectFields[] = 'value_char';
						}
						// Fetch mm record with overlay values
						$localizedArticleAttributeValues = $database->exec_SELECTgetRows(
							implode(', ', $selectFields), $mmTable,
							'uid_local=' . $localizedArticleUid . ' AND uid_foreign=' . $currentAttributeUid
						);
						// Use value_char if set, else check for default_value, else use non
						// localized value if enabled fallback
						if (strlen($localizedArticleAttributeValues[0]['value_char']) > 0) {
							$targetDataArray[$currentAttributeUid]['values'][] = $localizedArticleAttributeValues[0]['value_char'];
						} elseif (strlen($localizedArticleAttributeValues[0]['default_value']) > 0) {
							$targetDataArray[$currentAttributeUid]['values'][] = $localizedArticleAttributeValues[0]['default_value'];
						} elseif ($localizationAttributeValuesFallbackToDefault) {
							$targetDataArray[$currentAttributeUid]['values'][] = $attributeDataRow['value_char'];
						}
					}
				} else {
					// Use value_char if set, else default_value
					if (strlen($attributeDataRow['value_char']) > 0) {
						$targetDataArray[$currentAttributeUid]['values'][] = $attributeDataRow['value_char'];
					} else {
						$targetDataArray[$currentAttributeUid]['values'][] = $attributeDataRow['default_value'];
					}
				}
			} elseif ($attributeDataRow['uid_valuelist']) {
				// Get value list rows
				$valueListArrayRows = $database->exec_SELECTgetRows(
					'*', 'tx_commerce_attribute_values', 'uid IN (' . $attributeDataRow['uid_valuelist'] . ')'
				);
				foreach ($valueListArrayRows as $valueListArrayRow) {
					// Ignore row if this value list has already been calculated
					// This might happen if method is called with multiple article uid's
					if (count(
						array_intersect(
							array($valueListArrayRow['uid']), $targetDataArray[$currentAttributeUid]['valueuidlist']
						)
					)
					) {
						continue;
					}

					// Value lists must be localized.
					// So overwrite current row with localization record
					if ($this->lang_uid > 0) {
						$valueListArrayRow = $GLOBALS['TSFE']->sys_page->getRecordOverlay(
							'tx_commerce_attribute_values', $valueListArrayRow, $this->lang_uid, $this->translationMode
						);
					}
					if (!$valueListArrayRow) {
						continue;
					}

					// Add value list row to target array
					if ($valueListShowValueInArticleProduct || $valueListArrayRow['showvalue'] == 1) {
						$targetDataArray[$currentAttributeUid]['values'][] = $valueListArrayRow;
						$targetDataArray[$currentAttributeUid]['valueuidlist'][] = $valueListArrayRow['uid'];
					}
				}
			}
		}

		// Free resources of main query
		$database->sql_free_result($attributeDataArrayRessource);

		// Return "I didn't found anything, so I'm not an array"
		// This hack is a re-implementation of the original matrix behaviour
		if (count($targetDataArray) == 0) {
			return FALSE;
		}

		// Sort value lists by sorting value
		foreach ($targetDataArray as $attributeUid => $attributeValues) {
			if (count($attributeValues['valueuidlist']) > 1) {
				// compareBySorting is a special callback function to order
				// the array by its sorting value
				usort(
					$targetDataArray[$attributeUid]['values'], array(
						'tx_commerce_product',
						'compareBySorting'
					)
				);

				// Sort valuelist as well to get deterministic array output
				sort($attributeValues['valueuidlist']);
				$targetDataArray[$attributeUid]['valueuidlist'] = $attributeValues['valueuidlist'];
			}
		}

		return $targetDataArray;
	}

	/**
	 * Create query to get all attributes of articles or products
	 * This is a join over three tables:
	 *        parent table, either tx_commerce_articles or tx_commerce_producs
	 *        corresponding mm table
	 *        tx_commerce_attributes
	 *
	 * @param string $parentTable Name of the parent table,
	 *        either tx_commerce_articles or tx_commerce_products
	 * @param string $mmTable Name of the mm table,
	 *        either tx_commerce_articles_article_attributes_mm
	 *        or tx_commerce_products_attributes_mm
	 * @param string $sortingTable Name of table with .sorting field to order records
	 * @param mixed $articleList Array of some restricted articles of this product
	 *        (shall, must, ...), FALSE for all articles of product,
	 *        FALSE if $parentTable = tx_commerce_products
	 * @param mixed $attributeList Array of restricted attributes,
	 *        FALSE for all attributes
	 *
	 * @return string Query to be executed
	 */
	protected function getAttributeMatrixQuery($parentTable = 'tx_commerce_articles',
		$mmTable = 'tx_commerce_articles_article_attributes_mm', $sortingTable = 'tx_commerce_articles_article_attributes_mm',
		$articleList = FALSE, $attributeList = FALSE
	) {
		$database = $this->getDatabaseConnection();

		$selectFields = array();
		$selectWhere = array();

		// Distinguish differences between product->attribute
		// and article->attribute query
		if ($parentTable == 'tx_commerce_articles') {
			// Load full article list of product if not given
			if ($articleList === FALSE) {
				$articleList = $this->loadArticles();
			}
			// Get article attributes of current product only
			$selectWhere[] = $parentTable . '.uid_product = ' . $this->uid;
			// value_char is only available in article->attribute mm table
			$selectFields[] = $mmTable . '.value_char';
			// Restrict article list if given
			if (is_array($articleList) && count($articleList) > 0) {
				$selectWhere[] = $parentTable . '.uid IN (' . implode(',', $articleList) . ')';
			}
		} else {
			// Get attributes of current product only
			$selectWhere[] = $parentTable . '.uid = ' . $this->uid;
		}

		$selectFields[] = $parentTable . '.uid AS parent_uid';
		$selectFields[] = 'tx_commerce_attributes.uid AS attributes_uid';
		$selectFields[] = 'tx_commerce_attributes.pid AS attributes_pid';
		$selectFields[] = 'tx_commerce_attributes.sys_language_uid AS attributes_sys_language_uid';
		$selectFields[] = 'tx_commerce_attributes.title AS attributes_title';
		$selectFields[] = 'tx_commerce_attributes.unit AS attributes_unit';
		$selectFields[] = 'tx_commerce_attributes.valueformat AS attributes_valueformat';
		$selectFields[] = 'tx_commerce_attributes.internal_title AS attributes_internal_title';
		$selectFields[] = 'tx_commerce_attributes.icon AS attributes_icon';
		$selectFields[] = $mmTable . '.default_value';
		$selectFields[] = $mmTable . '.uid_valuelist';
		$selectFields[] = $sortingTable . '.sorting';

		$selectFrom = array();
		$selectFrom[] = $parentTable;
		$selectFrom[] = $mmTable;
		$selectFrom[] = 'tx_commerce_attributes';

		// mm join restriction
		$selectWhere[] = $parentTable . '.uid = ' . $mmTable . '.uid_local';
		$selectWhere[] = 'tx_commerce_attributes.uid = ' . $mmTable . '.uid_foreign';

		// Restrict attribute list if given
		if (is_array($attributeList) && !empty($attributeList)) {
			$selectWhere[] = 'tx_commerce_attributes.uid IN (' . implode(',', $attributeList) . ')';
		}

		// Get enabled rows only
		$selectWhere[] = ' 1 ' . $GLOBALS['TSFE']->sys_page->enableFields(
				'tx_commerce_attributes', $GLOBALS['TSFE']->showHiddenRecords
			);
		$selectWhere[] = ' 1 ' . $GLOBALS['TSFE']->sys_page->enableFields(
				$parentTable, $GLOBALS['TSFE']->showHiddenRecords
			);

		// Order rows by given sorting table
		$selectOrder = $sortingTable . '.sorting';

		// Compile query
		$attributeMmQuery = $database->SELECTquery(
			'DISTINCT ' . implode(', ', $selectFields), implode(', ', $selectFrom), implode(' AND ', $selectWhere), '',
			$selectOrder
		);

		return ($attributeMmQuery);
	}

	/**
	 * Evaluates the cheapest article for current product by gross price
	 *
	 * @param int $usePriceNet If true, Compare prices by net instead of gross
	 *
	 * @return int|bool article id, FALSE if no article
	 */
	public function getCheapestArticle($usePriceNet = 0) {
		$this->loadArticles();
		if (!is_array($this->articles_uids) || !count($this->articles_uids)) {
			return FALSE;
		}

		$priceArr = array();
		$articleCount = count($this->articles_uids);
		for ($j = 0; $j < $articleCount; $j++) {
			$article = & $this->articles[$this->articles_uids[$j]];
			if (is_object($article) && $article instanceof Tx_Commerce_Domain_Model_Article) {
				$priceArr[$article->getUid()] = $usePriceNet ? $article->getPriceNet() : $article->getPriceGross();
			}
		}
		asort($priceArr);
		reset($priceArr);
		return current(array_keys($priceArr));
	}

	/**
	 * Return Product description
	 *
	 * @return string Product description
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Returns an Array of Images
	 *
	 * @return array Images of this product
	 */
	public function getImages() {
		return $this->images_array;
	}

	/**
	 * Get l18n overlays of this product
	 *
	 * @return array l18n overlay objects
	 */
	public function getL18nProducts() {
		$languageParentUid = $this->databaseConnection->getL18nProducts($this->uid);

		return $languageParentUid;
	}

	/**
	 * Get manufacturer title
	 *
	 * @return string manufacturer title
	 */
	public function getManufacturerTitle() {
		$result = '';

		if ($this->getManufacturerUid()) {
			$result = $this->databaseConnection->getManufacturerTitle($this->getManufacturerUid());
		}

		return $result;
	}

	/**
	 * Get manufacturer UID of the product if set
	 *
	 * @return int UID of manufacturer
	 */
	public function getManufacturerUid() {
		if (isset($this->manufacturer_uid)) {
			return $this->manufacturer_uid;
		}

		return FALSE;
	}

	/**
	 * Get category master parent category
	 *
	 * @return array uid of category
	 */
	public function getMasterparentCategory() {
		return $this->databaseConnection->getMasterParentCategory($this->uid);
	}

	/**
	 * Get all parent categories
	 *
	 * @return array Parent categories of product
	 */
	public function getParentCategories() {
		return $this->databaseConnection->getParentCategories($this->uid);
	}

	/**
	 * Return product pid
	 *
	 * @return int Product pid
	 */
	public function getPid() {
		return $this->pid;
	}

	/**
	 * Returns the related page of the product
	 *
	 * @return int related page
	 */
	public function getRelatedPage() {
		return $this->relatedpage;
	}

	/**
	 * Get related products
	 *
	 * @return array related products
	 */
	public function getRelatedProducts() {
		if (!$this->relatedProducts_loaded) {
			$this->relatedProduct_uids = $this->databaseConnection->getRelatedProductUids($this->uid);
			if (count($this->relatedProduct_uids)) {
				foreach (array_keys($this->relatedProduct_uids) as $productId) {
					/**
					 * Product
					 *
					 * @var Tx_Commerce_Domain_Model_Product $product
					 */
					$product = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
						'Tx_Commerce_Domain_Model_Product',
						$productId,
						$this->lang_uid
					);
					$product->loadData();
					$product->loadArticles();

					// Check if the user is allowed to access the product and if the product
					// has at least one article
					if ($product->isAccessible() && $product->getArticlesCount()) {
						$this->relatedProducts[] = $product;
					}
				}
			}
			$this->relatedProducts_loaded = TRUE;
		}

		return $this->relatedProducts;
	}

	/**
	 * Sets renderMaxArticles Value in the Object
	 *
	 * @param int $count New Value
	 *
	 * @return void
	 */
	public function setRenderMaxArticles($count) {
		$this->renderMaxArticles = (int) $count;
	}

	/**
	 * Get renderMaxArticles Value in the Object
	 *
	 * @return int RenderMaxArticles
	 */
	public function getRenderMaxArticles() {
		return $this->renderMaxArticles;
	}

	/**
	 * Generates a Matrix from these concerning articles for all attributes
	 * and the values therefor
	 *
	 * @param mixed $articleList Uids of articles or FALSE
	 * @param mixed $attributesToInclude Array of attribute uids to include
	 *        or FALSE for all attributes
	 * @param bool $showHiddenValues Wether or net hidden values should be shown
	 * @param string $sortingTable Default order by of attributes
	 *
	 * @return bool|array
	 */
	public function getSelectAttributeMatrix($articleList = FALSE, $attributesToInclude = FALSE, $showHiddenValues = TRUE,
		$sortingTable = 'tx_commerce_articles_article_attributes_mm'
	) {
		$return = array();

		// If no list is given, take complate arctile-list from product
		if ($this->uid > 0) {
			if ($articleList == FALSE) {
				$articleList = $this->loadArticles();
			}

			$addwhere = '';
			if (is_array($attributesToInclude)) {
				if (!is_null($attributesToInclude[0])) {
					$addwhere .= ' AND tx_commerce_attributes.uid in (' . implode(',', $attributesToInclude) . ')';
				}
			}

			$addwhere2 = '';
			if (is_array($articleList) && count($articleList) > 0) {
				$queryArticleList = implode(',', $articleList);
				$addwhere2 = ' AND tx_commerce_articles.uid in (' . $queryArticleList . ')';
			}

			$database = $this->getDatabaseConnection();
			$result = $database->exec_SELECT_mm_query(
				'distinct tx_commerce_attributes.uid, tx_commerce_attributes.sys_language_uid, tx_commerce_articles.uid as article,
					tx_commerce_attributes.title, tx_commerce_attributes.unit, tx_commerce_attributes.valueformat,
					tx_commerce_attributes.internal_title, tx_commerce_attributes.icon, tx_commerce_attributes.iconmode,
					' . $sortingTable . '.sorting', 'tx_commerce_articles', 'tx_commerce_articles_article_attributes_mm',
				'tx_commerce_attributes',
				' AND tx_commerce_articles.uid_product = ' . $this->uid . ' ' . $addwhere . $addwhere2 . ' order by ' .
				$sortingTable . '.sorting'
			);

			$addwhere = $addwhere2;

			if (($result) && ($database->sql_num_rows($result) > 0)) {
				while (($data = $database->sql_fetch_assoc($result))) {
					// Language overlay
					if ($this->lang_uid > 0) {
						$proofSql = '';
						if (is_object($GLOBALS['TSFE']->sys_page)) {
							$proofSql = $GLOBALS['TSFE']->sys_page->enableFields(
								'tx_commerce_attributes', $GLOBALS['TSFE']->showHiddenRecords
							);
						}
						$attributeResult = $database->exec_SELECTquery(
							'*', 'tx_commerce_attributes', 'uid = ' . $data['uid'] . ' ' . $proofSql
						);

						// Result should contain only one Dataset
						if ($database->sql_num_rows($attributeResult) == 1) {
							$attributeData = $database->sql_fetch_assoc($attributeResult);
							$attributeData = $GLOBALS['TSFE']->sys_page->getRecordOverlay(
								'tx_commerce_attributes', $attributeData, $this->lang_uid, $this->translationMode
							);

							if (!is_array($attributeData)) {
								// No Translation possible, so next interation
								continue;
							}

							$data['title'] = $attributeData['title'];
							$data['unit'] = $attributeData['unit'];
							$data['internal_title'] = $attributeData['internal_title'];
						}

						$database->sql_free_result($attributeResult);
					}

					$valueshown = FALSE;

					// Only get select attributs, since we don't need any other in selectattribut
					// Matrix and we need the arrayKeys in this case. Get the localized values
					// from tx_commerce_articles_article_attributes_mm
					$valuelist = array();
					$attributeUid = $data['uid'];

					$attributeValueResult = $database->exec_SELECT_mm_query(
						'distinct tx_commerce_articles_article_attributes_mm.uid_valuelist', 'tx_commerce_articles',
						'tx_commerce_articles_article_attributes_mm', 'tx_commerce_attributes',
						' AND tx_commerce_articles_article_attributes_mm.uid_valuelist>0 AND tx_commerce_articles.uid_product = ' .
						$this->uid . ' AND tx_commerce_attributes.uid=' . $attributeUid . $addwhere
					);
					if (($valueshown == FALSE) && ($attributeValueResult)
						&& ($database->sql_num_rows(
								$attributeValueResult
							) > 0)
					) {
						while (($value = $database->sql_fetch_assoc($attributeValueResult))) {
							if ($value['uid_valuelist'] > 0) {
								$resvalue = $database->exec_SELECTquery(
									'*', 'tx_commerce_attribute_values', 'uid = ' . $value['uid_valuelist']
								);
								$row = $database->sql_fetch_assoc($resvalue);
								if ($this->lang_uid > 0) {
									$row = $GLOBALS['TSFE']->sys_page->getRecordOverlay(
										'tx_commerce_attribute_values', $row, $this->lang_uid, $this->translationMode
									);
									if (!is_array($row)) {
										continue;
									}
								}
								if (($showHiddenValues == TRUE) || (($showHiddenValues == FALSE) && ($row['showvalue'] == 1))) {
									$valuelist[$row['uid']] = $row;
									$valueshown = TRUE;
								}
							}
						}
						usort(
							$valuelist, array(
								'Tx_Commerce_Domain_Model_Product',
								'compareBySorting'
							)
						);
					}

					if ($valueshown == TRUE) {
						$return[$attributeUid] = array(
							'title' => $data['title'],
							'unit' => $data['unit'],
							'values' => $valuelist,
							'valueformat' => $data['valueformat'],
							'Internal_title' => $data['internal_title'],
							'icon' => $data['icon'],
							'iconmode' => $data['iconmode'],
						);
					}
				}

				return $return;
			}
		}

		return FALSE;
	}

	/**
	 * Generates the matrix for attribute values for attribute select options in FE
	 *
	 * @param array $attributeValues Of attribute->value pairs, used as default.
	 *
	 * @return array Values
	 */
	public function getSelectAttributeValueMatrix(array &$attributeValues = array()) {
		$values = array();
		$levelAttributes = array();

		$database = $this->getDatabaseConnection();

		if ($this->uid > 0) {
			$articleList = $this->loadArticles();

			$addWhere = '';
			if (is_array($articleList) && count($articleList) > 0) {
				$queryArticleList = implode(',', $articleList);
				$addWhere = 'uid_local IN (' . $queryArticleList . ')';
			}

			$articleAttributes = $database->exec_SELECTgetRows(
				'uid_local, uid_foreign, uid_valuelist',
				'tx_commerce_articles_article_attributes_mm',
				$addWhere,
				'',
				'uid_local, sorting'
			);

			$levels = array();
			$article = FALSE;
			$attributeValuesList = array();
			$attributeValueSortIndex = array();

			foreach ($articleAttributes as $articleAttribute) {
				$attributeValuesList[] = $articleAttribute['uid_valuelist'];
				if ($article != $articleAttribute['uid_local']) {
					$current = & $values;
					if (count($levels)) {
						foreach ($levels as $level) {
							if (!isset($current[$level])) {
								$current[$level] = array();
							}
							$current = & $current[$level];
						}
					}
					$levels = array();
					$levelAttributes = array();
					$article = $articleAttribute['uid_local'];
				}
				$levels[] = $articleAttribute['uid_valuelist'];
				$levelAttributes[] = $articleAttribute['uid_foreign'];
			}

			$current = & $values;
			if (count($levels)) {
				foreach ($levels as $level) {
					if (!isset($current[$level])) {
						$current[$level] = array();
					}
					$current = & $current[$level];
				}
			}

			// Get the sorting value for all attribute values
			if (count($attributeValuesList)) {
				$attributeValuesList = array_unique($attributeValuesList);
				$attributeValuesList = implode($attributeValuesList, ',');
				$attributeValueSortQuery = $database->exec_SELECTquery(
					'sorting, uid',
					'tx_commerce_attribute_values',
					'uid IN (' . $attributeValuesList . ')'
				);
				while (($attributeValueSort = $database->sql_fetch_assoc($attributeValueSortQuery))) {
					$attributeValueSortIndex[$attributeValueSort['uid']] = $attributeValueSort['sorting'];
				}
			}
		}

		$selectMatrix = array();
		$possible = $values;
		$impossible = array();

		foreach ($levelAttributes as $attributeUid) {
			$tImpossible = array();
			$tPossible = array();
			$selected = $attributeValues[$attributeUid];
			if (!$selected) {
				/**
				 * Attribute
				 *
				 * @var Tx_Commerce_Domain_Model_Attribute $attribute
				 */
				$attribute = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
					'Tx_Commerce_Domain_Model_Attribute',
					$attributeUid,
					$this->getFrontendController()->tmpl->setup['config.']['sys_language_uid']
				);
				$attribute->loadData();
				$attributeValues[$attributeUid] = $selected = $attribute->getFirstAttributeValueUid($possible);
			}

			foreach ($impossible as $key => $val) {
				$selectMatrix[$attributeUid][$key] = 'disabled';
				foreach ($val as $k => $v) {
					$tImpossible[$k] = $v;
				}
			}

			foreach ($possible as $key => $val) {
				$selectMatrix[$attributeUid][$key] = $selected == $key ? 'selected' : 'possible';
				foreach ($val as $k => $v) {
					if (!$selected || $key == $selected) {
						$tPossible[$k] = $v;
					} else {
						$tImpossible[$k] = $v;
					}
				}
			}

			$possible = $tPossible;
			$impossible = $tImpossible;
		}

		return $selectMatrix;
	}

	/**
	 * Return product subtitle
	 *
	 * @return string Product subtitle
	 */
	public function getSubtitle() {
		return $this->subtitle;
	}

	/**
	 * Returns the uid of the live version of this product
	 *
	 * @return int UID of live version of this product
	 */
	public function getT3verOid() {
		return $this->t3ver_oid;
	}

	/**
	 * Return product title
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Returns the product teaser
	 *
	 * @return string Product teaser
	 */
	public function getTeaser() {
		return $this->teaser;
	}

	/**
	 * Returns an Array of Images
	 *
	 * @return array Images of this product
	 */
	public function getTeaserImages() {
		return $this->teaserImagesArray;
	}

	/**
	 * Load article list of this product and store in private class variable
	 *
	 * @return array Article uids
	 */
	public function loadArticles() {
		if ($this->articlesLoaded == FALSE) {
			$uidToLoadFrom = $this->uid;
			if (
				$this->getT3verOid() > 0 && $this->getT3verOid() <> $this->uid &&
				(is_Object($GLOBALS['TSFE']) && $GLOBALS['TSFE']->beUserLogin)
			) {
				$uidToLoadFrom = $this->getT3verOid();
			}
			if (($this->articles_uids = $this->databaseConnection->getArticles($uidToLoadFrom))) {
				foreach ($this->articles_uids as $articleUid) {
					/**
					 * Article
					 *
					 * @var Tx_Commerce_Domain_Model_Article $article
					 */
					$article = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
						'Tx_Commerce_Domain_Model_Article',
						$articleUid,
						$this->lang_uid
					);
					$article->loadData();
					$this->articles[$articleUid] = $article;
				}
				$this->articlesLoaded = TRUE;

				return $this->articles_uids;
			} else {
				return FALSE;
			}
		}

		return $this->articles_uids;
	}

	/**
	 * Load data and divide comma sparated images in array
	 * inherited from parent
	 *
	 * @param mixed $translationMode Translation mode of the record,
	 *	default FALSE to use the default way of translation
	 *
	 * @return Tx_Commerce_Domain_Model_Product
	 */
	public function loadData($translationMode = FALSE) {
		$return = parent::loadData($translationMode);

		$this->images_array = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->images);
		$this->teaserImagesArray = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->teaserimages);

		return $return;
	}

	/**
	 * Returns TRUE if one Article of Product have more than
	 * zero articles on stock
	 *
	 * @return bool TRUE if one article of product has stock > 0
	 */
	public function hasStock() {
		$this->loadArticles();
		$result = FALSE;
		/**
		 * Article
		 *
		 * @var Tx_Commerce_Domain_Model_Article $article
		 */
		foreach ($this->articles as $article) {
			if ($article->getStock()) {
				$result = TRUE;
				break;
			}
		}

		return $result;
	}

	/**
	 * Carries out the move of the product to the new parent
	 * Permissions are NOT checked, this MUST be done beforehand
	 *
	 * @param int $uid Uid of the move target
	 * @param string $op Operation of move (can be 'after' or 'into'
	 *
	 * @return bool True on success
	 */
	public function move($uid, $op = 'after') {
		if ($op == 'into') {
			// Uid is a future parent
			$parentUid = $uid;
		} else {
			return FALSE;
		}

		// Update parent_category
		$set = $this->databaseConnection->updateRecord($this->uid, array('categories' => $parentUid));

		// Update relations only, if parent_category was successfully set
		if ($set) {
			$catList = array($parentUid);
			$catList = Tx_Commerce_Utility_BackendUtility::getUidListFromList($catList);
			$catList = Tx_Commerce_Utility_BackendUtility::extractFieldArray($catList, 'uid_foreign', TRUE);
			Tx_Commerce_Utility_BackendUtility::saveRelations($this->uid, $catList, 'tx_commerce_products_categories_mm', TRUE);
		} else {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Remove article uid from array by index
	 *
	 * @param int $index Index
	 *
	 * @return void
	 */
	public function removeArticleUid($index) {
		unset($this->articles_uids[$index]);
	}

	/**
	 * Remove article object from array by uid
	 *
	 * @param int $uid Uid
	 *
	 * @return void
	 */
	public function removeArticle($uid) {
		unset($this->articles[$uid]);
	}


	/**
	 * Get relevant articles
	 *
	 * @param array|bool $attributeArray Attribute array
	 *
	 * @return array|bool
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, is not used in commerce
	 */
	public function getRelevantArticles($attributeArray = FALSE) {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();
		// First we need all possible Attribute id's (not attribute value id's)
		foreach ($this->attribute as $attribute) {
			$attributeIsInArray = FALSE;
			foreach ($attributeArray as $attributeToCheck) {
				if ($attributeToCheck['AttributeUid'] == $attribute->uid) {
					$attributeIsInArray = TRUE;
				}
			}
			if (!$attributeIsInArray) {
				$attributeArray[] = array(
					'AttributeUid' => $attribute->uid,
					'AttributeValue' => NULL
				);
			}
		}

		if ($this->uid > 0 && is_array($attributeArray) && count($attributeArray)) {
			$unionSelects = array();
			foreach ($attributeArray as $attr) {
				if ($attr['AttributeValue']) {
					$unionSelects[] = '
						SELECT uid_local AS article_id, uid_valuelist
						FROM tx_commerce_articles_article_attributes_mm, tx_commerce_articles
						WHERE uid_local = uid AND uid_valuelist = ' . (int) $attr['AttributeValue'] .
									  ' AND tx_commerce_articles.uid_product = ' . $this->uid .
									  ' AND uid_foreign = ' . (int) $attr['AttributeUid'] .
									  $GLOBALS['TSFE']->sys_page->enableFields('tx_commerce_articles', $GLOBALS['TSFE']->showHiddenRecords);
				} else {
					$unionSelects[] = '
						SELECT uid_local AS article_id, uid_valuelist
						FROM tx_commerce_articles_article_attributes_mm, tx_commerce_articles
						WHERE uid_local = uid AND tx_commerce_articles.uid_product = ' . $this->uid .
									  ' AND uid_foreign = ' . (int) $attr['AttributeUid'] .
									  $GLOBALS['TSFE']->sys_page->enableFields('tx_commerce_articles', $GLOBALS['TSFE']->showHiddenRecords);
				}
			}
			$sql = '';
			if (is_array($unionSelects)) {
				$sql .= ' SELECT count(article_id) AS counter, article_id FROM (' . implode(' UNION ', $unionSelects) .
						') AS data GROUP BY article_id having COUNT(article_id) >= ' . (count($unionSelects) - 1) . '';
			}

			$database = $this->getDatabaseConnection();

			$res = $database->sql_query($sql);
			$articleUids = array();
			while (($row = $database->sql_fetch_assoc($res))) {
				$articleUids[] = $row['article_id'];
			}

			return $articleUids;
		}

		return FALSE;
	}

	/**
	 * Compare an array record by its sorting value
	 *
	 * @param array $array1 Left
	 * @param array $array2 Right
	 *
	 * @return int
	 */
	public static function compareBySorting($array1, $array2) {
		return $array1['sorting'] - $array2['sorting'];
	}

	/**
	 * Generates a Matrix from these concerning articles for all attributes
	 * and the values therefor
	 *
	 * @param mixed $articleList Uids of articles or FALSE
	 * @param mixed $attributeToInclude Array of attribute uids to include
	 *        or FALSE for all attributes
	 * @param bool $showHiddenValues Wether or net hidden values should be shown
	 * @param string $sortingTable Default order by of attributes
	 *
	 * @return bool|array
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getSelectAttributeMatrix instead
	 */
	public function get_selectattribute_matrix($articleList = FALSE, $attributeToInclude = FALSE, $showHiddenValues = TRUE,
		$sortingTable = 'tx_commerce_articles_article_attributes_mm'
	) {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getSelectAttributeMatrix($articleList, $attributeToInclude, $showHiddenValues, $sortingTable);
	}

	/**
	 * Get list of articles of this product filtered by given attribute
	 * UID and attribute value
	 *
	 * @param int $attributeUid Attribute_UID
	 * @param int $attributeValue Attribute_value
	 *
	 * @return array of article uids
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getArticlesByAttribute instead
	 */
	public function get_Articles_by_Attribute($attributeUid, $attributeValue) {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getArticlesByAttribute($attributeUid, $attributeValue);
	}

	/**
	 * Get list of articles of this product filtered by given attribute
	 * UID and attribute value
	 *
	 * @param array $attribute_Array Attributes(
	 *	array('AttributeUid'=>$attributeUID, 'AttributeValue'=>$attributeValue),
	 *	array('AttributeUid'=>$attributeUID, 'AttributeValue'=>$attributeValue),
	 *   ...
	 * )
	 * @param bool|int $proofUid Proof if script is running without
	 * 	instance and so without a single product
	 *
	 * @return array of article uids
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getArticlesByAttributeArray instead
	 */
	public function get_Articles_by_AttributeArray($attribute_Array, $proofUid = 1) {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getArticlesByAttributeArray($attribute_Array, $proofUid);
	}

	/**
	 * Get l18n overlays of this product
	 *
	 * @return array l18n overlay objects
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getL18nProducts instead
	 */
	public function get_l18n_products() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getL18nProducts();
	}

	/**
	 * Get number of articles of this product
	 *
	 * @return int Number of articles
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getArticlesCount instead
	 */
	public function getNumberOfArticles() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getArticlesCount();
	}

	/**
	 * Returns the product teaser
	 *
	 * @return string Product teaser
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getTeaser instead
	 */
	public function get_teaser() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getTeaser();
	}

	/**
	 * Return Product description
	 *
	 * @return string Product description
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getDescription instead
	 */
	public function get_description() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getDescription();
	}

	/**
	 * Return product subtitle
	 *
	 * @return string Product subtitle
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getSubtitle instead
	 */
	public function get_subtitle() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getSubtitle();
	}

	/**
	 * Returns the uid of the live version of this product
	 *
	 * @return int UID of live version of this product
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getT3verOid instead
	 */
	public function get_t3ver_oid() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getT3verOid();
	}

	/**
	 * Return product pid
	 *
	 * @return int Product pid
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getPid instead
	 */
	public function get_pid() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getPid();
	}

	/**
	 * Get category master parent category
	 *
	 * @return int uid of master parent category
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getMasterparentCategory instead
	 */
	public function getMasterparentCategorie() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getMasterparentCategory();
	}

	/**
	 * Return product title
	 *
	 * @return string Product title
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getTitle instead
	 */
	public function get_title() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getTitle();
	}

	/**
	 * Gets the category master parent
	 *
	 * @return array
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getMasterparentCategory instead
	 */
	public function get_masterparent_categorie() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getMasterparentCategory();
	}

	/**
	 * Get all parent categories
	 *
	 * @return array Uids of categories
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getImages instead
	 */
	public function get_parent_categories() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getParentCategories();
	}

	/**
	 * Returns an Array of Images
	 *
	 * @return array
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getImages instead
	 */
	public function get_images() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getImages();
	}

	/**
	 * Sets a short description
	 *
	 * @param int $leng Length
	 *
	 * @return void
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use typoscript instead
	 */
	public function set_leng_description($leng = 150) {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		$this->description = substr($this->description, 0, $leng) . '...';
	}

	/**
	 * Returns the attribute matrix
	 *
	 * @param bool $articleList Article list
	 * @param bool $attribute_include Attribute include
	 * @param bool $showHiddenValues Show hidden values
	 * @param string $sortingTable Sorting table
	 * @param bool $fallbackToDefault Fallback to default
	 *
	 * @return mixed
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getAttributeMatrix instead
	 */
	public function get_attribute_matrix($articleList = FALSE, $attribute_include = FALSE, $showHiddenValues = TRUE,
		$sortingTable = 'tx_commerce_articles_article_attributes_mm', $fallbackToDefault = FALSE
	) {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getAttributeMatrix($articleList, $attribute_include, $showHiddenValues, $sortingTable, $fallbackToDefault);
	}

	/**
	 * Returns the attribute matrix
	 *
	 * @param bool $articleList Article list
	 * @param bool $attribute_include Attribute include
	 * @param bool $showHiddenValues Show hidden values
	 * @param string $sortingTable Sorting table
	 *
	 * @return mixed
	 * @see getAttributeMatrix()
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getAttributeMatrix instead
	 */
	public function get_atrribute_matrix($articleList = FALSE, $attribute_include = FALSE, $showHiddenValues = TRUE,
		$sortingTable = 'tx_commerce_articles_article_attributes_mm'
	) {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getAttributeMatrix($articleList, $attribute_include, $showHiddenValues, $sortingTable);
	}

	/**
	 * Returns the attribute matrix
	 *
	 * @param bool $attribute_include Attribute include
	 * @param bool $showHiddenValues Show hidden values
	 * @param string $sortingTable Sorting Table
	 *
	 * @return mixed
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getAttributeMatrix instead
	 */
	public function get_product_attribute_matrix($attribute_include = FALSE, $showHiddenValues = TRUE,
		$sortingTable = 'tx_commerce_products_attributes_mm'
	) {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getAttributeMatrix(
			FALSE, $attribute_include, $showHiddenValues, $sortingTable, FALSE, 'tx_commerce_products'
		);
	}

	/**
	 * Generates a Matrix fro these concerning products
	 * for all Attributes and the values therfor
	 *
	 * @param bool $attribute_include Attribte include
	 * @param bool $showHiddenValues Show hidden values
	 * @param string $sortingTable Sorting table
	 *
	 * @return mixed
	 * @see getAttributeMatrix()
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getAttributeMatrix instead
	 */
	public function get_product_atrribute_matrix($attribute_include = FALSE, $showHiddenValues = TRUE,
		$sortingTable = 'tx_commerce_products_attributes_mm'
	) {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getAttributeMatrix(
			FALSE, $attribute_include, $showHiddenValues, $sortingTable, FALSE, 'tx_commerce_products'
		);
	}

	/**
	 * Get articles uids
	 *
	 * @return array
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use getArticleUids instead
	 */
	public function getArticles() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getArticleUids();
	}

	/**
	 * Load article list of this product and store in private class variable
	 *
	 * @return array Article uids
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0, please use loadArticles instead
	 */
	public function load_articles() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->loadArticles();
	}


	/**
	 * Get database connection
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Get typoscript frontend controller
	 *
	 * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 */
	protected function getFrontendController() {
		return $GLOBALS['TSFE'];
	}
}
