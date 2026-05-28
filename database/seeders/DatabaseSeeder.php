<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Storage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $provider = Provider::create([
            'name' => 'Tashkent Wholesale Trade LLC',
            'phone' => '+998 71 200 00 01',
            'address' => 'Yashnobod district, Tashkent, Uzbekistan',
        ]);

        $provider2 = Provider::create([
            'name' => 'Samarkand Supply Group',
            'phone' => '+998 66 200 00 02',
            'address' => 'Samarkand city, Uzbekistan',
        ]);

        $storage1 = Storage::create([
            'name' => 'Tashkent Central Warehouse',
            'address' => 'Chilanzar district, Tashkent, Uzbekistan',
        ]);

        $storage2 = Storage::create([
            'name' => 'Samarkand Distribution Warehouse',
            'address' => 'Samarkand city, Uzbekistan',
        ]);

        $beverages = Category::create([
            'name' => 'Beverages',
            'provider_id' => $provider->id,
        ]);

        $confectionery = Category::create([
            'name' => 'Confectionery',
            'provider_id' => $provider2->id,
        ]);

        $household = Category::create([
            'name' => 'Household Goods',
            'provider_id' => $provider->id,
        ]);

        $tea = Category::create([
            'name' => 'Tea',
            'parent_id' => $beverages->id,
        ]);

        $biscuits = Category::create([
            'name' => 'Biscuits',
            'parent_id' => $confectionery->id,
        ]);

        Product::create([
            'name' => 'Green Tea 100g',
            'category_id' => $tea->id,
            'sku' => 'UZ-TEA-100',
        ]);

        Product::create([
            'name' => 'Black Tea 100g',
            'category_id' => $tea->id,
            'sku' => 'UZ-TEA-101',
        ]);

        Product::create([
            'name' => 'Chocolate Biscuits 200g',
            'category_id' => $biscuits->id,
            'sku' => 'UZ-BIS-200',
        ]);

        Product::create([
            'name' => 'Laundry Soap 400g',
            'category_id' => $household->id,
            'sku' => 'UZ-SOAP-400',
        ]);

        Client::create([
            'name' => 'Navoi Mini Market',
            'phone' => '+998 90 123 45 67',
            'address' => 'Navoi city, Uzbekistan',
        ]);

        Client::create([
            'name' => 'Chorsu Trade Shop',
            'phone' => '+998 97 765 43 21',
            'address' => 'Chorsu area, Tashkent, Uzbekistan',
        ]);

        Client::create([
            'name' => 'Andijan Wholesale Point',
            'phone' => '+998 99 555 11 22',
            'address' => 'Andijan city, Uzbekistan',
        ]);
    }
}
