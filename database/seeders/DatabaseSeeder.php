<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            RoleSeeder::class,
            PlanSeeder::class,
        ]);
        $this->call(Cat001TableSeeder::class);
        $this->call(Cat002TableSeeder::class);
        $this->call(Cat003TableSeeder::class);
        $this->call(Cat004TableSeeder::class);
        $this->call(Cat005TableSeeder::class);
        $this->call(Cat006TableSeeder::class);
        $this->call(Cat007TableSeeder::class);
        $this->call(Cat009TableSeeder::class);
        $this->call(Cat010TableSeeder::class);
        $this->call(Cat011TableSeeder::class);
        $this->call(Cat012TableSeeder::class);
        $this->call(Cat013TableSeeder::class);
        $this->call(Cat014TableSeeder::class);
        $this->call(Cat015TableSeeder::class);
        $this->call(Cat016TableSeeder::class);
        $this->call(Cat017TableSeeder::class);
        $this->call(Cat018TableSeeder::class);
        $this->call(Cat019TableSeeder::class);
        $this->call(Cat020TableSeeder::class);
        $this->call(Cat021TableSeeder::class);
        $this->call(Cat022TableSeeder::class);
        $this->call(Cat023TableSeeder::class);
        $this->call(Cat024TableSeeder::class);
        $this->call(Cat025TableSeeder::class);
        $this->call(Cat026TableSeeder::class);
        $this->call(Cat027TableSeeder::class);
        $this->call(Cat028TableSeeder::class);
        $this->call(Cat029TableSeeder::class);
        $this->call(Cat030TableSeeder::class);
        $this->call(Cat031TableSeeder::class);
        $this->call(Cat032TableSeeder::class);
        $this->call(TributesSeeder::class);
    }
}
