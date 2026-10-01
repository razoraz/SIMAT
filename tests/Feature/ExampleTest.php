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

            $kemitraanRes = $this->actingAs($admin)->get('/astap/create-kemitraan');
            $kemitraanRes->assertStatus(200);
            $kemitraanRes->assertViewHas('dbMitraKemitraans');
            $kemitraanRes->assertViewHas('dbPpkKemitraans');

            $kemitraanAstap = \App\Models\Astap::where('sumber_dana', 'kemitraan')->first();
            if ($kemitraanAstap) {
                // Test edit kemitraan page
                $editKemitraanRes = $this->actingAs($admin)->get("/astap/{$kemitraanAstap->id}/edit-kemitraan");
                $editKemitraanRes->assertStatus(200);
                $editKemitraanRes->assertViewHas('astap');
                $editKemitraanRes->assertViewHas('dbMitraKemitraans');

                // Test general /astap/{id}/edit redirects to edit-kemitraan
                $redirectRes = $this->actingAs($admin)->get("/astap/{$kemitraanAstap->id}/edit");
                $redirectRes->assertRedirect(route('astap.edit_kemitraan', ['id' => $kemitraanAstap->id]));
            }

            // Test form create-hibah
            $hibahRes = $this->actingAs($admin)->get('/astap/create-hibah');
            $hibahRes->assertStatus(200);
            $hibahRes->assertViewHas('dbPemberiHibahs');
        }
    }
}
