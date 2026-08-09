<?php

namespace Database\Seeders;

use App\Enums\BikeSize;
use App\Enums\BikeStatus;
use App\Enums\BikeType;
use App\Models\Bike;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BikeSeeder extends Seeder
{
    /** Total number of bikes the seeder should produce, including the curated showcase bikes. */
    private const TOTAL_BIKES = 50;

    public function run(): void
    {
        foreach ($this->bikes() as $data) {
            $images = $data['images'];
            $work = $data['work'];
            unset($data['images'], $data['work']);

            $bike = Bike::updateOrCreate(['slug' => $data['slug']], $data);
            $bike->images()->delete();
            $bike->workItems()->delete();
            $bike->images()->createMany($images);
            $bike->workItems()->createMany($work);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function bikes(): array
    {
        $curated = $this->curatedBikes();

        return [...$curated, ...$this->generatedBikes(self::TOTAL_BIKES - count($curated))];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function curatedBikes(): array
    {
        return [
            [
                'name' => 'Trek 7200',
                'slug' => 'trek-7200',
                'brand' => 'Trek',
                'model' => '7200',
                'type' => 'Hybridsykkel',
                'price' => 2990,
                'status' => BikeStatus::FOR_SALE->value,
                'size' => 'M',
                'rider_height' => 'ca. 165–178 cm',
                'wheel_size' => '28 tommer',
                'gears' => 'Shimano 3 × 8',
                'frame' => 'Aluminium',
                'brakes' => 'Felgbremser',
                'color' => 'Mørk grønn',
                'year' => 'Ukjent',
                'description' => 'Lett og allsidig hybridsykkel for jobb, skole, asfalt og grus. En solid hverdagssykkel med oppreist og komfortabel sittestilling.',
                'condition_notes' => 'Normale kosmetiske bruksmerker. Ingen kjente funksjonsfeil etter gjennomgang og prøvekjøring.',
                'featured' => true,
                'published_at' => now(),
                'images' => [
                    ['path' => 'images/bikes/trek-7200/after-main.jpg', 'alt' => 'Trek 7200 etter klargjøring', 'stage' => 'after', 'sort_order' => 1],
                    ['path' => 'images/bikes/trek-7200/before-drive.jpg', 'alt' => 'Drivverk før rengjøring', 'stage' => 'before', 'sort_order' => 2],
                    ['path' => 'images/bikes/trek-7200/after-drive.jpg', 'alt' => 'Drivverk etter rengjøring', 'stage' => 'after', 'sort_order' => 3],
                    ['path' => 'images/bikes/trek-7200/before-cockpit.jpg', 'alt' => 'Styre før service', 'stage' => 'before', 'sort_order' => 4],
                    ['path' => 'images/bikes/trek-7200/after-cockpit.jpg', 'alt' => 'Styre etter service', 'stage' => 'after', 'sort_order' => 5],
                ],
                'work' => [
                    ['title' => 'Gir og bremser', 'description' => 'Kontrollert og justert.'],
                    ['title' => 'Drivverk', 'description' => 'Grundig rengjort, smurt og kontrollert for slitasje.'],
                    ['title' => 'Hjul og lager', 'description' => 'Kontrollert og justert ved behov.'],
                    ['title' => 'Sikkerhetskontroll', 'description' => 'Prøvekjørt og sluttkontrollert.'],
                ],
            ],
            [
                'name' => 'Merida Matts 40',
                'slug' => 'merida-matts-40',
                'brand' => 'Merida',
                'model' => 'Matts 40',
                'type' => 'Terrengsykkel',
                'price' => 2490,
                'status' => BikeStatus::FOR_SALE->value,
                'size' => 'L',
                'rider_height' => 'ca. 175–187 cm',
                'wheel_size' => '26 tommer',
                'gears' => 'Shimano 3 × 8',
                'frame' => 'Aluminium',
                'brakes' => 'Skivebremser',
                'color' => 'Sort / sølv',
                'year' => 'Ukjent',
                'description' => 'Robust hardtail til grusvei, tur og variert fritidsbruk.',
                'condition_notes' => 'Pent brukt med noen riper i lakken. Teknisk gjennomgått.',
                'featured' => true,
                'published_at' => now(),
                'images' => [
                    ['path' => 'images/bikes/merida-matts-40/after-main.jpg', 'alt' => 'Merida Matts 40 etter klargjøring', 'stage' => 'after', 'sort_order' => 1],
                ],
                'work' => [
                    ['title' => 'Komplett gjennomgang', 'description' => 'Gir, bremser, drivverk og hjul kontrollert.'],
                ],
            ],
            [
                'name' => 'DBS Classic',
                'slug' => 'dbs-classic',
                'brand' => 'DBS',
                'model' => 'Classic',
                'type' => 'Bysykkel',
                'price' => 3490,
                'status' => BikeStatus::RESERVED->value,
                'size' => 'M/L',
                'rider_height' => 'ca. 168–183 cm',
                'wheel_size' => '28 tommer',
                'gears' => 'Shimano Nexus 7',
                'frame' => 'Stål',
                'brakes' => 'Felg / fotbrems',
                'color' => 'Blå',
                'year' => 'Ukjent',
                'description' => 'Komfortabel bysykkel med skjermer, bagasjebrett og lukket girsystem.',
                'condition_notes' => 'Kosmetiske bruksspor. Klar til hverdagsturer.',
                'featured' => false,
                'published_at' => now(),
                'images' => [
                    ['path' => 'images/bikes/dbs-classic/after-main.jpg', 'alt' => 'DBS Classic etter klargjøring', 'stage' => 'after', 'sort_order' => 1],
                ],
                'work' => [
                    ['title' => 'Sesongklargjøring', 'description' => 'Rengjort, smurt, justert og prøvekjørt.'],
                ],
            ],
        ];
    }

    /**
     * Procedurally generate additional, realistic-looking bikes so the demo catalogue feels populated.
     * A fixed Faker seed keeps the output deterministic, so the seeder stays idempotent across runs.
     *
     * @return array<int, array<string, mixed>>
     */
    private function generatedBikes(int $count): array
    {
        $faker = FakerFactory::create('nb_NO');
        $faker->seed(20260809);

        $bikes = [];

        for ($i = 1; $i <= $count; $i++) {
            [$brand, $model] = $faker->randomElement($this->brandModelPool());
            $type = $faker->randomElement($this->typePool());
            $size = $faker->randomElement(array_keys($this->riderHeightBySize()));
            $status = $faker->randomElement([BikeStatus::FOR_SALE->value, BikeStatus::FOR_SALE->value, BikeStatus::FOR_SALE->value, BikeStatus::FOR_SALE->value, BikeStatus::RESERVED->value, BikeStatus::SOLD->value]);
            $name = "{$brand} {$model}";
            $slug = Str::slug("{$name}-{$i}");

            $bikes[] = [
                'name' => $name,
                'slug' => $slug,
                'brand' => $brand,
                'model' => $model,
                'type' => $type,
                'price' => $faker->numberBetween(15, 80) * 100,
                'status' => $status,
                'size' => $size,
                'rider_height' => $this->riderHeightBySize()[$size],
                'wheel_size' => $faker->randomElement(['20 tommer', '24 tommer', '26 tommer', '27,5 tommer', '28 tommer', '29 tommer']),
                'gears' => $faker->randomElement(['Shimano 3 × 8', 'Shimano 2 × 9', 'Shimano Nexus 7', 'Shimano 1 × 11', 'SRAM 1 × 12', 'Enkel gir']),
                'frame' => $faker->randomElement(['Aluminium', 'Stål', 'Karbon']),
                'brakes' => $faker->randomElement(['Skivebremser', 'Felgbremser', 'Felg / fotbrems']),
                'color' => $faker->randomElement(['Sort', 'Hvit', 'Blå', 'Rød', 'Grønn', 'Grå', 'Sølv', 'Sort / rød', 'Blå / hvit']),
                'year' => $faker->randomElement(['Ukjent', '2019', '2020', '2021', '2022', '2023', '2024']),
                'description' => str_replace('{type}', mb_strtolower($type), $faker->randomElement($this->descriptionPool())),
                'condition_notes' => $faker->randomElement($this->conditionNotesPool()),
                'featured' => $i % 9 === 0,
                'published_at' => now()->subDays($faker->numberBetween(0, 45)),
                'images' => $this->generatedImages($faker, $slug, $name),
                'work' => $faker->randomElements($this->workItemPool(), $faker->numberBetween(1, 3)),
            ];
        }

        return $bikes;
    }

    /**
     * @return array<int, array{path: string, alt: string, stage: string, sort_order: int}>
     */
    private function generatedImages(Generator $faker, string $slug, string $name): array
    {
        $images = [
            ['path' => "images/bikes/{$slug}/after-main.jpg", 'alt' => "{$name} etter klargjøring", 'stage' => 'after', 'sort_order' => 1],
        ];

        if ($faker->boolean(50)) {
            $images[] = ['path' => "images/bikes/{$slug}/after-detail.jpg", 'alt' => "{$name} detaljbilde", 'stage' => 'after', 'sort_order' => 2];
        }

        return $images;
    }

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    private function brandModelPool(): array
    {
        return [
            ['Trek', 'FX 2'], ['Trek', 'Marlin 5'], ['Trek', 'Domane AL 2'],
            ['Merida', 'Crossway'], ['Merida', 'Speeder 100'],
            ['DBS', 'Comfort'], ['DBS', 'Urban'],
            ['Specialized', 'Sirrus'], ['Specialized', 'Rockhopper'], ['Specialized', 'Allez'],
            ['Giant', 'Escape 3'], ['Giant', 'Talon'], ['Giant', 'Contend'],
            ['Scott', 'Sub Cross'], ['Scott', 'Aspect'], ['Scott', 'Speedster'],
            ['Cube', 'Nature'], ['Cube', 'Aim'], ['Cube', 'Attain'],
            ['Kildemoes', 'Herresykkel'], ['Kildemoes', 'Damesykkel'],
            ['Centurion', 'Basic'], ['Centurion', 'Backfire'],
            ['Batavus', 'Fryslan'], ['Batavus', 'Cambridge'],
            ['Winther', 'Kick'], ['Winther', 'Slim'],
            ['Crescent', 'Amalfi'], ['Crescent', 'Rush'],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function typePool(): array
    {
        return array_column(BikeType::cases(), 'value');
    }

    /**
     * @return array<string, string>
     */
    private function riderHeightBySize(): array
    {
        return [
            BikeSize::XS->value => 'ca. 150–160 cm',
            BikeSize::S->value => 'ca. 158–168 cm',
            BikeSize::M->value => 'ca. 165–178 cm',
            BikeSize::L->value => 'ca. 175–187 cm',
            BikeSize::XL->value => 'ca. 185–198 cm',
            BikeSize::ML->value => 'ca. 168–183 cm',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function descriptionPool(): array
    {
        return [
            'En pålitelig {type} som passer godt til hverdagsbruk og pendling.',
            'Godt vedlikeholdt {type} med jevn og komfortabel kjøreegenskap.',
            'Solid {type} i fin stand, ferdig klargjort og klar til bruk.',
            'Praktisk {type} med godt utstyr, perfekt for daglige turer.',
            '{type} med god komfort og stabil kjørefølelse, ideell for by og landevei.',
            'Velholdt {type} som har fått grundig gjennomgang før salg.',
            'Fin {type} med pent design og god funksjon.',
            'Rimelig {type} i god stand, klar for ny eier.',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function conditionNotesPool(): array
    {
        return [
            'Normale kosmetiske bruksmerker. Ingen kjente funksjonsfeil.',
            'Pent brukt med noen få riper i lakken. Teknisk gjennomgått.',
            'God stand for alderen. Fungerer som den skal.',
            'Lettere slitasje på grep og seteputer. Ellers i fin stand.',
            'Nylig service utført. Klar til bruk.',
        ];
    }

    /**
     * @return array<int, array{title: string, description: string}>
     */
    private function workItemPool(): array
    {
        return [
            ['title' => 'Gir og bremser', 'description' => 'Kontrollert og justert.'],
            ['title' => 'Drivverk', 'description' => 'Rengjort og smurt.'],
            ['title' => 'Hjul og lager', 'description' => 'Kontrollert og justert ved behov.'],
            ['title' => 'Sikkerhetskontroll', 'description' => 'Prøvekjørt og sluttkontrollert.'],
            ['title' => 'Dekk og slanger', 'description' => 'Kontrollert lufttrykk og slitasje.'],
            ['title' => 'Sesongklargjøring', 'description' => 'Rengjort, smurt og justert.'],
        ];
    }
}
