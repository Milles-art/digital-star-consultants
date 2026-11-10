<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

/**
 * Usafiri na booking.
 */
class TravelServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'travel')->firstOrFail()->id;

        $this->seedService($id, 'Msaada wa Kujiandikisha Ndege', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Mji wa Kuondoka', 'field_key' => 'departure_city', 'field_type' => 'text'],
            ['label' => 'Mji wa Kufika', 'field_key' => 'destination_city', 'field_type' => 'text'],
            ['label' => 'Tarehe ya Kuondoka', 'field_key' => 'departure_date', 'field_type' => 'date'],
            ['label' => 'Tarehe ya Kurudi (kama ni safari ya kurudi)', 'field_key' => 'return_date', 'field_type' => 'date', 'is_required' => false],
            ['label' => 'Idadi ya Abiria', 'field_key' => 'passengers', 'field_type' => 'number'],
            ['label' => 'Kampuni ya Ndege Unayopendelea', 'field_key' => 'preferred_airline', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Maombi Maalum', 'field_key' => 'special_requests', 'field_type' => 'textarea', 'is_required' => false],
        ], sortOrder: 1, slug: 'flight-booking-assistance');

        $this->seedService($id, 'Booking ya Hoteli / Malazi', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Mji / Eneo', 'field_key' => 'city_area', 'field_type' => 'text'],
            ['label' => 'Tarehe ya Kuingia', 'field_key' => 'check_in_date', 'field_type' => 'date'],
            ['label' => 'Tarehe ya Kutoka', 'field_key' => 'check_out_date', 'field_type' => 'date'],
            ['label' => 'Idadi ya Wageni', 'field_key' => 'guests', 'field_type' => 'number'],
            ['label' => 'Idadi ya Chumba', 'field_key' => 'rooms', 'field_type' => 'number'],
            ['label' => 'Aina ya Malazi Unayopendelea', 'field_key' => 'accommodation_type', 'field_type' => 'select', 'options' => ['Luxury', 'Wastani', 'Nafuu', 'Apartment', 'Hostel']],
            ['label' => 'Maombi Maalum', 'field_key' => 'special_requests', 'field_type' => 'textarea', 'is_required' => false],
        ], sortOrder: 2, slug: 'hotelvacation-rental-booking');

        $this->seedService($id, 'Upangaji wa Ratiba ya Safari', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Maeneo Unayotaka Kutembelea', 'field_key' => 'destinations', 'field_type' => 'textarea'],
            ['label' => 'Muda wa Safari (Siku)', 'field_key' => 'trip_duration', 'field_type' => 'number'],
            ['label' => 'Tarehe za Safari', 'field_key' => 'travel_dates', 'field_type' => 'text'],
            ['label' => 'Maslahi / Upendeleo', 'field_key' => 'interests', 'field_type' => 'textarea'],
            ['label' => 'Kiwango cha Bajeti', 'field_key' => 'budget', 'field_type' => 'select', 'options' => ['Ndogo', 'Wastani', 'Juu', 'Luxury']],
        ], sortOrder: 3, slug: 'travel-itinerary-planning');
    }
}
