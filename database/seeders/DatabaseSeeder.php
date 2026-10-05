<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CompanySeeder::class);

        // Storefront content: categories before products (products require
        // a category_id), the rest are independent of one another.
        $this->call(CategorySeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(FeatureSeeder::class);
        $this->call(PersonaSeeder::class);
        $this->call(CarouselItemSeeder::class);
        $this->call(SponsorSeeder::class);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $adminPassword = env('ADMIN_SEED_PASSWORD');

        if (! $adminPassword) {
            if (! app()->environment('local', 'testing')) {
                throw new \RuntimeException(
                    'Set ADMIN_SEED_PASSWORD before seeding outside local/testing — refusing to create an admin account with a default password.'
                );
            }

            $adminPassword = 'password';
        }

        // 'role' is deliberately not mass-assigned here (and isn't in
        // User::$fillable at all): set it directly so an admin account can
        // only ever be created by code that explicitly means to, never by
        // a form/request array that happens to include a "role" key.
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => $adminPassword,
        ]);
        $admin->role = 'admin';
        $admin->save();
    }
}
