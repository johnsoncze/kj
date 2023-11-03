<?php

/**
 * @return string
 */
function getDBConfigPath() : string
{
    $name = getenv("CIRCLECI") ? 'circleci' : 'local';
    return __DIR__ . sprintf('/config/config.%s.neon', $name);
}