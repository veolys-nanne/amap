<?php
exec ('rm -rf ../var/cache/dev/*', $output);
var_dump($output);
exec ('rm -rf ../var/cache/test/*', $output);
var_dump($output);
exec ('rm -rf ../var/cache/prod/*', $output);
var_dump($output);

