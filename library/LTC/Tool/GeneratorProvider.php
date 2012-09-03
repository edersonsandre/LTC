<?php

require_once 'LTC/Tool/Exception.php';

/**
 * LTC provider for Zend Tool
 *
 * @package LTC
 * @author Éderson Sandre <ederson.sandre@gmail.com>
 * @copyright Copyright(c) 2012 Éderson Sandre <ederson.sandre@gmail.com>
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 * @version $Id: GeneratorProvider.php 1 2012-09-03 14:40:01Z ederson.sandre@gmail.com $
 */
class LTC_Tool_GeneratorProvider extends Zend_Tool_Framework_Provider_Abstract {

    /**
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     *
     * @var string
     */
    protected $_dbName;

    /**
     * The package name that would be generated based on the dbName
     *
     * @var string
     */
    protected $_packageName;

    /**
     * The module name of generated code, set to null or empty to disable
     * 
     * @var string 
     */
    protected $_moduleName;

    /**
     * The controller name prefix
     * 
     * @var string
     */
    protected $_controllerNamePrefix = '';

    /**
     * Current working directory
     * 
     * @var string
     */
    protected $_cwd;

    /**
     * Zodeken directory
     * 
     * @var string
     */
    protected $_Dir = __DIR__;

    /**
     * The shared table definitions that would be set by _analyzeTableDefinitions()
     *
     * @var array
     */
    protected $_tables;

    /**
     * Overwrites the files
     *
     * @var bool
     */
    protected $_override = false;

    /**
     * Module
     *
     * @var string
     */
    protected $_module = '';

    public function __construct() {

        $this->_cwd = getcwd();
    }

    /**
     * Create File
     * 
     * @param string $path
     * @param string $code
     * @return bool
     */
    protected function _createFile($path, $code) {

        $path = preg_replace('#//#', '/', $path);
        $baseDir = pathinfo($path, PATHINFO_DIRNAME);

        if (!file_exists($baseDir)) {
            mkdir($baseDir, 0777, true);
        }

        if (!$this->_override && file_exists($path)) {
            echo "Arquivo existente: $path\n";
            return false;
        }

        if (@file_put_contents($path, $code)) {
            echo "Arquivo criado: $path\n";
            return true;
        } else {
            echo "Erro ao criar: $path\n";
        }

        return false;
    }

    /**
     * Convert a table name to class name.
     *
     * Eg, post -> Model_DbTable_Post, posts_tags => Model_DbTable_PostsTags
     *
     * @param string $tableName
     * @return string
     */
    protected function _getDbTableClassName($tableName) {
        return $this->_appnamespace . 'Model_'
                . $this->_getCamelCase($tableName) . '_DbTable';
    }

    /**
     * Convert a table name to a table's row class name.
     *
     * Eg, post -> Model_DbTable_Row_Post, posts_tags => Model_DbTable_Row_PostsTags
     *
     * @param string $tableName
     * @return string
     */
    protected function _getRowClassName($tableName) {
        return $this->_appnamespace . 'Model_'
                . $this->_getCamelCase($tableName) . '_Row';
    }

    /**
     * Convert a table name to a table's rowset class name.
     *
     * Eg, post -> Model_DbTable_Rowset_Post, posts_tags => Model_DbTable_Rowset_PostsTags
     *
     * @param string $tableName
     * @return string
     */
    protected function _getRowsetClassName($tableName) {
        return $this->_appnamespace . 'Model_'
                . $this->_getCamelCase($tableName) . '_Rowset';
    }

    /**
     * Convert a table name to a mapper class name.
     *
     * @param string $tableName
     * @return string
     */
    protected function _getMapperClassName($tableName) {
        return $this->_appnamespace . 'Model_' . $this->_getCamelCase($tableName) . 'Mapper';
    }

    /**
     * Convert a table name to a form class name ('latest' version).
     *
     * @param string $tableName
     * @return string
     */
    protected function _getFormLatestClassName($tableName) {
        return $this->_appnamespace . 'Form_Edit'
                . $this->_getCamelCase($tableName) . '_Latest';
    }

    /**
     * Convert a table name to a form class name.
     *
     * @param string $tableName
     * @return string
     */
    protected function _getFormClassName($tableName) {
        return $this->_appnamespace . 'Form_Edit' . $this->_getCamelCase($tableName);
    }

    /**
     * Convert a string to CamelCase format.
     *
     * Underscores are eliminated, each word's first character is capitalized.
     *
     * Eg, post -> Post, posts_tags => PostsTags
     *
     * @param string $string
     * @return string
     */
    protected function _getCamelCase($string) {
        $string = str_replace(array('_', '-'), ' ', $string);
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);

