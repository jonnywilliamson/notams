<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //We won't actually create this database table because this database comes from another
    //source - ie the FAA. This is here so that the IDE can pickup all the fields that will
    //be available to us.
    public function up(): void
    {
        if (app()->environment('ignoreme')) {
            Schema::create('notams', function (Blueprint $table) {
                $table->id('fnsid');
                $table->integer('correlationid')->nullable();
                $table->timestamp('issuedtimestamp')->nullable();
                $table->timestamp('storedtimestamp')->nullable();
                $table->timestamp('updatedtimestamp')->nullable();
                $table->timestamp('validfromtimestamp')->nullable();
                $table->timestamp('validtotimestamp')->nullable();
                $table->string('classification')->nullable();
                $table->string('locationdesignator')->nullable();
                $table->string('notamaccountability')->nullable();
                $table->text('notamtext')->nullable();
                $table->longText('aixmnotammessage')->nullable();
                $table->string('status')->nullable();
            });
        }
    }
};
