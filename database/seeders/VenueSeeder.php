<?php<?php



namespace Database\Seeders;namespace Database\Seeders;



use Illuminate\Database\Seeder;use Illuminate\Database\Seeder;

use App\Models\Venue;use App\Models\Venue;

use App\Models\League;use App\Models\League;



class VenueSeeder extends Seederclass VenueSeeder extends Seeder

{{

    public function run(): void    public function run(): void

    {    {

        $leagues = League::all();        $leagues = League::all();



        foreach ($leagues as $league) {        foreach ($leagues as $league) {

            // Crear 2-3 canchas por liga            // Crear 2-3 canchas por liga

            Venue::create([            Venue::create([

                'league_id' => $league->id,                'league_id' => $league->id,

                'name' => 'Estadio Principal ' . $league->name,                'name' => 'Estadio Principal ' . $league->name,

                'address' => 'Av. Principal 123',                'address' => 'Av. Principal 123',

                'city' => 'Ciudad Central',                'city' => 'Ciudad Central',

                'capacity' => 5000,                'capacity' => 5000,

                'rental_cost' => 500.00,                'rental_cost' => 500.00,

                'contact_name' => 'Juan Pérez',                'contact_name' => 'Juan Pérez',

                'contact_phone' => '555-1234',                'contact_phone' => '555-1234',

                'contact_email' => 'contacto@estadio.com',                'contact_email' => 'contacto@estadio.com',

                'facilities' => 'Vestidores, Iluminación, Estacionamiento',                'facilities' => 'Vestidores, Iluminación, Estacionamiento',

                'is_active' => true,                'is_active' => true,

            ]);            ]);



            Venue::create([            Venue::create([

                'league_id' => $league->id,                'league_id' => $league->id,

                'name' => 'Cancha Municipal',                'name' => 'Cancha Municipal',

                'address' => 'Calle Deportes 456',                'address' => 'Calle Deportes 456',

                'city' => 'Ciudad Central',                'city' => 'Ciudad Central',

                'capacity' => 2000,                'capacity' => 2000,

                'rental_cost' => 300.00,                'rental_cost' => 300.00,

                'contact_name' => 'María González',                'contact_name' => 'María González',

                'contact_phone' => '555-5678',                'contact_phone' => '555-5678',

                'is_active' => true,                'is_active' => true,

            ]);            ]);

        }        }

    }    }

}}

atabase\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
    }
}