        return $string;
    }

    /**
     * Convert a string to CamelCase label.
     *
     * Underscores are eliminated, each word's first character is capitalized.
     *
     * Eg, post -> Post, posts_tags => Posts Tags
     *
     * @param string $string
     * @return string
     */
    protected function _getLabel($string) {
        $string = str_replace('_', ' ', $string);
        $string = ucwords($string);

        return $string;
    }

    /**
     * Analyze tables structure and relationships.
     *
     * These configurations are used by other methods.
     */
    protected function _analyzeTableDefinitions() {
        $tables = array();

        // get the list of tables
        echo "Analyzing tables\n";
        foreach ($this->_db->fetchAll("SHOW TABLES", array(), Zend_Db::FETCH_NUM) as $tableRow) {
            $tableName = $tableRow[0];

            $primaryKey = array();
            $fields = array();
            $dependentTables = array();
            $references = array();

            echo "\tAnalyzing table: $tableName\n";
            // loop through the field list
            foreach ($this->_db->fetchAll("SHOW FIELDS FROM `$tableName`") as $fieldRow) {
                /* @var $fieldRow Zend_Db_Table_Row_Abstract */

                // check if the field is listed in the primary key fields
                // strtoupper is probably not necessary, but add it for sure
                $isPrimaryKey = 'PRI' === strtoupper($fieldRow['Key']);

                if ($isPrimaryKey) {
                    $primaryKey[] = $fieldRow['Field'];
                }

                // analyze type definition to find the type name and type arguments
                // for example: ENUM('m','f'), INT(10), VARCHAR(200)...
                $typeAnalyzed = array();
                preg_match('#([a-z_\$]+)(?:\((.+)\))?#', $fieldRow['Type'], $typeAnalyzed);

                $field = array(
                    'name' => $fieldRow['Field'],
                    'getFunctionName' => 'get' . $this->_getCamelCase($fieldRow['Field']),
                    'setFunctionName' => 'set' . $this->_getCamelCase($fieldRow['Field']),
                    'label' => $this->_getLabel($fieldRow['Field']),
                    'is_required' => 'YES' === $fieldRow['Null'] ? false : true,
                    'is_primary_key' => $isPrimaryKey,
                    'default_value' => $fieldRow['Default'],
                    'type' => strtolower($typeAnalyzed[1]),
                    'php_type' => isset($this->_mysqlToPhpTypesMap[$typeAnalyzed[1]]) ? $this->_mysqlToPhpTypesMap[$typeAnalyzed[1]] : 'string',
                    'type_arguments' => ''
                );

                if (isset($typeAnalyzed[2])) {
                    $field['type_arguments'] = $typeAnalyzed[2];
                }

                $fields[] = $field;
            }

            echo "\t\tGet table relationships\n";
            // get dependent tables
            foreach ($this->_db->fetchAll("
                SELECT TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME
                FROM information_schema.key_column_usage
                WHERE REFERENCED_TABLE_SCHEMA = '$this->_dbName'
                    AND REFERENCED_TABLE_NAME = '$tableName'") as $dependentTable) {
                $dependentTables[] = array($dependentTable['TABLE_NAME'], $dependentTable['COLUMN_NAME']);
            }

            $foreignKeyInPrimaryKeyCount = 0;

            // get referenced tables
            foreach ($this->_db->fetchAll("
                SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
                FROM information_schema.key_column_usage
                WHERE TABLE_SCHEMA = '$this->_dbName'
                    AND TABLE_NAME = '$tableName'
                    AND REFERENCED_COLUMN_NAME IS NOT NULL
                ") as $referenceTable) {
                if (in_array($referenceTable['COLUMN_NAME'], $primaryKey)) {
                    $foreignKeyInPrimaryKeyCount++;
                }

                $references[$referenceTable['COLUMN_NAME']] = array(
                    'columns' => $referenceTable['COLUMN_NAME'],
                    'refTableClass' => $this->_getDbTableClassName($referenceTable['REFERENCED_TABLE_NAME']),
                    'refColumns' => $referenceTable['REFERENCED_COLUMN_NAME'],
                    'table' => $referenceTable['REFERENCED_TABLE_NAME']
                );
            }

            $tables[$tableName] = array(
                'name' => $tableName,
                'className' => $this->_getDbTableClassName($tableName),
                'classNameAbstract' => $this->_getDbTableClassName($tableName) . '_Abstract',
                'baseClassName' => $this->_getCamelCase($tableName),
                'controllerName' => str_replace('_', '-', $tableName),
                'rowClassName' => $this->_getRowClassName($tableName),
                'rowClassNameAbstract' => $this->_getRowClassName($tableName) . '_Abstract',
                'rowsetClassName' => $this->_getRowsetClassName($tableName),
                'rowsetClassNameAbstract' => $this->_getRowsetClassName($tableName) . '_Abstract',
                'mapperClassName' => $this->_getMapperClassName($tableName),
                'formClassName' => $this->_getFormClassName($tableName),
                'formClassNameLatest' => $this->_getFormLatestClassName($tableName),
                'primaryKey' => $primaryKey,
                'fields' => $fields,
                'dependentTables' => $dependentTables,
                'referenceMap' => $references,
                // if the primary key consists of 2 columns at least, mark
                // this as a map table
                'isMap' => $foreignKeyInPrimaryKeyCount > 1,
                'hasMany' => array(),
            );
        }

        // loop again to repair the many-to-many relationships
        foreach ($tables as $tableName => $table) {
            // we just find many-to-many from a map table, so if table is not a
            // map, we'll skip it
            if (!$table['isMap']) {
                continue;
            }

            $inRelationships = array();

            // loop through the references, get the referenced table that has
            // a field linking to the mapped table's primary key
            foreach ($table['referenceMap'] as $column => $reference) {
                // if the column of this table is one of the composite key,
                // we consider its refereced table as a table that has a
                // many-to-many relationship with another table
                if (in_array($column, $table['primaryKey'])) {
                    $inRelationships[] = array($reference['table'], $column);
                }
            }

            $tables[$inRelationships[0][0]]['hasMany'][$inRelationships[0][1]] = array($inRelationships[1][0], $inRelationships[1][1], $table['name']);
            $tables[$inRelationships[1][0]]['hasMany'][$inRelationships[1][1]] = array($inRelationships[0][0], $inRelationships[0][1], $table['name']);
        }

        $this->_tables = $tables;
    }

    /**
     * Preserve some special constants in application.ini file
     *
     * @param string $iniFilename
     */
    protected function _preserveIniConfigs($iniFilename) {
        $ini = file_get_contents($iniFilename);

        //$ini = preg_replace('#"([A-Z_]{2,})#s', '\1 "', $ini);

        $ini = str_replace('"APPLICATION_PATH/', 'APPLICATION_PATH "/', $ini);
        // "0" -> 0, "1" => 1...
        $ini = preg_replace('#= "(\d+)"#si', '= \1', $ini);

        file_put_contents($iniFilename, $ini);
    }

    /**
     * Show the question and retrieve answer from user
     *
     * @param string $question
     * @return string
     */
    protected function _readInput($question) {
        echo $question;

        return trim(fgets(STDIN));
    }

    /**
     * Generator CRUD
     *
     * @param bool @override
     * @param string $module
     * @return 
     */
    public function CRUD($override = false, $module = '') {

        $this->_cwd;

        $this->_override = $override == 1 ? true : false;

        if (!empty($module)) {
            $this->_module = "/modules/{$module}/";
        }


        $config = $this->_cwd . "/application/configs/application.ini";
        if (!file_exists($config)) {
            throw new LTC_Tool_Exception('Application config file not found: ' . $config);
        }

        // Get configs
        $configs = new Zend_Config_Ini($config);

        // Find configs in development
        $dbConfig = $configs->development->resources->db;

        // If not found, find it in production
        if ($dbConfig === null) {
            $dbConfig = $configs->production->resources->db;
        }

        if ($dbConfig === null) {
            throw new LTC_Tool_Exception("Db configs not found in your application.ini");
        }

        copy($config, $config . "_bkp");

        // used to modify the file
        $writableConfigs = new Zend_Config_Ini($config, null, array(
                    'skipExtends' => true,
                    'allowModifications' => true
                ));


        // get the app namespace
        if ($writableConfigs->production->appnamespace) {
            $this->_appnamespace = $writableConfigs->production->appnamespace;

            if ($this->_appnamespace[strlen($this->_appnamespace) - 1] !== '_') {

                $this->_appnamespace .= '_';
            }
        }

        $this->_dbName = $dbConfig->params->dbname;
        $this->_packageName = $this->_getCamelCase($this->_dbName);
        $this->_db = Zend_Db::factory($dbConfig);

        // modify the config file
        if (!$writableConfigs->zodeken) {
            $writableConfigs->zodeken = array();
        }

        // get package name from config
        if ($writableConfigs->zodeken->packageName) {
            $this->_packageName = $writableConfigs->zodeken->packageName;
        }

        $eol = PHP_EOL;

        // load the output files config
        $dir = dirname(__FILE__);
        $xdoc = new DOMDocument();
        $xdoc->load($dir . '/output-config.xml');
        $outputs = array();
        $asciiChar = 97;
        $allKeys = array();

        if ($this->_override) {
            echo "\033[1;31mATTENTION! Generator will override all existing files!\033[0;37m";
        }

        $question = array("{$eol}Which files do you want to generate?{$eol}- Enter 1 to generate all{$eol}- Enter a comma-separated list of generated files, e.g. a,b,c,d{$eol}    ");

        foreach ($xdoc->getElementsByTagName('output') as $outputElement) {
            $output = array(
                'key' => strtolower(chr($asciiChar++)),
                'templateName' => $outputElement->getAttribute('templateName'),
                'templateFile' => $dir . '/templates/default/' . $outputElement->getAttribute('templateName'),
                'canOverride' => (int) $outputElement->getAttribute('canOverride'),
                'outputPath' => $outputElement->getAttribute('outputPath'),
                'acceptMapTable' => $outputElement->getAttribute('acceptMapTable'),
            );

            $outputs[] = $output;

            $allKeys[] = strtolower($output['key']);

            $question[] = $output['key'] . '. ' . $output['templateName'];
        }

        $question = implode($eol . '    ', $question) . "{$eol}{$eol}Your choice: ";

        $input = strtolower(trim($this->_readInput($question)));


        if ($input == '1') {
            $keys = $allKeys;
        } elseif ($input) {
            $keys = explode(',', $input);
        } else {
            $keys = array();
        }

        $packageName = $this->_readInput("Your package name ($this->_packageName): ");

        if (!empty($packageName)) {
            $this->_packageName = $packageName;
        }

        // module support has been suggested by Brian Gerrity (bgerrity73@gmail.com)
        $moduleName = $this->_readInput("Module name (leave empty for default): ");

        if (!empty($moduleName)) {
            $this->_moduleName = $moduleName;
            $this->_controllerNamePrefix = $this->_getCamelCase($moduleName) . '_';
        }

        // auto-add "Zodeken_" to the autoloadernamespaces directive
        $autoloaderNamespaces = $writableConfigs->production->autoloadernamespaces;

        if (!$autoloaderNamespaces) {
            $autoloaderNamespaces = array('Zodeken_');
        } else {
            $autoloaderNamespaces = $autoloaderNamespaces->toArray();

            if (false === array_search('Zodeken_', $autoloaderNamespaces)) {
                $autoloaderNamespaces[] = 'Zodeken_';
            }
        }

        // auto-add "resources.frontController.moduleDirectory" if module is specified
        if (!empty($this->_moduleName)) {
            if (!$writableConfigs->production->resources->frontController->moduleDirectory) {
                $writableConfigs->production->resources->frontController->moduleDirectory = 'APPLICATION_PATH/modules';
            }

            if (!$writableConfigs->production->resources->modules) {
                $writableConfigs->production->resources->modules = '';
            }
        }

        // modify configs
        $writableConfigs->zodeken->packageName = $this->_packageName;
        $writableConfigs->production->autoloadernamespaces = $autoloaderNamespaces;

        $configWriter = new Zend_Config_Writer_Ini(array(
                    'config' => $writableConfigs,
                    'filename' => $config
                ));

        $configWriter->write();

        // some constants like APPLICATION_PATH is replaced with "APPLICATION_PATH"
        // we need to remove the double quotes...
        $this->_preserveIniConfigs($config);

        echo 'Configs have been written to application.ini', PHP_EOL;
        // end of modifying configs

        $this->_analyzeTableDefinitions();

        $moduleBaseDirectory = $this->_cwd . '/application';

        if (!empty($this->_moduleName)) {
            $moduleBaseDirectory .= '/modules/' . $this->_moduleName;
        }

        foreach ($this->_tables as $tableName => $tableDefinition) {
            $tableBaseClassName = $tableDefinition['baseClassName'];

            foreach ($outputs as $output) {
                if (!in_array($output['key'], $keys) || $tableDefinition['isMap'] && !$output['acceptMapTable']) {
                    continue;
                }

                $fileName = $output['outputPath'];
                $canOverride = $output['canOverride'];
                $templateFile = $output['templateFile'];

                $code = require $templateFile;

                $fileName = str_replace('{MODULE_BASE_DIR}', $moduleBaseDirectory, $fileName);
                $fileName = str_replace('{APPLICATION_DIR}', $moduleBaseDirectory, $fileName);
                $fileName = str_replace('{TABLE_CAMEL_NAME}', $tableDefinition['baseClassName'], $fileName);
                $fileName = str_replace('{TABLE_CONTROLLER_NAME}', $tableDefinition['controllerName'], $fileName);

                $this->_createFile($fileName, $code, $this->_override ? true : $canOverride);
            }
        }

    }

}
