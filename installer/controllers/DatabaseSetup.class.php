<?php
/**
 * Installation step: database setup
 *
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers
 */

class DatabaseSetup extends InstallController {
    
    public function index() {
        include_once(dirname(__FILE__)."/../../Config.class.php");
        include_once(dirname(__FILE__)."/../../includes/rb.php");
        
        $queries["errors"] = "CREATE TABLE IF NOT EXISTS `errors` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `time` bigint(20) DEFAULT NULL,
              `user_agent` varchar(255) DEFAULT NULL,
              `url_request` varchar(255) DEFAULT NULL,
              `format` varchar(24) DEFAULT NULL,
              `error_message` text,
              `error_code` varchar(255) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
        
        $queries["generic_resource"] = "CREATE TABLE IF NOT EXISTS `generic_resource` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `resource_id` bigint(20) NOT NULL,
              `type` varchar(40) NOT NULL,
              `documentation` varchar(512) NOT NULL,
              `timestamp` int(11) unsigned DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `resource_id` (`resource_id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
        
        $queries["generic_resource_csv"] = "CREATE TABLE IF NOT EXISTS `generic_resource_csv` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `gen_resource_id` bigint(20) NOT NULL,
              `uri` varchar(512) NOT NULL,
              `has_header_row` tinyint(2) NOT NULL,
              `start_row` int(128) NOT NULL,
              `delimiter` varchar(10) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `gen_resource_id` (`gen_resource_id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
        
        $queries["package"] = "CREATE TABLE IF NOT EXISTS `package` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `package_name` varchar(255) NOT NULL,
              `timestamp` bigint(20) NOT NULL,
              `title` varchar(80) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
        
        $queries["published_columns"] = "CREATE TABLE IF NOT EXISTS `published_columns` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `generic_resource_id` bigint(20) NOT NULL,
              `column_name` varchar(50) NOT NULL,
              `is_primary_key` int(11) DEFAULT NULL,
              `column_name_alias` varchar(50) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `generic_resource_id` (`generic_resource_id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
        
        $queries["remote_resource"] = "CREATE TABLE IF NOT EXISTS `remote_resource` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `resource_id` bigint(20) NOT NULL,
              `package_name` varchar(255) NOT NULL,
              `base_url` varchar(128) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `resource_id` (`resource_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
        
        $queries["requests"] = "CREATE TABLE IF NOT EXISTS `requests` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `time` bigint(20) DEFAULT NULL,
              `user_agent` varchar(255) DEFAULT NULL,
              `url_request` varchar(512) DEFAULT NULL,
              `package` varchar(64) DEFAULT NULL,
              `resource` varchar(64) DEFAULT NULL,
              `format` varchar(24) DEFAULT NULL,
              `subresources` varchar(128) DEFAULT NULL,
              `reqparameters` varchar(128) DEFAULT NULL,
              `allparameters` varchar(164) DEFAULT NULL,
              `requiredparameter` varchar(255) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";    
        
        $queries["resource"] = "CREATE TABLE IF NOT EXISTS `resource` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `resource_name` varchar(255) NOT NULL,
              `package_id` varchar(255) NOT NULL,
              `creation_timestamp` bigint(20) NOT NULL,
              `last_update_timestamp` bigint(20) NOT NULL,
              `type` varchar(30) NOT NULL,
              `tags` varchar(512) DEFAULT NULL,
              `title` varchar(80) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `package_id` (`package_id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
        
        $queries["info"] = "CREATE TABLE IF NOT EXISTS `info` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `value` varchar(255) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `name` (`name`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
        
        $queries["datasets"] = "CREATE TABLE IF NOT EXISTS `datasets` (
              `datasetName` varchar(255) NOT NULL DEFAULT '',
              `defaultModelUri` varchar(255) NOT NULL DEFAULT '0',
              PRIMARY KEY (`datasetName`),
              KEY `datasetName` (`datasetName`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        
        $queries["dataset_model"] = "CREATE TABLE IF NOT EXISTS `dataset_model` (
              `datasetName` varchar(255) NOT NULL DEFAULT '0',
              `modelId` bigint(20) NOT NULL DEFAULT '0',
              `graphURI` varchar(255) NOT NULL DEFAULT '',
              PRIMARY KEY (`modelId`,`datasetName`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        
        $queries["models"] = "CREATE TABLE IF NOT EXISTS `models` (
              `modelID` bigint(20) NOT NULL,
              `modelURI` varchar(255) NOT NULL,
              `baseURI` varchar(255) DEFAULT '',
              PRIMARY KEY (`modelID`),
              UNIQUE KEY `m_modURI_idx` (`modelURI`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        
        $queries["namespaces"] = "CREATE TABLE IF NOT EXISTS `namespaces` (
              `modelID` bigint(20) NOT NULL,
              `namespace` varchar(255) NOT NULL,
              `prefix` varchar(255) NOT NULL,
              PRIMARY KEY (`modelID`,`namespace`),
              KEY `n_mod_idx` (`modelID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        
        $queries["statements"] = "CREATE TABLE IF NOT EXISTS `statements` (
              `modelID` bigint(20) NOT NULL,
              `subject` varchar(255) NOT NULL,
              `predicate` varchar(255) NOT NULL,
              `object` text,
              `l_language` varchar(255) DEFAULT '',
              `l_datatype` varchar(255) DEFAULT '',
              `subject_is` varchar(1) NOT NULL,
              `object_is` varchar(1) NOT NULL,
              KEY `s_mod_idx` (`modelID`),
              KEY `s_sub_pred_idx` (`subject`(200),`predicate`(200)),
              KEY `s_sub_idx` (`subject`(200)),
              KEY `s_pred_idx` (`predicate`(200)),
              KEY `s_obj_idx` (`object`(250)),
              KEY `s_obj_ftidx` (`object`(250))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        


        $tables = array();
        foreach($queries as $table=>$query) {
            $tables[$table] = "failed";
        }
        
        try {
            R::setup(Config::$DB, Config::$DB_USER, Config::$DB_PASSWORD);
            
            foreach($queries as $table=>$query) {
                R::exec($query);
                $tables[$table] = "passed";
            }
            
            if(!$this->installer->installedVersion()) {
                $info = R::dispense("info");
                $info->name = "version";
                $info->value = $this->installer->version();
                R::store($info);
            }
            else {
                $info = R::findOne('info','name=:name LIMIT 1', array(":name"=>"version"));
                $info->value = $this->installer->version();
                R::store($info);
            }
            
            $data["status"] = "passed";
            $data["tables"] = $tables;
        }
        catch(Exception $e) {
            $data["status"] = "failed";
            $data["tables"] = $tables;
            $data["message"] = $e->getMessage();
            $this->installer->nextStep(FALSE);
        }
        
        $this->view("database_setup", $data);
    }
    
}