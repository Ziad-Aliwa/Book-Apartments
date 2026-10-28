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
        Schema::table('apartments', function (Blueprint $table) {
            $table->unsignedSmallInteger('bedrooms')->default(1)->after('price_per_night');
            $table->unsignedSmallInteger('bathrooms')->default(1)->after('bedrooms');
            $table->unsignedSmallInteger('max_guests')->default(2)->after('bathrooms');
            $table->json('amenities')->nullable()->after('max_guests');
            $table->decimal('latitude', 10, 8)->nullable()->after('address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->decimal('weekly_discount', 5, 2)->default(0)->after('price_per_night');
            $table->decimal('monthly_discount', 5, 2)->default(0)->after('weekly_discount');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_code')->unique()->nullable()->after('id');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropColumn([
                'bedrooms',
                'bathrooms',
                'max_guests',
                'amenities',
                'latitude',
                'longitude',
                'weekly_discount',
                'monthly_discount',
            ]);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['booking_code', 'discount_amount']);
        });
    }
};
