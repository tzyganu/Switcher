<?php
/**
 * Easylife_Switcher extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE_EASYLIFE_SWITCHER.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category   	Easylife
 * @package	    Easylife_Switcher
 * @copyright   2013 - 2014 Marius Strajeru
 * @license	    http://opensource.org/licenses/mit-license.php MIT License
 */
$this->startSetup();
$table = $this->getConnection()
    ->newTable($this->getTable('easylife_switcher/hashcode'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Hash ID'
    )
    ->addColumn(
        'option_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array(
            'nullable'  => false,
        ),
        'Option id'
    )
    ->addColumn('hashcode', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'hashcode')
    ->addIndex(
        $this->getIdxName(
            'easylife_switcher/hashcode',
            array('option_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('option_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->setComment('Hashcode table');
$this->getConnection()->createTable($table);
$this->endSetup();
