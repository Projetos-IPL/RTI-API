<?php

// Files
defined('ROOTPATH') or define('ROOTPATH', __DIR__ . '/../');

// Http verbs
defined('GET') or define('GET', 'GET');
defined('POST') or define('POST', 'POST');
defined('PUT') or define('PUT', 'PUT');
defined('DELETE') or define('DELETE', 'DELETE');


// Http headers
defined('X_AUTH_TOKEN') or define('X_AUTH_TOKEN', 'X-Auth-Token');

// Entities
defined('ENTITY_NOT_FOUND_INDEX') or define('ENTITY_NOT_FOUND_INDEX', -1);

// Database
defined('DB_HOST') or define('DB_HOST', 'localhost');
defined('DB_DATABASE_NAME') or define('DB_DATABASE_NAME', 'rti');
defined('DB_USER') or define('DB_USER', 'root');
defined('DB_PASSWORD') or define('DB_PASSWORD', '');
defined('DB_CHARSET') or define('DB_CHARSET', 'utf8mb4');

// Event Queue
defined('EQ_ADD_ACTION') or define('EQ_ADD_ACTION', 'ADD');
defined('EQ_REMOVE_ACTION') or define('EQ_REMOVE_ACTION', 'REMOVE');

// Data Entities
defined('ET_SENSOR_LOG') or define('ET_SENSOR_LOG', 'SENSOR_LOGS');
defined('ET_ACTUATOR_LOG') or define('ET_ACTUATOR_LOG', 'ACTUATOR_LOGS');
defined('ET_PEOPLE') or define('ET_PEOPLE', 'PEOPLE');
defined('ET_PERMISSIONS') or define('ET_PERMISSIONS', 'PERMISSIONS');
defined('ET_ENTRANCE_LOGS') or define('ET_ENTRANCE_LOGS', 'ENTRANCE_LOGS');
defined('ET_ENTRANCE_LOG_IMAGES') or define('ET_ENTRANCE_LOG_IMAGES', 'ENTRANCE_LOG_IMAGES');


