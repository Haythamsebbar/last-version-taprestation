<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $this->call(RoleSeeder::class);
        $this->call([
            UsersTableSeeder::class,
            VideoSeeder::class,
            CategoriesTableSeeder::class,
            EquipmentCategoriesTableSeeder::class,
            EquipmentTableSeeder::class,
            SkillsTableSeeder::class,
            ServicesTableSeeder::class,
            AdditionalServicesTableSeeder::class,
            MoreServicesTableSeeder::class,

            UrgentSalesTableSeeder::class,
            OffersTableSeeder::class,
            ReviewsTableSeeder::class,
            NotificationsTableSeeder::class,
            MessagesTableSeeder::class,
            BookingsTableSeeder::class,
        ]);
    }
}
