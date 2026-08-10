<?php

namespace App\Enums;

enum Service: string
{
    case SYKKELSJEKK = 'Sykkelsjekk';
    case ENKEL_SERVICE = 'Enkel service';
    case SEASONAL = 'Vårklar / høstklar';
    case GRUNDIG_SERVICE = 'Grundig Service';
    case OTHER = 'Annet';

    public function price(): string
    {
        return match ($this) {
            self::SYKKELSJEKK => '349 kr',
            self::ENKEL_SERVICE => '649 kr',
            self::SEASONAL => '799 kr',
            self::GRUNDIG_SERVICE => '1 299 kr',
            self::OTHER => 'Etter avtale',
        };
    }

    public function typicalWork(): string
    {
        return match ($this) {
            self::SYKKELSJEKK => 'Sikkerhetssjekk, dekktrykk, sjekk av skruer, enkel bremse- og girjustering',
            self::ENKEL_SERVICE => 'Grundig sjekk, justere gir/bremser, smøre drivverk, sjekke hjul/lagre/dekk, skruer',
            self::GRUNDIG_SERVICE => 'Enkel service + komplett gjennomgang av lager, hjulretting og helhetlig ettersyn',
            self::SEASONAL => 'Vask, drivverksservice, bremser/gir, dekksjekk, sjekk av skruer',
            self::OTHER => 'Spesifiser i meldingsfeltet hva du ønsker utført på sykkelen',
        };
    }

    public function labelWithPrice(): string
    {
        return sprintf('%s (%s)', $this->value, $this->price());
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->labelWithPrice();
        }

        return $options;
    }
}
