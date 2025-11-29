<?php

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    $this->seed(RoleSeeder::class);
});

function makeUserWithRole(string $role): User
{
    $user = User::factory()->create();
    $user->assignRole($role);
    return $user;
}

test('UserPolicy::forceLogout works correctly', function () {
    $policy = app(\App\Policies\UserPolicy::class);

    $client = makeUserWithRole('client');
    $staff = makeUserWithRole('staff');
    $otherStaff = makeUserWithRole('staff');
    $admin = makeUserWithRole('admin');
    $super = makeUserWithRole('super-user');

    // Client rules
    expect($policy->forceLogout($client, $client))->toBeTrue()
        ->and($policy->forceLogout($client, $staff))->toBeFalse()
        ->and($policy->forceLogout($staff, $client))->toBeTrue()
        ->and($policy->forceLogout($staff, $otherStaff))->toBeFalse()
        ->and($policy->forceLogout($admin, $client))->toBeTrue()
        ->and($policy->forceLogout($admin, $staff))->toBeTrue()
        ->and($policy->forceLogout($admin, $admin))->toBeFalse()
        ->and($policy->forceLogout($admin, $super))->toBeFalse()
        ->and($policy->forceLogout($super, $client))->toBeTrue()
        ->and($policy->forceLogout($super, $staff))->toBeTrue()
        ->and($policy->forceLogout($super, $admin))->toBeTrue()
        ->and($policy->forceLogout($super, $super))->toBeTrue();
});


test('client cannot force logout any user except themselves', function () {
    $client = makeUserWithRole('client');
    $otherClient = makeUserWithRole('client');

    // cannot logout others
    $this->actingAs($client)
        ->post(route('admin.users.force-logout', $otherClient))
        ->assertForbidden();

    // can logout themselves
    $this->actingAs($client)
        ->post(route('admin.users.force-logout', $client))
        ->assertOk();
});

test('staff may force logout client users only', function () {
    $staff = makeUserWithRole('staff');
    $client = makeUserWithRole('client');
    $otherStaff = makeUserWithRole('staff');

//    dd($staff);

    // allowed
    $this->actingAs($staff)
        ->post(route('admin.users.force-logout', $client))
        ->assertOk();

    // not allowed
    $this->actingAs($staff)
        ->post(route('admin.users.force-logout', $otherStaff))
        ->assertForbidden();
});

test('administrator may logout staff and client users but not other admins', function () {
    $admin = makeUserWithRole('admin');
    $staff = makeUserWithRole('staff');
    $client = makeUserWithRole('client');
    $otherAdmin = makeUserWithRole('admin');

    // allowed
    $this->actingAs($admin)
        ->post(route('admin.users.force-logout', $staff))
        ->assertOk();

    $this->actingAs($admin)
        ->post(route('admin.users.force-logout', $client))
        ->assertOk();

    // forbidden
    $this->actingAs($admin)
        ->post(route('admin.users.force-logout', $otherAdmin))
        ->assertForbidden();
});

test('super-user may logout anyone including administrators', function () {
    $super = makeUserWithRole('super-user');
    $admin = makeUserWithRole('admin');
    $staff = makeUserWithRole('staff');
    $client = makeUserWithRole('client');

    $this->actingAs($super)
        ->post(route('admin.users.force-logout', $admin))
        ->assertOk();

    $this->actingAs($super)
        ->post(route('admin.users.force-logout', $staff))
        ->assertOk();

    $this->actingAs($super)
        ->post(route('admin.users.force-logout', $client))
        ->assertOk();
});

test('guests cannot logout anyone', function () {
    $user = makeUserWithRole('client');

    $this->post(route('admin.users.force-logout', $user))
        ->assertRedirect('/login'); // or 401 if API
});
