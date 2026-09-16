<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
        \DB::purge('sqlite');
    }

    /**
     * Test guest is redirected to login page.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    /**
     * Test login page returns a successful response.
     */
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * Test form astap create loads penyedias and pejabats from astaps table
     */
    public function test_astap_create_loads_penyedias_and_pejabats(): void
    {
        $admin = \App\Models\User::whereIn('role', ['admin', 'master_admin'])->first();
        if ($admin) {
            $response = $this->actingAs($admin)->get('/astap/create');
            $response->assertStatus(200);
            $response->assertViewHas('dbPenyedias');
            $response->assertViewHas('dbPejabats');

            $astap = \App\Models\Astap::first();
            if ($astap) {
                $editRes = $this->actingAs($admin)->get("/astap/{$astap->id}/edit");
                $editRes->assertStatus(200);
                $editRes->assertViewHas('dbPenyedias');
                $editRes->assertViewHas('dbPejabats');
            }
        }
    }
}
