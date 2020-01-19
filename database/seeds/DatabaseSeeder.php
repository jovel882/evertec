<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $tableNames = config('permission.table_names');
        if (app()->env != "testing") {
            $this->truncateTables([
                "transaction_states",
                "transactions",
                "orders",
                $tableNames['role_has_permissions'],
                $tableNames['model_has_roles'],
                $tableNames['model_has_permissions'],
                $tableNames['roles'],
                $tableNames['permissions'],
                "users",
            ]);
        }
        $this->call(PermissionsSeeder::class);
        $this->call(ProfilesSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(OrdersSeeder::class);
        $this->call(TransactionsSeeder::class);
        $this->call(TransactionStatesSeeder::class);
    }
    /**
     * Trunca todas las tablas enviadas en el array
     * @param array $tables Array con los nombres de las tablas a truncar.
     * @return void.
     */
    protected function truncateTables(array $tables):void
    {
        DB::statement("SET FOREIGN_KEY_CHECKS=0");
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
        DB::statement("SET FOREIGN_KEY_CHECKS=1");
    }
}
