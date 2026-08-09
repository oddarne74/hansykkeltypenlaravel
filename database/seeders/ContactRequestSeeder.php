<?php

namespace Database\Seeders;

use App\Models\ContactRequest;
use Illuminate\Database\Seeder;

class ContactRequestSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->requests() as $data) {
            $createdAt = $data['created_at'];
            unset($data['created_at']);

            $request = ContactRequest::updateOrCreate(
                ['name' => $data['name'], 'contact' => $data['contact'], 'subject' => $data['subject']],
                $data,
            );

            $request->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->saveQuietly();
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function requests(): array
    {
        return [
            [
                'name' => 'Kari Nordmann',
                'contact' => 'kari.nordmann@example.com',
                'subject' => 'bike',
                'message' => 'Hei! Jeg ser etter en hybridsykkel til dama i størrelse M. Har dere noe inne nå, eller noe som er på vei inn? Jeg er ca. 170 cm høy.',
                'consent' => true,
                'ip_hash' => hash('sha256', '203.0.113.10'),
                'read_at' => now()->subDays(2),
                'created_at' => now()->subDays(3),
            ],
            [
                'name' => 'Ola Hansen',
                'contact' => '412 34 567',
                'subject' => 'service',
                'message' => 'Sykkelen min girer dårlig og bremsene piper. Kan jeg komme innom med den i helgen for en gjennomgang? Hva koster en vanlig service?',
                'consent' => true,
                'ip_hash' => hash('sha256', '203.0.113.24'),
                'read_at' => now()->subDay(),
                'created_at' => now()->subDays(2),
            ],
            [
                'name' => 'Silje Pedersen',
                'contact' => 'silje.p@example.com',
                'subject' => 'sell',
                'message' => 'Vi har to barnesykler stående i garasjen som ungene har vokst ut av. De trenger litt kjærlighet, men er ellers hele. Er dere interessert i å overta dem?',
                'consent' => true,
                'ip_hash' => hash('sha256', '198.51.100.7'),
                'read_at' => null,
                'created_at' => now()->subHours(20),
            ],
            [
                'name' => 'Per Johansen',
                'contact' => 'per.johansen@example.com',
                'subject' => 'bike',
                'message' => 'Er DBS Classic fortsatt reservert, eller er den ledig igjen? Gi gjerne beskjed hvis den blir tilgjengelig – jeg kan hente i Bodø på kort varsel.',
                'consent' => true,
                'ip_hash' => hash('sha256', '198.51.100.42'),
                'read_at' => null,
                'created_at' => now()->subHours(5),
            ],
            [
                'name' => 'Anne Berg',
                'contact' => '976 54 321',
                'subject' => 'other',
                'message' => 'Hei! Driver dere med utleie av sykler også, eller kun salg og service? Vi får besøk i sommer og trenger to sykler i en ukes tid.',
                'consent' => true,
                'ip_hash' => hash('sha256', '192.0.2.55'),
                'read_at' => null,
                'created_at' => now()->subHour(),
            ],
        ];
    }
}
