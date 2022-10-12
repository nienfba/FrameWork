<?php

/** 
 * @var string|null DEFAULT STORAGE TYPE - database|json|null 
 */
const STORAGE = null;

/** DATABASE CONNEXION INFORMATION */
/** 
 * @var string DSN for PDO
 */
const DB_DSN = 'mysql:host=localhost;dbname=MYDATABASE;charset=utf8';
/** 
 * @var string User for database
 */
const DB_USERNAME = 'root';
/** 
 * @var string Password for database
 */
const DB_PASSWORD = '';



/** PATH VALUES  */
/** 
 * @var string Root directory 
 */
const PATH_ROOT = __DIR__.'/../';
/** 
 * @var string Views directory 
 */
const PATH_VIEWS = PATH_ROOT.'src/Views/';


/** URL VALUES  */
/** 
 * @var string URL of your application 
 */
const URL = 'http://_framework.local/';


/** DEFAULT VALUES  */
/** 
 * @var boolean Use rewriting URL or NOT - true | false
 */
const USE_REWRITE = true;

/** 
 * @var string Default Layout of your application
 */
const DEFAULT_LAYOUT = 'base.phtml';

/** 
 * @var string Default controller
 */
const DEFAULT_CONTROLLER = 'front';

/** 
 * @var string Default action in called controller or in default controller
 */
const DEFAULT_ACTION = 'home';