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
 * Main script class for the handling of attributes. An attribute desribes the
 * technical data of an article
 * Libary for Frontend-Rendering of attributes. This class
 * should be used for all Fronten-Rendering, no Database calls
 * to the commerce tables should be made directly
 * This Class is inhertited from Tx_Commerce_Domain_Model_AbstractEntity, all
 * basic Database calls are made from a separate Database Class
 * Do not acces class variables directly, allways use the get and set methods,
 * variables will be changed in php5 to private
 * Basic class for handleing attributes
 *
 * Class Tx_Commerce_Domain_Model_Attribute
 *
 * @author 2005-2011 Ingo Schmitt <is@marketing-factory.de>
 */
class Tx_Commerce_Domain_Model_Attribute extends Tx_Commerce_Domain_Model_AbstractEntity {
	/**
	 * Database class name
	 *
	 * @var string
	 */
	protected $databaseClass = 'Tx_Commerce_Domain_Repository_AttributeRepository';

	/**
	 * Database connection
	 *
	 * @var Tx_Commerce_Domain_Repository_AttributeRepository
	 */
	public $databaseConnection;

	/**
	 * Title of Attribute
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * Unit auf the attribute
	 *
	 * @var string
	 */
	protected $unit = '';

	/**
	 * If the attribute has a separate value_list for selecting the value
	 *
	 * @var int
	 */
	protected $has_valuelist = 0;

	/**
	 * Check if attribute values are already loaded
	 *
	 * @var bool
	 */
	protected $attributeValuesLoaded = FALSE;

	/**
	 * Attribute value uid list
	 *
	 * @var array
	 */
	protected $attribute_value_uids = array();

	/**
	 * Attribute value object list
	 *
	 * @var array
	 */
	protected $attribute_values = array();

	/**
	 * Icon mode
	 *
	 * @var int
	 */
	protected $iconmode = 0;

	/**
	 * Attribute
	 *
	 * @var int|Tx_Commerce_Domain_Model_Attribute
	 */
	protected $parent = 0;

	/**
	 * Children
	 *
	 * @var array
	 */
	protected $children = NULL;

