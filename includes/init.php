<?php
// Define nav class variables
$home_page = '';
$catalog_page = '';
$add_works_page = '';
$login_page = '';

include_once("includes/db.php");
$db = init_sqlite_db('db/site.sqlite', 'db/init.sql');

include_once("includes/sessions.php");
$session_messages = array();
process_session_params($db, $session_messages);
