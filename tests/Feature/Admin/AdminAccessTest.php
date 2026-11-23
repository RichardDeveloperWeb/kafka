<?php

use App\Enums\ManagerRole;
use App\Models\ContactMessage;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('guests and users without admin permissions cannot access the admin panel', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));

    $client = User::factory()->client()->create();

    $this->actingAs($client);

    $this->get(route('admin.dashboard'))->assertForbidden();
    $this->get(route('admin.products.index'))->assertForbidden();
});

test('manager roles can only reach their own admin resources', function () {
    $manager = User::factory()->withManagerRole(ManagerRole::Products)->create();

    $this->actingAs($manager);

    $this->get(route('admin.products.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/products/Index')
            ->has('products')
            ->has('categories')
            ->etc());

    $this->get(route('admin.users.index'))->assertForbidden();
    $this->get(route('admin.dashboard'))->assertForbidden();
});

test('dashboard exposes counts and recent admin activity', function () {
    $products = Product::factory()->count(3)->create();
    Reservation::factory()->count(2)->for($products->first())->create();
    ContactMessage::factory()->count(2)->create();
    ContactMessage::factory()->read()->create();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Dashboard')
            ->where('counts.products', 3)
            ->where('counts.preorders', 2)
            ->where('counts.messages', 2)
            ->where('counts.users', User::count())
            ->has('recentPreorders', 2)
            ->has('recentMessages', 3)
            ->etc());
});