	/**
	 * Constructor class, basically calls init
	 *
	 * @param int $uid Attribute uid
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
	 * Constructor class, basically calls init
	 *
	 * @param int $uid Uid or attribute
	 * @param int $languageUid Language uid, default 0
	 *
	 * @return bool
	 */
	public function init($uid, $languageUid = 0) {
		$uid = (int) $uid;
		$this->fieldlist = array(
			'title',
			'unit',
			'iconmode',
			'has_valuelist',
			'l18n_parent',
			'parent'
		);

		if ($uid > 0) {
			$this->uid = $uid;
			$this->lang_uid = (int) $languageUid;
			$this->databaseConnection = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($this->databaseClass);

			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['commerce/lib/class.tx_commerce_attribute.php']['postinit'])) {
				\TYPO3\CMS\Core\Utility\GeneralUtility::deprecationLog('
					hook
					$GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'commerce/lib/class.tx_commerce_attribute.php\'][\'postinit\']
					is deprecated since commerce 1.0.0, it will be removed in commerce 1.4.0, please use instead
					$GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'commerce/Classes/Domain/Model/Attribute.php\'][\'postinit\']
				');
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['commerce/lib/class.tx_commerce_attribute.php']['postinit'] as $classRef) {
					$hookObj = \TYPO3\CMS\Core\Utility\GeneralUtility::getUserObj($classRef);
					if (method_exists($hookObj, 'postinit')) {
						$hookObj->postinit($this);
					}
				}
			}
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['commerce/Classes/Domain/Model/Attribute.php']['postinit'])) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['commerce/Classes/Domain/Model/Attribute.php']['postinit'] as $classRef) {
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
	 * How do we take care about depencies between attributes?
	 *
	 * @param bool|object $returnAsObjects Condition to return the value objects
	 * @param bool|object $product Return only attribute values that are
	 *	possible for the given product
	 *
	 * @return array values of attribute
	 */
	public function getAllValues($returnAsObjects = FALSE, $product = FALSE) {
		/**
		 * Attribute value
		 *
		 * @var $attributeValue Tx_Commerce_Domain_Model_AttributeValue
		 */
		if ($this->attributeValuesLoaded === FALSE) {
			if (($this->attribute_value_uids = $this->databaseConnection->getAttributeValueUids($this->uid))) {
				foreach ($this->attribute_value_uids as $valueUid) {
					/**
					 * Attribute value
					 *
					 * @var $attributeValue Tx_Commerce_Domain_Model_AttributeValue
					 */
					$attributValue = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
						'Tx_Commerce_Domain_Model_AttributeValue',
						$valueUid,
						$this->lang_uid
					);
					$attributValue->loadData();

					$this->attribute_values[$valueUid] = $attributValue;
				}
				$this->attributeValuesLoaded = TRUE;
			}
		}

		$attributeValues = $this->attribute_values;

		// if productObject is a productObject we have to remove the attribute
		// values wich are not possible at all for this product
		if (is_object($product)) {
			$tAttributeValues = array();
			$productSelectAttributeValues = $product->get_selectattribute_matrix(FALSE, array($this->uid));
			foreach ($attributeValues as $attributeKey => $attributeValue) {
				foreach ($productSelectAttributeValues[$this->uid]['values'] as $selectAttributeValue) {
					if ($attributeValue->getUid() == $selectAttributeValue['uid']) {
						$tAttributeValues[$attributeKey] = $attributeValue;
					}
				}
			}
			$attributeValues = $tAttributeValues;
		}

		if ($returnAsObjects) {
			return $attributeValues;
		}

		$return = array();
		foreach ($attributeValues as $valueUid => $attributeValue) {
			$return[$valueUid] = $attributeValue->getValue();
		}

		return $return;
	}

	/**
	 * Get first attribute value uid
	 *
	 * @param bool|array $includeValues Array of allowed values,
	 *        if empty all values are allowed
	 *
	 * @return int first attribute uid
	 */
	public function getFirstAttributeValueUid($includeValues = FALSE) {
		$attributes = $this->databaseConnection->getAttributeValueUids($this->uid);

		if (is_array($includeValues) && count($includeValues) > 0) {
			$attributes = array_intersect($attributes, array_keys($includeValues));
		}

		return array_shift($attributes);
	}

	/**
	 * Synonym to get_all_values
	 *
	 * @see tx_commerce_attributes->get_all_values()
	 * @return array
	 */
	public function getValues() {
		return $this->getAllValues();
	}

	/**
	 * Synonym to get_all_values
	 *
	 * @param int $uid Value
	 *
	 * @return bool|string
	 * @see tx_commerce_attributes->get_all_values()
	 */
	public function getValue($uid) {
		$result = FALSE;
		if ($uid) {
			if (!$this->has_valuelist) {
				$this->getAllValues();

				/**
				 * Attribute value
				 *
				 * @var $attributeValue Tx_Commerce_Domain_Model_AttributeValue
				 */
				$attributeValue = $this->attribute_values[$uid];
				$result = $attributeValue->getValue();
			}
		}

		return $result;
	}

	/**
	 * Gets the title
	 *
	 * @return string title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Getter
	 *
	 * @return string unit
	 */
	public function getUnit() {
		return $this->unit;
	}

	/**
	 * Overwrite get_attributes as attributes cant hav attributes
	 *
	 * @return bool
	 */
	public function getAttributes() {
		return FALSE;
	}

	/**
	 * Get parent
	 *
	 * @param bool|string $translationMode Translation mode
	 *
	 * @return int|Tx_Commerce_Domain_Model_Attribute
	 */
	public function getParent($translationMode = FALSE) {
		if (is_int($this->parent) && $this->parent > 0) {
			/**
			 * Attribute
			 *
			 * @var $parent Tx_Commerce_Domain_Model_Attribute
			 */
			$parent = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(get_class($this));
			$parent->init($this->parent, $this->lang_uid);
			$parent->loadData($translationMode);

			$this->parent = $parent;
		}

		return $this->parent;
	}

	/**
	 * Get children
	 *
	 * @param bool|string $translationMode Translation mode
	 *
	 * @return null|array
	 */
	public function getChildren($translationMode = FALSE) {
		if ($this->children === NULL) {
			$childAttributeList = $this->databaseConnection->getChildAttributeUids($this->uid);

			foreach ($childAttributeList as $childAttributeUid) {
				/**
				 * Attribute
				 *
				 * @var $parent Tx_Commerce_Domain_Model_Attribute
				 */
				$attribute = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(get_class($this));
				$attribute->init($childAttributeUid, $this->lang_uid);
				$attribute->loadData($translationMode);

				$this->children[$childAttributeUid] = $attribute;
			}
		}

		return $this->children;
	}

	/**
	 * Check if it is an Iconmode Attribute
	 *
	 * @return bool
	 */
	public function isIconmode() {
		return $this->iconmode == '1';
	}

	/**
	 * Check if attribute has parent
	 *
	 * @return bool
	 */
	public function hasParent() {
		return is_object($this->parent);
	}

	/**
	 * Check if attribute has children
	 *
	 * @return bool
	 */
	public function hasChildren() {
		return count($this->children) > 0;
	}


	/**
	 * Get all values
	 *
	 * @param bool|object $returnObjects Returns
	 * @param bool|object $productObject Products
	 *
	 * @return array
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0 - Use tx_commerce_attribute::getAllValues() instead
	 */
	public function get_all_values($returnObjects = FALSE, $productObject = FALSE) {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getAllValues($returnObjects, $productObject);
	}

	/**
	 * Values
	 *
	 * @return array
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0 - Use tx_commerce_attribute::getValues() instead
	 */
	public function get_values() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getValues();
	}

	/**
	 * Value
	 *
	 * @param int $uid Value uid
	 *
	 * @return bool|string
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0 - Use tx_commerce_attribute::getValue() instead
	 */
	public function get_value($uid) {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getValue($uid);
	}

	/**
	 * Title
	 *
	 * @return string title
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0 - Use tx_commerce_attribute::getTitle() instead
	 */
	public function get_title() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getTitle();
	}

	/**
	 * Overwrite get_attributes as attributes cant hav attributes
	 *
	 * @return bool
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0 - Use tx_commerce_attribute::getAttributes() instead
	 */
	public function get_attributes() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getAttributes();
	}

	/**
	 * Unit
	 *
	 * @return string unit
	 * @deprecated since commerce 1.0.0, this function will be removed in commerce 1.4.0 - Use tx_commerce_attribute::getUnit() instead
	 */
	public function get_unit() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();

		return $this->getUnit();
	}
}
