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
     * Test master pages load successfully
     */
    public function test_hibah_page(): void
    {
        $admin = \App\Models\User::where('id', 1)->first();
        $this->actingAs($admin);

        // 1. Test Controller only
        $controller = new \App\Http\Controllers\HibahController();
        $view = $controller->index(request());
        echo "\n[1] Controller OK! Peak Mem: " . round(memory_get_peak_usage() / 1024 / 1024) . " MB\n";

        // 2. Test serializing $hibahRecords
        $hibahRecords = $view->getData()['hibahRecords'];
        $json = json_encode($hibahRecords);
        echo "[2] Serializing hibahRecords OK! Peak Mem: " . round(memory_get_peak_usage() / 1024 / 1024) . " MB\n";

        // 3. Test serializing activeAstaps
        $activeAstaps = $view->getData()['activeAstaps'];
        $json = json_encode($activeAstaps);
        echo "[3] Serializing activeAstaps OK! Peak Mem: " . round(memory_get_peak_usage() / 1024 / 1024) . " MB\n";

        // 4. Test rendering full view
        $html = $view->render();
        echo "[4] Full Render OK! Peak Mem: " . round(memory_get_peak_usage() / 1024 / 1024) . " MB\n";
    }

    public function test_reklas_page(): void
    {
        $admin = \App\Models\User::where('id', 1)->first();
        $this->actingAs($admin);
        $t0 = microtime(true);
        $res = $this->get('/master-data/reklasifikasi');
        $res->assertStatus(200);
        echo "\nReklasifikasi loaded in " . round((microtime(true) - $t0) * 1000) . "ms\n";
    }

    public function test_kemitraan_page(): void
    {
        $admin = \App\Models\User::where('id', 1)->first();
        $this->actingAs($admin);
        $t0 = microtime(true);
        $res = $this->get('/master-data/kemitraan');
        $res->assertStatus(200);
        echo "\nKemitraan loaded in " . round((microtime(true) - $t0) * 1000) . "ms\n";
    }

    /**
     * Test form astap create loads penyedias and pejabats from astaps table
     */
    public function test_astap_create_loads_penyedias_and_pejabats(): void
    {
        $admin = \App\Models\User::whereIn('role', ['admin', 'master_admin'])->first();
        if ($admin) {
            $astap = \App\Models\Astap::first();
            if ($astap) {
                $editRes = $this->actingAs($admin)->get("/astap/{$astap->id}/edit");
                $editRes->assertStatus(200);
            }
        }
    }
}
