<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    public const SUBJECTS = [
        'bike' => 'Jeg ser etter en sykkel',
        'service' => 'Service eller reparasjon',
        'sell' => 'Selge eller gi bort en sykkel',
        'other' => 'Annet',
    ];

    protected $fillable = ['name', 'contact', 'subject', 'message', 'consent', 'ip_hash', 'read_at'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'consent' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    public function subjectLabel(): string
    {
        return self::SUBJECTS[$this->subject] ?? $this->subject;
    }
}
