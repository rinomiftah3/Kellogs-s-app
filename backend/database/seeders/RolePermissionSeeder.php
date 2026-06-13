<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    private const GUARD = 'web';

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    private const ROLE_SUPER_ADMIN = 'Super Admin';
    private const ROLE_ADMIN       = 'Admin';
    private const ROLE_STAFF       = 'Staff';

    /*
    |--------------------------------------------------------------------------
    | Permission Matrix
    |--------------------------------------------------------------------------
    */

    private const PERMISSIONS = [

        'dashboard' => [
            'dashboard.view',
        ],

        /*
        |--------------------------------------------------------------------------
        | Foundation
        |--------------------------------------------------------------------------
        */

        'users' => [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
        ],

        'roles' => [
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
        ],

        'permissions' => [
            'permissions.view',
            'permissions.create',
            'permissions.update',
            'permissions.delete',
        ],

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        'categories' => [
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',
        ],

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */

        'products' => [
            'products.view',
            'products.create',
            'products.update',
            'products.delete',
        ],

        'product_reviews' => [
            'product_reviews.view',
            'product_reviews.update',
            'product_reviews.delete',
        ],

        /*
        |--------------------------------------------------------------------------
        | Customers
        |--------------------------------------------------------------------------
        */

        'customers' => [
            'customers.view',
            'customers.create',
            'customers.update',
            'customers.delete',
        ],

        'customer_addresses' => [
            'customer_addresses.view',
            'customer_addresses.update',
        ],

        /*
        |--------------------------------------------------------------------------
        | Inventory
        |--------------------------------------------------------------------------
        */

        'inventories' => [
            'inventories.view',
            'inventories.create',
            'inventories.update',
        ],

        'stock_movements' => [
            'stock_movements.view',
        ],

        'stock_adjustments' => [
            'stock_adjustments.view',
            'stock_adjustments.create',
        ],

        'stock_opnames' => [
            'stock_opnames.view',
            'stock_opnames.create',
            'stock_opnames.update',
        ],

        /*
        |--------------------------------------------------------------------------
        | Cart & Checkout
        |--------------------------------------------------------------------------
        */

        'carts' => [
            'carts.view',
        ],

        'checkouts' => [
            'checkouts.view',
        ],

        /*
        |--------------------------------------------------------------------------
        | Promotions
        |--------------------------------------------------------------------------
        */

        'vouchers' => [
            'vouchers.view',
            'vouchers.create',
            'vouchers.update',
            'vouchers.delete',
        ],

        'promotions' => [
            'promotions.view',
            'promotions.create',
            'promotions.update',
            'promotions.delete',
        ],

        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */

        'orders' => [
            'orders.view',
            'orders.create',
            'orders.update',
            'orders.delete',
        ],

        'order_histories' => [
            'order_histories.view',
        ],

        /*
        |--------------------------------------------------------------------------
        | Payments
        |--------------------------------------------------------------------------
        */

        'payments' => [
            'payments.view',
            'payments.update',
        ],

        'payment_transactions' => [
            'payment_transactions.view',
        ],

        /*
        |--------------------------------------------------------------------------
        | Shipping
        |--------------------------------------------------------------------------
        */

        'couriers' => [
            'couriers.view',
            'couriers.create',
            'couriers.update',
            'couriers.delete',
        ],

        'shipping_methods' => [
            'shipping_methods.view',
            'shipping_methods.create',
            'shipping_methods.update',
            'shipping_methods.delete',
        ],

        'shipments' => [
            'shipments.view',
            'shipments.update',
        ],

        /*
        |--------------------------------------------------------------------------
        | Loyalty
        |--------------------------------------------------------------------------
        */

        'loyalty_points' => [
            'loyalty_points.view',
            'loyalty_points.update',
        ],

        'point_transactions' => [
            'point_transactions.view',
        ],

        /*
        |--------------------------------------------------------------------------
        | Activity
        |--------------------------------------------------------------------------
        */

        'activity_logs' => [
            'activity_logs.view',
        ],
    ];

    public function run(): void
    {
        $this->resetPermissionCache();

        /*
        |--------------------------------------------------------------------------
        | Create Permissions
        |--------------------------------------------------------------------------
        */

        $allPermissions = collect(
            self::PERMISSIONS
        )->flatten();

        foreach ($allPermissions as $permission) {

            Permission::firstOrCreate([
                'name'       => $permission,
                'guard_name' => self::GUARD,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Create Roles
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::firstOrCreate([
            'name'       => self::ROLE_SUPER_ADMIN,
            'guard_name' => self::GUARD,
        ]);

        $admin = Role::firstOrCreate([
            'name'       => self::ROLE_ADMIN,
            'guard_name' => self::GUARD,
        ]);

        $staff = Role::firstOrCreate([
            'name'       => self::ROLE_STAFF,
            'guard_name' => self::GUARD,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        $superAdmin->syncPermissions(
            Permission::all()
        );

        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        $admin->syncPermissions([

            'dashboard.view',

            'users.view',
            'users.create',
            'users.update',

            'roles.view',
            'roles.create',
            'roles.update',

            'permissions.view',

            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',

            'products.view',
            'products.create',
            'products.update',
            'products.delete',

            'product_reviews.view',
            'product_reviews.update',
            'product_reviews.delete',

            'customers.view',
            'customers.create',
            'customers.update',

            'customer_addresses.view',
            'customer_addresses.update',

            'inventories.view',
            'inventories.create',
            'inventories.update',

            'stock_movements.view',

            'stock_adjustments.view',
            'stock_adjustments.create',

            'stock_opnames.view',
            'stock_opnames.create',
            'stock_opnames.update',

            'carts.view',
            'checkouts.view',

            'vouchers.view',
            'vouchers.create',
            'vouchers.update',
            'vouchers.delete',

            'promotions.view',
            'promotions.create',
            'promotions.update',
            'promotions.delete',

            'orders.view',
            'orders.create',
            'orders.update',

            'order_histories.view',

            'payments.view',
            'payments.update',

            'payment_transactions.view',

            'couriers.view',
            'couriers.create',
            'couriers.update',
            'couriers.delete',

            'shipping_methods.view',
            'shipping_methods.create',
            'shipping_methods.update',
            'shipping_methods.delete',

            'shipments.view',
            'shipments.update',

            'loyalty_points.view',
            'loyalty_points.update',

            'point_transactions.view',

            'activity_logs.view',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Staff
        |--------------------------------------------------------------------------
        */

        $staff->syncPermissions([

            'dashboard.view',

            'categories.view',

            'products.view',

            'product_reviews.view',

            'customers.view',

            'customer_addresses.view',

            'orders.view',
            'orders.update',

            'payments.view',

            'shipments.view',
            'shipments.update',

            'stock_movements.view',

            'activity_logs.view',
        ]);

        $this->resetPermissionCache();

        /*
        |--------------------------------------------------------------------------
        | Logging
        |--------------------------------------------------------------------------
        */

        Log::info(
            'RolePermissionSeeder executed successfully.',
            [
                'roles_count'       => Role::count(),
                'permissions_count' => Permission::count(),
                'guard'             => self::GUARD,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function resetPermissionCache(): void
    {
        app()
            ->make(
                PermissionRegistrar::class
            )
            ->forgetCachedPermissions();
    }
}