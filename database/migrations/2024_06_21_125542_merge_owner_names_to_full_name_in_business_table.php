<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MergeOwnerNamesToFullNameInBusinessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {
            // Add new column for owner_full_name
            $table->string('owner_full_name')->nullable()->after('name');
        });

        // Copy data from owner_first_name and owner_last_name to owner_full_name
        DB::table('business')->get()->each(function ($business) {
            $fullName = trim($business->owner_first_name . ' ' . $business->owner_last_name);
            DB::table('business')->where('id', $business->id)->update(['owner_full_name' => $fullName]);
        });

        // Drop old columns owner_first_name and owner_last_name
        Schema::table('business', function (Blueprint $table) {
            $table->dropColumn(['owner_first_name', 'owner_last_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business', function (Blueprint $table) {
            // Reverse the changes: recreate columns owner_first_name and owner_last_name
            $table->string('owner_first_name')->nullable()->after('name');
            $table->string('owner_last_name')->nullable()->after('owner_first_name');
        });

        // Copy data back from owner_full_name to owner_first_name and owner_last_name
        DB::table('business')->get()->each(function ($business) {
            $names = explode(' ', $business->owner_full_name, 2);
            $firstName = $names[0] ?? null;
            $lastName = $names[1] ?? null;
            DB::table('business')->where('id', $business->id)->update([
                'owner_first_name' => $firstName,
                'owner_last_name' => $lastName,
            ]);
        });

        // Drop the owner_full_name column
        Schema::table('business', function (Blueprint $table) {
            $table->dropColumn('owner_full_name');
        });
    }
}
