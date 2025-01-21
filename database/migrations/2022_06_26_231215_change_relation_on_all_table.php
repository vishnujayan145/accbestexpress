<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeRelationOnAllTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // user table
        Schema::table('users', function (Blueprint $table) {
            $table->integer('role_manage_id')->unsigned()->nullable()->change();
            $table->foreign('role_manage_id')->references('id')->on('role_manages');
        });

        // profiles table
        Schema::table('profiles', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users');
        });

        // income_expense_heads
        DB::statement('ALTER TABLE income_expense_heads MODIFY income_expense_type_id INTEGER;');
        DB::statement('ALTER TABLE income_expense_heads MODIFY income_expense_group_id INTEGER;');
        // income_expense_heads
        Schema::table('income_expense_heads', function (Blueprint $table) {
            $table->integer('income_expense_type_id')->nullable()->unsigned()->change();
            $table->integer('income_expense_group_id')->nullable()->unsigned()->change();
            $table->foreign('income_expense_type_id')->references('id')->on('income_expense_types');
            $table->foreign('income_expense_group_id')->references('id')->on('income_expense_groups');
        });

        // transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('branch_id')->nullable()->unsigned()->change();
            $table->integer('income_expense_head_id')->nullable()->unsigned()->change();
            $table->integer('bank_cash_id')->nullable()->unsigned()->change();
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('income_expense_head_id')->references('id')->on('income_expense_heads');
            $table->foreign('bank_cash_id')->references('id')->on('bank_cashes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // user table
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_manage_id']);
        });
        // profiles table
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        // income_expense_heads
        Schema::table('income_expense_heads', function (Blueprint $table) {
            $table->dropForeign(['income_expense_type_id']);
            $table->dropForeign(['income_expense_group_id']);
        });
        // transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['income_expense_head_id']);
            $table->dropForeign(['bank_cash_id']);
        });
    }
}
