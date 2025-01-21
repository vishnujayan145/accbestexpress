<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCodeOnIncomeExpenseGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('income_expense_groups', function (Blueprint $table) {
            $table->dropUnique(['code']);
        });
        Schema::table('income_expense_groups', function (Blueprint $table) {
            $table->renameColumn('code', 'description');
        });
        Schema::table('income_expense_groups', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('income_expense_groups', function (Blueprint $table) {
            $table->renameColumn('description', 'code');
        });
    }
}
