<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\JobPrioritiesSeeder;
use Database\Seeders\JobStatusSeeder;
use Database\Seeders\TypeMaintenanceSeeder;
use Database\Seeders\TypeCommunication;
use Database\Seeders\UserAdminSeeder;
use Database\Seeders\WorkOrderStatusesSeeder;
use Database\Seeders\statusEquipmentSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        
        $this->call([
            JobStatusSeeder::class,  //Seeder para jobStatus
            JobPrioritiesSeeder::class, //Seeder para jobPriorities
            TypeMaintenanceSeeder::class, //Seeder para jobPriorities
            TypeCommunication::class,//Seeder para typeCommunication
            UserAdminSeeder::class, //Seeder para user
            WorkOrderStatusesSeeder::class, //Seeder para workOrderStatuses
            statusEquipmentSeeder::class
        ]);

    }
}
