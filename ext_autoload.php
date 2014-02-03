<?php

$extensionPath = t3lib_extMgm::extPath('commerce');
$classPath = $extensionPath . 'Classes/';

return array(
	'tx_commerce_cli' => $extensionPath . 'cli/Statistic.php',

	'tx_commerce_dao_addressdao' => $classPath . 'Dao/AddressDao.php',
	'tx_commerce_dao_addressdaomapper' => $classPath . 'Dao/AddressDaoMapper.php',
	'tx_commerce_dao_addressdaoobject' => $classPath . 'Dao/AddressDaoObject.php',
	'tx_commerce_dao_addressdaoparser' => $classPath . 'Dao/AddressDaoParser.php',
	'tx_commerce_dao_addressobserver' => $classPath . 'Dao/AddressObserver.php',
	'tx_commerce_dao_basicdao' => $classPath . 'Dao/BasicDao.php',
	'tx_commerce_dao_basicdaomapper' => $classPath . 'Dao/BasicDaoMapper.php',
	'tx_commerce_dao_basicdaoobject' => $classPath . 'Dao/BasicDaoObject.php',
	'tx_commerce_dao_basicdaoparser' => $classPath . 'Dao/BasicDaoParser.php',
	'tx_commerce_dao_feuserdao' => $classPath . 'Dao/FeuserDao.php',
	'tx_commerce_dao_feuserdaomapper' => $classPath . 'Dao/FeuserDaoMapper.php',
	'tx_commerce_dao_feuserdaoobject' => $classPath . 'Dao/FeuserDaoObject.php',
	'tx_commerce_dao_feuserdaoparser' => $classPath . 'Dao/FeuserDaoParser.php',
	'tx_commerce_dao_feuserobserver' => $classPath . 'Dao/FeuserObserver.php',
	'tx_commerce_dao_feuseraddressfieldmapper' => $classPath . 'Dao/FeuserAddressFieldmapper.php',

	'tx_commerce_articlecreator' => $extensionPath . 'class.tx_commerce_articlecreator.php',
	'tx_commerce_attributeeditor' => $extensionPath . 'class.tx_commerce_attributeeditor.php',
	'tx_commerce_article' => $extensionPath . 'lib/class.tx_commerce_article.php',
	'tx_commerce_article_price' => $extensionPath . 'lib/class.tx_commerce_article_price.php',
	'tx_commerce_attribute' => $extensionPath . 'lib/class.tx_commerce_attribute.php',
	'tx_commerce_attribute_value' => $extensionPath . 'lib/class.tx_commerce_attribute_value.php',
	'tx_commerce_basic_basket' => $extensionPath . 'lib/class.tx_commerce_basic_basket.php',
	'tx_commerce_basket' => $extensionPath . 'lib/class.tx_commerce_basket.php',
	'tx_commerce_basket_item' => $extensionPath . 'lib/class.tx_commerce_basket_item.php',
	'tx_commerce_belib' => $extensionPath . 'lib/class.tx_commerce_belib.php',
	'tx_commerce_category' => $extensionPath . 'lib/class.tx_commerce_category.php',
	'tx_commerce_create_folder' => $extensionPath . 'lib/class.tx_commerce_create_folder.php',
	'tx_commerce_db_alib' => $extensionPath . 'lib/class.tx_commerce_db_alib.php',
	'tx_commerce_db_article' => $extensionPath . 'lib/class.tx_commerce_db_article.php',
	'tx_commerce_db_attribute' => $extensionPath . 'lib/class.tx_commerce_db_attribute.php',
	'tx_commerce_db_attribute_value' => $extensionPath . 'lib/class.tx_commerce_db_attribute_value.php',
	'tx_commerce_db_category' => $extensionPath . 'lib/class.tx_commerce_db_category.php',
	'tx_commerce_db_list' => $extensionPath . 'lib/class.tx_commerce_db_list.php',
	'commercerecordlist' => $extensionPath . 'lib/class.tx_commerce_db_list_extra.php',
	'tx_commerce_db_product' => $extensionPath . 'lib/class.tx_commerce_db_product.php',
	'tx_commerce_db_price' => $extensionPath . 'lib/class.tx_commerce_db_price.php',
	'tx_commerce_div' => $extensionPath . 'lib/class.tx_commerce_div.php',
	'tx_commerce_element_alib' => $extensionPath . 'lib/class.tx_commerce_element_alib.php',
	'tx_commerce_folder_db' => $extensionPath . 'lib/class.tx_commerce_folder_db.php',
	'tx_commerce_forms_select' => $extensionPath . 'lib/class.tx_commerce_forms_select.php',
	'tx_commerce_navigation' => $extensionPath . 'lib/class.tx_commerce_navigation.php',
	'user_tx_commerce_catmenu_pub' => $extensionPath . 'lib/class.user_tx_commerce_catmenu_pub.php',

	'tx_commerce_feusers_localrecordlist' => $extensionPath . 'lib/class.tx_commerce_feusers_localrecordlist.php',
	'tx_commerce_order_localrecordlist' => $extensionPath . 'lib/class.tx_commerce_order_localrecordlist.php',
	'tx_commerce_pibase' => $extensionPath . 'lib/class.tx_commerce_pibase.php',
	'tx_commerce_product' => $extensionPath . 'lib/class.tx_commerce_product.php',
	'tx_commerce_statistics' => $extensionPath . 'lib/class.tx_commerce_statistics.php',

	'tx_commerce_pi1' => $extensionPath . 'pi1/class.tx_commerce_pi1.php',
	'tx_commerce_pi2' => $extensionPath . 'pi2/class.tx_commerce_pi2.php',
	'tx_commerce_pi3' => $extensionPath . 'pi3/class.tx_commerce_pi3.php',
	'tx_commerce_pi4' => $extensionPath . 'pi4/class.tx_commerce_pi4.php',
	'tx_commerce_pi6' => $extensionPath . 'pi6/class.tx_commerce_pi6.php',

	'tx_commerce_categorytree' => $extensionPath . 'treelib/class.tx_commerce_categorytree.php',
	'tx_commerce_categorymounts' => $extensionPath . 'treelib/class.tx_commerce_categorymounts.php',
	'tx_commerce_leaf_article' => $extensionPath . 'treelib/class.tx_commerce_leaf_article.php',
	'tx_commerce_leaf_articledata' => $extensionPath . 'treelib/class.tx_commerce_leaf_articledata.php',
	'tx_commerce_leaf_articleview' => $extensionPath . 'treelib/class.tx_commerce_leaf_articleview.php',
	'tx_commerce_leaf_category' => $extensionPath . 'treelib/class.tx_commerce_leaf_category.php',
	'tx_commerce_leaf_categorydata' => $extensionPath . 'treelib/class.tx_commerce_leaf_categorydata.php',
	'tx_commerce_leaf_categoryview' => $extensionPath . 'treelib/class.tx_commerce_leaf_categoryview.php',
	'tx_commerce_leaf_product' => $extensionPath . 'treelib/class.tx_commerce_leaf_product.php',
	'tx_commerce_leaf_productdata' => $extensionPath . 'treelib/class.tx_commerce_leaf_productdata.php',
	'tx_commerce_leaf_productview' => $extensionPath . 'treelib/class.tx_commerce_leaf_productview.php',
	'tx_commerce_treelib_browser' => $extensionPath . 'treelib/class.tx_commerce_treelib_browser.php',
	'tx_commerce_treelib_link_categorytree' => $extensionPath . 'treelib/link/class.tx_commerce_treelib_link_categorytree.php',
	'tx_commerce_treelib_link_leaf_categoryview' => $extensionPath . 'treelib/link/class.tx_commerce_treelib_link_leaf_categoryview.php',
	'tx_commerce_treelib_link_leaf_productview' => $extensionPath . 'treelib/link/class.tx_commerce_treelib_link_leaf_productview.php',
	'tx_commerce_treelib_tceforms' => $extensionPath . 'treelib/class.tx_commerce_treelib_tceforms.php',

	'payment' => $extensionPath . 'payment/libs/class.payment.php',
	'wirecard' => $extensionPath . 'payment/libs/class.wirecard.php',
	'tx_commerce_payment' => $extensionPath . 'payment/interfaces/interface.tx_commerce_payment.php',
	'tx_commerce_payment_abstract' => $extensionPath . 'payment/class.tx_commerce_payment_abstract.php',
	'tx_commerce_payment_cashondelivery' => $extensionPath . 'payment/class.tx_commerce_payment_cashondelivery.php',
	'tx_commerce_payment_creditcard' => $extensionPath . 'payment/class.tx_commerce_payment_creditcard.php',
	'tx_commerce_payment_debit' => $extensionPath . 'payment/class.tx_commerce_payment_debit.php',
	'tx_commerce_payment_invoice' => $extensionPath . 'payment/class.tx_commerce_payment_invoice.php',
	'tx_commerce_payment_prepayment' => $extensionPath . 'payment/class.tx_commerce_payment_prepayment.php',

	'tx_commerce_payment_provider' => $extensionPath . 'payment/provider/interfaces/interface.tx_commerce_payment_provider.php',
	'tx_commerce_payment_provider_abstract' => $extensionPath . 'payment/provider/class.tx_commerce_payment_provider_abstract.php',
	'tx_commerce_payment_provider_wirecard' => $extensionPath . 'payment/provider/class.tx_commerce_payment_provider_wirecard.php',
	'tx_commerce_payment_wirecard_lib' => $extensionPath . 'payment/libs/class.tx_commerce_payment_wirecard_lib.php',

	'creditcardvalidationsolution' => $extensionPath . 'payment/ccvs/class.tx_commerce_payment_ccvs.php',
	'tx_commerce_payment_criterion' => $extensionPath . 'payment/criteria/interfaces/interface.tx_commerce_payment_criterion.php',
	'tx_commerce_payment_criterion_abstract' => $extensionPath . 'payment/criteria/class.tx_commerce_payment_criterion_abstract.php',
	'tx_commerce_payment_ccvs' => $extensionPath . 'payment/class.tx_commerce_payment_ccvs.php',

	'langbase' => $extensionPath . 'tree/class.langbase.php',
	'leaf' => $extensionPath . 'tree/class.leaf.php',
	'mounts' => $extensionPath . 'tree/class.mounts.php',
	'leafview' => $extensionPath . 'tree/class.leafView.php',
	'leafdata' => $extensionPath . 'tree/class.leafData.php',
	'leafslave' => $extensionPath . 'tree/class.leafSlave.php',
	'leafmaster' => $extensionPath . 'tree/class.leafMaster.php',
	'leafmasterdata' => $extensionPath . 'tree/class.leafMasterData.php',
	'leafslavedata' => $extensionPath . 'tree/class.leafSlaveData.php',
	'browsetree' => $extensionPath . 'tree/class.browsetree.php',

	'localpagetree' => $extensionPath . 'mod_orders/class.tx_commerce_order_pagetree.php',
	'user_orderedit_func' => $extensionPath . 'mod_orders/class.user_orderedit_func.php',

	'tx_moneylib' => t3lib_extMgm::extPath('moneylib') . 'class.tx_moneylib.php',
	'tx_staticinfotables_pi1' => t3lib_extMgm::extPath('static_info_tables') . 'pi1/class.tx_staticinfotables_pi1.php',

		// classes from outside of commerce
	'recordlist' => PATH_typo3 . 'class.db_list.inc',
	'localrecordlist' => PATH_typo3 . 'class.db_list_extra.inc',
);

?>