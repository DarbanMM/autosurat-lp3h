<?php

namespace App\Database;

use Illuminate\Database\Connectors\PostgresConnector as BasePostgresConnector;
use PDO;

class PostgresConnector extends BasePostgresConnector
{
    public function configureSearchPath(PDO $connection, array $config)
    {
        // Skip configuring search path for PgBouncer compatibility
        // PgBouncer doesn't support prepared statements
    }
}
