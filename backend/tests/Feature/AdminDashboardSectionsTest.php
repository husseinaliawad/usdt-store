<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardSectionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_sections_render_successfully(): void
    {
        $admin = User::factory()->create([
            'phone' => '+963900000001',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->get(route('control.dashboard'))->assertOk();

        foreach ([
            'transfers',
            'users',
            'wallets',
            'currencies',
            'orders',
            'notifications',
            'reports',
            'audit',
            'roles',
            'security',
            'settings',
            'support',
        ] as $section) {
            $this->actingAs($admin)
                ->get(route('control.section', ['section' => $section]))
                ->assertOk();
        }
    }
}
