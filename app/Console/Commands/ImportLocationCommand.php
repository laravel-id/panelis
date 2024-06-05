<?php

namespace App\Console\Commands;

use App\Models\Location\Country;
use App\Models\Location\District;
use App\Models\Location\Region;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportLocationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-location';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import locations data (Indonesia) bey fetching from 3rd party API';

    private function getCountry(): Country
    {
        $country = Country::firstOrNew(['name' => 'Indonesia']);
        $country->fill([
            'alpha2' => 'ID',
            'alpha3' => 'IDN',
            'un_code' => '360',
        ]);
        $country->save();

        return $country;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $specialRegions = ['DI YOGYAKARTA', 'DKI JAKARTA'];
        $regions = Http::get('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
        if ($regions->successful()) {
            $country = $this->getCountry();

            $total = count($regions->json());
            $this->info(sprintf('Fetching all districts from total %d regions.', $total));
            $bar = $this->output->createProgressBar($total);

            $bar->start();
            foreach ($regions->json() as $region) {
                if (in_array($region['name'], $specialRegions)) {
                    [$special, $regionName] = explode(' ', $region['name']);
                    $regionName = sprintf('%s %s', $special, str($regionName)->title()->toString());
                } else {
                    $regionName = str($region['name'])->title()->toString();
                }

                $storedRegion = Region::FirstOrCreate([
                    'country_id' => $country->id,
                    'name' => $regionName,
                ]);

                // fetch district based on region
                $districts = Http::get(sprintf('https://www.emsifa.com/api-wilayah-indonesia/api/regencies/%s.json', $region['id']));
                if ($districts->successful()) {
                    foreach ($districts->json() as $district) {
                        District::firstOrCreate([
                            'region_id' => $storedRegion->id,
                            'name' => str($district['name'])->title()->toString(),
                        ]);
                    }
                }
                $bar->advance();
            }
            $bar->finish();
        } else {
            $this->error('Failed to fetch region data from API.');
        }
    }
}
