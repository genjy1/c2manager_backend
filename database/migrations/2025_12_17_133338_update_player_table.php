<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::table('players', function (Blueprint $table) {
            //
            $table->string('role')->nullable();
            $table->bigInteger('current_team_id')->unsigned()->nullable()->after('id');
            $table->foreign('current_team_id')->references('id')->on('teams');
            $table->integer('Firepower')->default(0);
            $table->integer('Entrying')->default(0);
            $table->integer('Trading')->default(0);
            $table->integer('Opening')->default(0);
            $table->integer('Clutching')->default(0);
            $table->integer('Sniping')->default(0);
            $table->integer('Utility')->default(0);
            $table->float('price')->default(0);
            $table->float('kpr')->default(0.00);
            $table->float('dpr')->default(0.00);
            $table->integer('maps')->default(0);
            $table->dateTime('contract_ends_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
