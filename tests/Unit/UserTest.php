<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_check_if_user_is_an_admin()
    {
        $admin = User::factory()->make([
            'name' => 'Jon',
            'email' => 'jon@doe.com',
        ]);

        $nonAdmin = User::factory()->make([
            'name' => 'Non Admin',
            'email' => 'user@user.com',
        ]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($nonAdmin->isAdmin());
    }
}
