<?php

namespace Tests\Feature;

use App\Models\Klientet;
use App\Models\Materialet;
use App\Models\Projektet;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MaterialAddTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'rol_id' => 1,
            'emri' => 'Admin',
            'mbiemri' => 'Test',
            'email' => 'admin-material@test.com',
            'fjalekalimi_hash' => Hash::make('password'),
            'aktiv' => 1,
        ]);
    }

    public function test_admin_can_create_catalog_material(): void
    {
        $response = $this->actingAs($this->admin)->post(route('materialet.store'), [
            'emri' => 'Druri Alder',
            'njesia_matese' => 'm2',
            'pershkrimi' => 'Material test',
            'sasia_stokut' => 100,
            'sasia_minimale' => 10,
            'cmimi_per_njesi' => 25.5,
            'lokacioni' => 'Raft A1',
        ]);

        $response->assertRedirect(route('materialet.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('materialet', [
            'emri_materialit' => 'Druri Alder',
            'njesia_matese' => 'm2',
        ]);

        // Activity log should store the real primary key (material_id), not null
        $material = Materialet::where('emri_materialit', 'Druri Alder')->first();
        $this->assertDatabaseHas('ditar_veprimet', [
            'objekt_id' => $material->material_id,
            'objekt_tipi' => Materialet::class,
        ]);
    }

    public function test_admin_can_create_catalog_material_with_empty_optional_numbers(): void
    {
        $response = $this->actingAs($this->admin)->post(route('materialet.store'), [
            'emri' => 'MDF Bardhe',
            'njesia_matese' => 'copë',
            'pershkrimi' => '',
            'sasia_stokut' => '',
            'sasia_minimale' => '',
            'cmimi_per_njesi' => '',
            'lokacioni' => '',
        ]);

        $response->assertRedirect(route('materialet.index'));
        $this->assertDatabaseHas('materialet', [
            'emri_materialit' => 'MDF Bardhe',
            'njesia_matese' => 'copë',
        ]);
    }

    public function test_can_add_material_to_project_via_json_endpoint(): void
    {
        [$projekt, $material] = $this->createProjektAndMaterial();

        $response = $this->actingAs($this->admin)
            ->postJson(route('projekte.materiale.store', $projekt->projekt_id), [
                'material_id' => $material->material_id,
                'sasia_perdorur' => 3.5,
                'pershkrimi' => 'Shënim opsional që nuk ruhet në pivot',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('projekt_materiale', [
            'projekt_id' => $projekt->projekt_id,
            'material_id' => $material->material_id,
            'sasia_perdorur' => 3.5,
        ]);
    }

    public function test_can_add_material_to_project_via_projektet_material_store(): void
    {
        [$projekt, $material] = $this->createProjektAndMaterial();

        $response = $this->actingAs($this->admin)
            ->postJson(route('projektet.material.store', $projekt->projekt_id), [
                'material_id' => $material->material_id,
                'sasia_perdorur' => 2,
                'pershkrimi' => 'shënim',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('projekt_materiale', [
            'projekt_id' => $projekt->projekt_id,
            'material_id' => $material->material_id,
            'sasia_perdorur' => 2,
        ]);
    }

    public function test_duplicate_material_on_project_returns_validation_error_not_500(): void
    {
        [$projekt, $material] = $this->createProjektAndMaterial();

        $projekt->materialet()->attach($material->material_id, ['sasia_perdorur' => 1]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('projekte.materiale.store', $projekt->projekt_id), [
                'material_id' => $material->material_id,
                'sasia_perdorur' => 2,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * @return array{0: Projektet, 1: Materialet}
     */
    private function createProjektAndMaterial(): array
    {
        DB::table('statuset_projektit')->insert([
            'emri_statusit' => 'pending',
            'renditja' => 1,
        ]);

        $klientId = DB::table('klientet')->insertGetId([
            'person_kontakti' => 'Klient Test',
            'email_kontakt' => 'klient@test.com',
            'telefon_kontakt' => '044000000',
        ]);

        $projekt = Projektet::create([
            'klient_id' => $klientId,
            'emri_projektit' => 'Projekt Test Material',
            'status_id' => 1,
            'krijues_id' => $this->admin->perdorues_id,
        ]);

        $material = Materialet::create([
            'emri_materialit' => 'Material Projekti ' . uniqid(),
            'njesia_matese' => 'm2',
            'sasia_stokut' => 50,
            'sasia_minimale' => 5,
        ]);

        return [$projekt, $material];
    }
}
