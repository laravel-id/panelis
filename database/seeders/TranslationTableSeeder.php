<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

class TranslationTableSeeder extends Seeder
{
    private array $lines = [
        'ui' => [
            'title' => [
                'en' => 'User interface',
                'id' => 'Antar muka',
            ],
            'created_at' => [
                'en' => 'Created at',
                'id' => 'Dibuat pada',
            ],
            'updated_at' => [
                'en' => 'Updated at',
                'id' => 'Diperbarui pada',
            ],
        ],

        'customer' => [
            'title' => [
                'en' => 'Customer',
                'id' => 'Pelanggan',
            ],
            'name' => [
                'en' => 'Name',
                'id' => 'Nama lengkap',
            ],
            'phone' => [
                'en' => 'Mobile number',
                'id' => 'No. HP',
            ],
            'email' => [
                'en' => 'Email',
                'id' => 'Pos-el',
            ],
            'address' => [
                'en' => 'Address',
                'id' => 'Alamat',
            ],
        ],

        'package' => [
            'title' => [
                'en' => 'Package',
                'id' => 'Paket',
            ],
            'name' => [
                'en' => 'Name',
                'id' => 'Nama',
            ],
            'unit' => [
                'en' => 'Unit',
                'id' => 'Unit',
            ],
            'unit_price' => [
                'en' => 'Unit price',
                'id' => 'Harga satuan',
            ],
        ],

        'translation' => [
            'title' => [
                'en' => 'Translation',
                'id' => 'Translasi',
            ],
            'group' => [
                'en' => 'Group',
                'id' => 'Grup',
            ],
            'key' => [
                'en' => 'Key',
                'id' => 'Kunci',
            ],
            'text' => [
                'en' => 'Text',
                'id' => 'Teks',
            ],
            'is_system' => [
                'en' => 'System',
                'id' => 'Sistem',
            ],
            'group_change_warning' => [
                'en' => 'Changing the group data may cause some missing translations',
                'id' => 'Mengubah data grup dapat menyebabkan beberapa terjemahan hilang',
            ],
            'key_change_warning' => [
                'en' => 'Changing the key data may cause some missing translations',
                'id' => 'Mengubah data kunci dapat menyebabkan beberapa terjemahan hilang',
            ],
        ],

        'unit' => [
            'title' => [
                'en' => 'Unit',
                'id' => 'Unit',
            ],
            'name' => [
                'en' => 'Name',
                'id' => 'Nama',
            ],
            'alias' => [
                'en' => 'Alias',
                'id' => 'Alias',
            ],
            'multiplier' => [
                'en' => 'Multiplier',
                'id' => 'Kelipatan',
            ],
            'parent' => [
                'en' => 'Parent unit',
                'id' => 'Unit induk',
            ],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->lines as $group => $keys) {
            foreach ($keys as $key => $text) {
                Translation::updateOrCreate([
                    'group' => $group,
                    'key' => $key,
                ], [
                    'text' => $text,
                    'is_system' => true,
                ]);
            }
        }
    }
}
