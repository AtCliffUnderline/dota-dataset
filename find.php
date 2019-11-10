<?php
require_once 'vendor/autoload.php';

$kernel = new \App\MatchSearcher();

$kernel->findMatches();