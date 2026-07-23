<?php
/**
 * Kept for backward compatibility - all real settings now live in config.php.
 * Every page in this project includes 'db.php', so we simply load config.php
 * here (which sets up $connect) to avoid touching every single file's include.
 */
require_once __DIR__ . '/config.php';
