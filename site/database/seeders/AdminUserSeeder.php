<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use OpenAdmin\Admin\Auth\Database\Administrator;
use OpenAdmin\Admin\Auth\Database\Menu;
use OpenAdmin\Admin\Auth\Database\Permission;
use OpenAdmin\Admin\Auth\Database\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Administrator::truncate();
        Administrator::create([
            'username' => 'jobmatchy.dev',
            'password' => Hash::make('jobmatchy@!123+'),
            'name'     => 'Administrator',
        ]);

        // create a role.
        Role::truncate();
        Role::create([
            'name' => 'Administrator',
            'slug' => 'administrator',
        ]);

        // add role to user.
        Administrator::first()->roles()->save(Role::first());

        //create a permission
        Permission::truncate();
        Permission::insert([
            [
                'name'        => 'All permission',
                'slug'        => '*',
                'http_method' => '',
                'http_path'   => '*',
            ],
            [
                'name'        => 'Dashboard',
                'slug'        => 'dashboard',
                'http_method' => 'GET',
                'http_path'   => '/',
            ],
            [
                'name'        => 'Login',
                'slug'        => 'auth.login',
                'http_method' => '',
                'http_path'   => "/auth/login\r\n/auth/logout",
            ],
            [
                'name'        => 'User setting',
                'slug'        => 'auth.setting',
                'http_method' => 'GET,PUT',
                'http_path'   => '/auth/setting',
            ],
            [
                'name'        => 'Auth management',
                'slug'        => 'auth.management',
                'http_method' => '',
                'http_path'   => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs",
            ],
        ]);

        Role::first()->permissions()->save(Permission::first());

        // add default menus.
        Menu::truncate();
        Menu::insert([
            [
                'parent_id' => 0,
                'order'     => 2,
                'title'     => 'Admin',
                'icon'      => 'fa-tasks',
                'uri'       => '',
            ],
            [
                'parent_id' => 1,
                'order'     => 2,
                'title'     => 'Users',
                'icon'      => 'fa-users',
                'uri'       => 'auth/users',
            ],
            [
                'parent_id' => 1,
                'order'     => 3,
                'title'     => 'Roles',
                'icon'      => 'fa-user',
                'uri'       => 'auth/roles',
            ],
            [
                'parent_id' => 1,
                'order'     => 4,
                'title'     => 'Permission',
                'icon'      => 'fa-ban',
                'uri'       => 'auth/permissions',
            ],
            [
                'parent_id' => 1,
                'order'     => 5,
                'title'     => 'Menu',
                'icon'      => 'fa-bars',
                'uri'       => 'auth/menu',
            ],
            [
                'parent_id' => 1,
                'order'     => 6,
                'title'     => 'Operation log',
                'icon'      => 'fa-history',
                'uri'       => 'auth/logs',
            ],
            [
                'parent_id' => 0,
                'order'     => 2,
                'title'     => 'Dashboard',
                'icon'      => 'fa-bar-chart',
                'uri'       => '/',
            ],
            [
                'parent_id' => 0,
                'order'     => 3,
                'title'     => 'Chat Rooms',
                'icon'      => 'icon-comment-alt',
                'uri'       => 'chat-rooms',
            ],
            [
                'parent_id' => 0,
                'order'     => 4,
                'title'     => 'Companies',
                'icon'      => 'icon-archway',
                'uri'       => 'companies',
            ],
            [
                'parent_id' => 0,
                'order'     => 5,
                'title'     => 'Flips',
                'icon'      => 'icon-hand-point-left',
                'uri'       => 'flips',
            ],
            [
                'parent_id' => 0,
                'order'     => 6,
                'title'     => 'Jobs',
                'icon'      => 'icon-gavel',
                'uri'       => 'jobs',
            ],
            [
                'parent_id' => 0,
                'order'     => 7,
                'title'     => 'Jobseekers',
                'icon'      => 'icon-gavel',
                'uri'       => 'jobseekers',
            ],
            [
                'parent_id' => 0,
                'order'     => 8,
                'title'     => 'Occupation',
                'icon'      => 'icon-allergies',
                'uri'       => 'job-categories',
            ],
            [
                'parent_id' => 0,
                'order'     => 9,
                'title'     => 'Otp Checks',
                'icon'      => 'icon-mobile',
                'uri'       => 'otp-checks',
            ],
            [
                'parent_id' => 0,
                'order'     => 10,
                'title'     => 'Verification',
                'icon'      => 'icon-apple-alt',
                'uri'       => 'violation-reports',
            ],
            [
                'parent_id' => 15,
                'order'     => 1,
                'title'     => 'Verified',
                'icon'      => 'icon-apple-alt',
                'uri'       => 'verified',
            ],
            [
                'parent_id' => 15,
                'order'     => 2,
                'title'     => 'verify',
                'icon'      => 'icon-apple-alt',
                'uri'       => 'verify',
            ],
            [
                'parent_id' => 0,
                'order'     => 11,
                'title'     => 'Violation Reports',
                'icon'      => 'icon-apple-alt',
                'uri'       => 'violation-reports',
            ],
            [
                'parent_id' => 18,
                'order'     => 1,
                'title'     => 'Profile',
                'icon'      => 'icon-apple-alt',
                'uri'       => 'profile-violation',
            ],
            [
                'parent_id' => 18,
                'order'     => 2,
                'title'     => 'Chat',
                'icon'      => 'icon-apple-alt',
                'uri'       => 'chat-violation',
            ],
            [
                'parent_id' => 0,
                'order'     => 19,
                'title'     => 'Districts',
                'icon'      => 'icon-location-arrow',
                'uri'       => 'district',
            ],
            
            
        ]);

        // add role to menu.
        Menu::find(2)->roles()->save(Role::first());
    }
}
