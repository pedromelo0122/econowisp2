<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lc_escrow_transactions')) {
            return;
        }

        if (! Schema::hasColumn('lc_escrow_transactions', 'post_id')) {
            Schema::table('lc_escrow_transactions', function (Blueprint $table) {
                $table->unsignedBigInteger('post_id')->nullable()->after('id');
            });
        }

        if (Schema::hasColumn('lc_escrow_transactions', 'post_id')) {
            if (! $this->indexExists('lc_escrow_transactions', 'lc_escrow_transactions_post_id_index')) {
                Schema::table('lc_escrow_transactions', function (Blueprint $table) {
                    $table->index('post_id', 'lc_escrow_transactions_post_id_index');
                });
            }

            if (! $this->foreignKeyExists('lc_escrow_transactions', 'post_id', 'posts')) {
                Schema::table('lc_escrow_transactions', function (Blueprint $table) {
                    $table->foreign('post_id', 'lc_escrow_transactions_post_id_foreign')
                        ->references('id')
                        ->on('posts')
                        ->cascadeOnDelete();
                });
            }
        }

        foreach (['buyer_id', 'seller_id'] as $column) {
            if (! Schema::hasColumn('lc_escrow_transactions', $column)) {
                continue;
            }

            $indexName = "lc_escrow_transactions_{$column}_index";
            if (! $this->indexExists('lc_escrow_transactions', $indexName)) {
                Schema::table('lc_escrow_transactions', function (Blueprint $table) use ($column, $indexName) {
                    $table->index($column, $indexName);
                });
            }

            if (! $this->foreignKeyExists('lc_escrow_transactions', $column, 'users')) {
                $foreignKeyName = "lc_escrow_transactions_{$column}_foreign";

                Schema::table('lc_escrow_transactions', function (Blueprint $table) use ($column, $foreignKeyName) {
                    $table->foreign($column, $foreignKeyName)
                        ->references('id')
                        ->on('users')
                        ->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('lc_escrow_transactions')) {
            return;
        }

        if ($this->foreignKeyExists('lc_escrow_transactions', 'post_id', 'posts')) {
            Schema::table('lc_escrow_transactions', function (Blueprint $table) {
                $table->dropForeign(['post_id']);
            });
        }

        if ($this->indexExists('lc_escrow_transactions', 'lc_escrow_transactions_post_id_index')) {
            Schema::table('lc_escrow_transactions', function (Blueprint $table) {
                $table->dropIndex('lc_escrow_transactions_post_id_index');
            });
        }

        if (Schema::hasColumn('lc_escrow_transactions', 'post_id')) {
            Schema::table('lc_escrow_transactions', function (Blueprint $table) {
                $table->dropColumn('post_id');
            });
        }
    }

    protected function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        return match ($driver) {
            'mysql', 'mariadb' => $this->mysqlIndexExists($table, $index),
            'pgsql' => $this->postgresIndexExists($table, $index),
            'sqlite' => $this->sqliteIndexExists($table, $index),
            default => false,
        };
    }

    protected function foreignKeyExists(string $table, string $column, string $referencedTable): bool
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        return match ($driver) {
            'mysql', 'mariadb' => $this->mysqlForeignKeyExists($table, $column, $referencedTable),
            'pgsql' => $this->postgresForeignKeyExists($table, $column, $referencedTable),
            'sqlite' => $this->sqliteForeignKeyExists($table, $column, $referencedTable),
            default => false,
        };
    }

    protected function mysqlIndexExists(string $table, string $index): bool
    {
        $result = DB::select('SHOW INDEX FROM `'.$table.'` WHERE `Key_name` = ?', [$index]);

        return ! empty($result);
    }

    protected function mysqlForeignKeyExists(string $table, string $column, string $referencedTable): bool
    {
        $database = Schema::getConnection()->getDatabaseName();

        $result = DB::select(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME = ?',
            [$database, $table, $column, $referencedTable]
        );

        return ! empty($result);
    }

    protected function postgresIndexExists(string $table, string $index): bool
    {
        $result = DB::select('SELECT indexname FROM pg_indexes WHERE schemaname = current_schema() AND tablename = ? AND indexname = ?', [$table, $index]);

        return ! empty($result);
    }

    protected function postgresForeignKeyExists(string $table, string $column, string $referencedTable): bool
    {
        $result = DB::select(
            'SELECT tc.constraint_name FROM information_schema.table_constraints AS tc '
            . 'JOIN information_schema.key_column_usage AS kcu ON tc.constraint_name = kcu.constraint_name '
            . 'AND tc.table_schema = kcu.table_schema '
            . 'JOIN information_schema.constraint_column_usage AS ccu ON tc.constraint_name = ccu.constraint_name '
            . 'AND tc.table_schema = ccu.table_schema '
            . "WHERE tc.constraint_type = 'FOREIGN KEY' AND tc.table_schema = current_schema() AND tc.table_name = ? "
            . 'AND kcu.column_name = ? AND ccu.table_name = ?',
            [$table, $column, $referencedTable]
        );

        return ! empty($result);
    }

    protected function sqliteIndexExists(string $table, string $index): bool
    {
        $result = DB::select("PRAGMA index_list('".$table."')");

        foreach ($result as $row) {
            if (($row->name ?? null) === $index) {
                return true;
            }
        }

        return false;
    }

    protected function sqliteForeignKeyExists(string $table, string $column, string $referencedTable): bool
    {
        $result = DB::select("PRAGMA foreign_key_list('".$table."')");

        foreach ($result as $row) {
            if (($row->from ?? null) === $column && ($row->table ?? null) === $referencedTable) {
                return true;
            }
        }

        return false;
    }
};