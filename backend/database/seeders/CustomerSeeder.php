<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CustomerProfile;
use App\Models\CustomerAddress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Budi Santoso',
                'email' => 'customer1@kelloggs.test',
                'phone' => '081234567801',
                'gender' => 'male',
                'membership' => 'regular',
            ],
            [
                'name' => 'Siti Rahma',
                'email' => 'customer2@kelloggs.test',
                'phone' => '081234567802',
                'gender' => 'female',
                'membership' => 'silver',
            ],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'customer3@kelloggs.test',
                'phone' => '081234567803',
                'gender' => 'male',
                'membership' => 'gold',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'customer4@kelloggs.test',
                'phone' => '081234567804',
                'gender' => 'female',
                'membership' => 'platinum',
            ],
            [
                'name' => 'Rizky Pratama',
                'email' => 'customer5@kelloggs.test',
                'phone' => '081234567805',
                'gender' => 'male',
                'membership' => 'regular',
            ],
        ];

        foreach ($customers as $index => $customer) {

            $user = User::firstOrCreate(
                [
                    'email' => $customer['email'],
                ],
                [
                    'name' => $customer['name'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            $user->syncRoles([
                User::ROLE_CUSTOMER
            ]);

            $profile = CustomerProfile::updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'customer_code' => 'CUS-' . str_pad(
                        $index + 1,
                        5,
                        '0',
                        STR_PAD_LEFT
                    ),

                    'full_name' => $customer['name'],
                    'phone' => $customer['phone'],
                    'gender' => $customer['gender'],

                    'birth_date' => now()
                        ->subYears(rand(20, 35))
                        ->toDateString(),

                    'membership_level' => $customer['membership'],

                    'total_points' => 0,
                    'total_spent' => 0,
                    'total_orders' => 0,

                    'is_active' => true,

                    'email_subscribed' => true,
                    'sms_subscribed' => false,
                    'push_subscribed' => true,
                ]
            );

            CustomerAddress::updateOrCreate(
                [
                    'customer_profile_id' => $profile->id,
                    'label' => 'Rumah',
                ],
                [
                    'recipient_name' => $customer['name'],
                    'recipient_phone' => $customer['phone'],

                    'address' => 'Jl. Contoh Alamat No. ' . ($index + 1),

                    'province' => 'Jawa Tengah',
                    'city' => 'Purwokerto',
                    'district' => 'Purwokerto Timur',
                    'subdistrict' => 'Arcawinangun',
                    'postal_code' => '53113',

                    'is_default' => true,
                    'is_active' => true,

                    'notes' => 'Alamat Seeder',
                ]
            );
        }
    }
}