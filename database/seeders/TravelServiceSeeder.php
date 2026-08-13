<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

class TravelServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'travel')->firstOrFail()->id;

        $this->seedService($id, 'Flight Booking Assistance', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Departure City', 'field_key' => 'departure_city', 'field_type' => 'text'],
            ['label' => 'Destination City', 'field_key' => 'destination_city', 'field_type' => 'text'],
            ['label' => 'Departure Date', 'field_key' => 'departure_date', 'field_type' => 'date'],
            ['label' => 'Return Date (if round trip)', 'field_key' => 'return_date', 'field_type' => 'date', 'is_required' => false],
            ['label' => 'Number of Passengers', 'field_key' => 'passengers', 'field_type' => 'number'],
            ['label' => 'Preferred Airline', 'field_key' => 'preferred_airline', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Special Requests', 'field_key' => 'special_requests', 'field_type' => 'textarea', 'is_required' => false],
        ], sortOrder: 1);

        $this->seedService($id, 'Hotel/Vacation Rental Booking', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'City/Area', 'field_key' => 'city_area', 'field_type' => 'text'],
            ['label' => 'Check-in Date', 'field_key' => 'check_in_date', 'field_type' => 'date'],
            ['label' => 'Check-out Date', 'field_key' => 'check_out_date', 'field_type' => 'date'],
            ['label' => 'Number of Guests', 'field_key' => 'guests', 'field_type' => 'number'],
            ['label' => 'Number of Rooms', 'field_key' => 'rooms', 'field_type' => 'number'],
            ['label' => 'Preferred Hotel/Accommodation Type', 'field_key' => 'accommodation_type', 'field_type' => 'select', 'options' => ['Luxury', 'Mid-range', 'Budget', 'Apartment', 'Hostel']],
            ['label' => 'Special Requests', 'field_key' => 'special_requests', 'field_type' => 'textarea', 'is_required' => false],
        ], sortOrder: 2);

        $this->seedService($id, 'Travel Itinerary Planning', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Destinations', 'field_key' => 'destinations', 'field_type' => 'textarea'],
            ['label' => 'Trip Duration (Days)', 'field_key' => 'trip_duration', 'field_type' => 'number'],
            ['label' => 'Travel Dates', 'field_key' => 'travel_dates', 'field_type' => 'text'],
            ['label' => 'Interests/Preferences', 'field_key' => 'interests', 'field_type' => 'textarea'],
            ['label' => 'Budget Range', 'field_key' => 'budget', 'field_type' => 'select', 'options' => ['Low', 'Medium', 'High', 'Luxury']],
        ], sortOrder: 3);
    }
}