<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Modification;
use App\Models\User;
use App\Models\Event;
use App\Models\Track;
use App\Models\Organizer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $car = Car::factory()->create([
            'user_id' => $user->id,
        ]);

        Modification::factory()->count(10)->create([
            'car_id' => $car->id,
        ]);

        $car = Car::factory()->count(15)->create([
            'user_id' => $user->id,
        ]);

        $tracks = [
            [
                'name' => 'Redbull Ring',
                'city' => 'Spielberg',
                'country' => 'Austria',
                'latitude' => 47.2196684,
                'longitude' => 14.7625382,
            ],
            [
                'name' => 'Salzburgring',
                'city' => 'Salzburg',
                'country' => 'Austria',
                'latitude' => 47.8239586,
                'longitude' => 13.1711037,
            ],
            [
                'name' => 'Spa-Francorchamps',
                'city' => 'Spa',
                'country' => 'Belgium',
                'latitude' => 50.4436823,
                'longitude' => 5.9635863,
            ],
            [
                'name' => 'Zolder',
                'city' => 'Hasselt',
                'country' => 'Belgium',
                'latitude' => 50.9111168,
                'longitude' => 5.3917604,
            ],
            [
                'name' => 'Brno Circuit',
                'city' => 'Brno',
                'country' => 'Czech Republic',
                'latitude' => 49.2035779,
                'longitude' => 16.4415533,
            ],
            [
                'name' => 'Autodrom Most',
                'city' => 'Most',
                'country' => 'Czech Republic',
                'latitude' => 50.5197716,
                'longitude' => 13.6036485,
            ],
            [
                'name' => 'Kymi Ring',
                'city' => 'Kouvola',
                'country' => 'Finland',
                'latitude' => 60.8821414,
                'longitude' => 26.4830706,
            ],
            [
                'name' => 'Circuit de la Sarthe',
                'city' => 'Le Mans',
                'country' => 'France',
                'latitude' => 47.9559971,
                'longitude' => 0.2054022,
            ],
            [
                'name' => 'Dijon-Prenois',
                'city' => 'Dijon',
                'country' => 'France',
                'latitude' => 47.3638821,
                'longitude' => 4.8965674,
            ],
            [
                'name' => 'Lédenon',
                'city' => 'Lédenon',
                'country' => 'France',
                'latitude' => 43.9038534,
                'longitude' => 4.5047975,
            ],
            [
                'name' => 'Nevers Magny-Cours',
                'city' => 'Magny-Cours',
                'country' => 'France',
                'latitude' => 46.863809,
                'longitude' => 3.1599929,
            ],
            [
                'name' => 'Circuit Paul Armagnac',
                'city' => 'Nogaro',
                'country' => 'France',
                'latitude' => 43.7619546,
                'longitude' => -0.0424369,
            ],
            [
                'name' => 'Paul Ricard',
                'city' => 'Le Castellet',
                'country' => 'France',
                'latitude' => 43.2514602,
                'longitude' => 5.7907357,
            ],
            [
                'name' => 'Bilster Berg',
                'city' => 'Bad Driburg',
                'country' => 'Germany',
                'latitude' => 51.792207,
                'longitude' => 9.0680637,
            ],
            [
                'name' => 'Hockenheimring',
                'city' => 'Hockenheim',
                'country' => 'Germany',
                'latitude' => 49.3271494,
                'longitude' => 8.562912,
            ],
            [
                'name' => 'Nürburgring Nordschleife',
                'city' => 'Nürburg',
                'country' => 'Germany',
                'latitude' => 50.3331596,
                'longitude' => 6.9432176,
            ],
            [
                'name' => 'Nürburgring GP',
                'city' => 'Nürburg',
                'country' => 'Germany',
                'latitude' => 50.3331596,
                'longitude' => 6.9432176,
            ],
            [
                'name' => 'Motorsport ArenaOschersleben',
                'city' => 'Oschersleben',
                'country' => 'Germany',
                'latitude' => 52.0257347,
                'longitude' => 11.2754487,
            ],
            [   
                'name' => 'Sachsenring',
                'city' => 'Oberlungwitz',
                'country' => 'Germany',
                'latitude' => 51.0177004,
                'longitude' => 13.1331111,
            ],
            [
                'name' => 'Hungaroring',
                'city' => 'Budapest',
                'country' => 'Hungary',
                'latitude' => 47.5797591,
                'longitude' => 19.2448411,
            ],
            [
                'name' => 'Balaton Park',
                'city' => 'Balaton',
                'country' => 'Hungary',
                'latitude' => 47.0076137,
                'longitude' => 18.1981147,
            ],
            [
                'name' => 'Vallelunga',
                'city' => 'Vallelunga',
                'country' => 'Italy',
                'latitude' => 42.1582919,
                'longitude' => 12.3670665,
            ],
            [
                'name' => 'Imola',
                'city' => 'Imola',
                'country' => 'Italy',
                'latitude' => 44.3443901,
                'longitude' => 11.7130472,
            ],
            [
                'name' => 'Monza',
                'city' => 'Monza',
                'country' => 'Italy',
                'latitude' => 45.6166791,
                'longitude' => 9.2809217,
            ],
            [
                'name' => 'Cremona',
                'city' => 'Cremona',
                'country' => 'Italy',
                'latitude' => 45.085851,
                'longitude' => 10.3102585,
            ],
            [
                'name' => 'Mugello',
                'city' => 'Mugello',
                'country' => 'Italy',
                'latitude' => 43.9910059,
                'longitude' => 11.3677383,
            ],
            [
                'name' => 'Misano',
                'city' => 'Misano',
                'country' => 'Italy',
                'latitude' => 43.9636153,
                'longitude' => 12.6831311,
            ],
            [
                'name' => 'Zandvoort Circuit',
                'city' => 'Zandvoort',
                'country' => 'Netherlands',
                'latitude' => 52.3842476,
                'longitude' => 4.5319059,
            ],
            [
                'name' => 'TT Assen Circuit',
                'city' => 'Assen',
                'country' => 'Netherlands',
                'latitude' => 52.9583015,
                'longitude' => 6.5197671,
            ],
            [
                'name' => 'Autodromo Internacional do Algarve',
                'city' => 'Portimao',
                'country' => 'Portugal',
                'latitude' => 37.2315321,
                'longitude' => -8.6327661,
            ],
            [
                'name' => 'Circuito Estoril',
                'city' => 'Estoril',
                'country' => 'Portugal',
                'latitude' => 38.7490973,
                'longitude' => -9.3960739,
            ],
            [
                'name' => 'Circuiton Vasco Sameiro',
                'city' => 'Braga',
                'country' => 'Portugal',
                'latitude' => 41.5888631,
                'longitude' => -8.4497546,
            ],
            [
                'name' => 'Circuito do Sol',
                'city' => 'Beja',
                'country' => 'Portugal',
                'latitude' => 37.8932381,
                'longitude' => -7.3451084,
            ],
            [
                'name' => 'Slovakia Ring',
                'city' => 'Bratislava',
                'country' => 'Slovakia',
                'latitude' => 48.0542578,
                'longitude' => 17.5670629,
            ],
            [
                'name' => 'Ascari',
                'city' => 'Ascari',
                'country' => 'Spain',
                'latitude' => 36.6899127,
                'longitude' => -4.9306645,
            ],
            [
                'name' => 'Circuito de Jerez',
                'city' => 'Jerez',
                'country' => 'Spain',
                'latitude' => 36.7104122,
                'longitude' => -6.0354474,
            ],
            [
                'name' => 'Circuito de Barcelona-Catalunya',
                'city' => 'Barcelona',
                'country' => 'Spain',
                'latitude' => 41.5696659,
                'longitude' => 2.2553006,
            ],
            [
                'name' => 'Circuito Ricardo Tormo',
                'city' => 'Valencia',
                'country' => 'Spain',
                'latitude' => 39.329907,
                'longitude' => -0.5221664,
            ],
            [
                'name' => 'Jarama',
                'city' => 'Madrid',
                'country' => 'Spain',
                'latitude' => 40.615988,
                'longitude' => -3.5866224,
            ],
            [
                'name' => 'Circuito de Navarra',
                'city' => 'Navarra',
                'country' => 'Spain',
                'latitude' => 42.5580902,
                'longitude' => -2.1694478,
            ],
            [
                'name' => 'Circuito de Almeria',
                'city' => 'Almeria',
                'country' => 'Spain',
                'latitude' => 37.050541,
                'longitude' => -2.0749703,
            ],
            [
                'name' => 'Motorland Aragón',
                'city' => 'Teruel',
                'country' => 'Spain',
                'latitude' => 41.0803413,
                'longitude' => -0.2069449,
            ],
            [
                'name' => 'Brands Hatch',
                'city' => 'West Kingsdown',
                'country' => 'United Kingdom',
                'latitude' => 51.3611831,
                'longitude' => 0.2601193,
            ],
            [
                'name' => 'Donington Park',
                'city' => 'Donington',
                'country' => 'United Kingdom',
                'latitude' => 52.8293722,
                'longitude' => -1.3841593,
            ],
            [
                'name' => 'Silverstone Circuit',
                'city' => 'Silverstone',
                'country' => 'United Kingdom',
                'latitude' => 52.0376804,
                'longitude' => -1.0398188,
            ],
        ];

        // Create tracks from the predefined list
        $createdTracks = collect($tracks)->map(function ($trackData) {
            return Track::factory()->create($trackData);
        });

        // Create some organizers
        $organizers = Organizer::factory()->count(8)->create();

        // Create events using existing tracks and organizers
        $trackIds = $createdTracks->pluck('id')->toArray();
        $organizerIds = $organizers->pluck('id')->toArray();

        // Create 50 events with random track and organizer assignments
        for ($i = 0; $i < 50; $i++) {
            Event::factory()->create([
                'track_id' => fake()->randomElement($trackIds),
                'organizer_id' => fake()->randomElement($organizerIds),
            ]);
        }
    }
}
