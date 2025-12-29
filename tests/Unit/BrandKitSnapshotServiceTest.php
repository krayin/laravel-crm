<?php

namespace Tests\Unit;

use App\Models\BrandKitCustomCss;
use App\Models\BrandKitOverride;
use App\Models\BrandKitSnapshot;
use App\Services\BrandKitSnapshotService;
use Tests\TestCase;
use Tests\Traits\BrandKitTestSchema;

/**
 * Testes para BrandKitSnapshotService.
 *
 * Foco: Transacionalidade, Atomicidade do Restore, Cleanup de Auto-Snapshots.
 *
 * Usa schema mínimo em memória para evitar dependência das migrations
 * completas do Krayin (que podem falhar com SQLite).
 */
class BrandKitSnapshotServiceTest extends TestCase
{
    use BrandKitTestSchema;

    private BrandKitSnapshotService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Schema mínimo em memória
        $this->setUpBrandKitSchema();

        // Cache em memória para isolamento total
        config()->set('cache.default', 'array');

        $this->service = app(BrandKitSnapshotService::class);
    }

    protected function tearDown(): void
    {
        $this->tearDownBrandKitSchema();
        parent::tearDown();
    }

    // =========================================================================
    // CREATE AUTO SNAPSHOT TESTS
    // =========================================================================

    /** @test */
    public function create_auto_before_change_captures_current_state(): void
    {
        // Arrange: cria overrides
        BrandKitOverride::create([
            'scope_key'    => 'global',
            'theme_slug'   => 'test-theme',
            'override_key' => 'color_primary',
            'value'        => '#FF0000',
            'is_active'    => true,
        ]);

        BrandKitOverride::create([
            'scope_key'    => 'global',
            'theme_slug'   => 'test-theme',
            'override_key' => 'color_success',
            'value'        => '#00FF00',
            'is_active'    => true,
        ]);

        // Act
        $snapshot = $this->service->createAutoBeforeChange('global', 'test-theme', 1);

        // Assert
        $this->assertNotNull($snapshot);
        $this->assertTrue($snapshot->is_auto);
        $this->assertStringStartsWith('Auto:', $snapshot->name);
        $this->assertCount(2, $snapshot->overrides_data);
    }

    /** @test */
    public function create_auto_returns_null_when_no_data(): void
    {
        // Act: sem dados
        $snapshot = $this->service->createAutoBeforeChange('global', 'empty-theme', 1);

        // Assert
        $this->assertNull($snapshot);
    }

    /** @test */
    public function create_auto_limits_to_max_snapshots(): void
    {
        // Arrange: cria um override para ter dados
        BrandKitOverride::create([
            'scope_key'    => 'global',
            'theme_slug'   => 'test-theme',
            'override_key' => 'color_primary',
            'value'        => '#FF0000',
            'is_active'    => true,
        ]);

        // Act: cria 15 snapshots (mais que o limite de 10)
        for ($i = 0; $i < 15; $i++) {
            $this->service->createAutoBeforeChange('global', 'test-theme', 1);
        }

        // Assert: deve ter apenas 10 (MAX_AUTO_SNAPSHOTS)
        $count = BrandKitSnapshot::where('scope_key', 'global')
            ->where('theme_slug', 'test-theme')
            ->where('is_auto', true)
            ->count();

        $this->assertEquals(10, $count);
    }

    // =========================================================================
    // CREATE MANUAL SNAPSHOT TESTS
    // =========================================================================

    /** @test */
    public function create_manual_creates_named_snapshot(): void
    {
        // Arrange
        BrandKitOverride::create([
            'scope_key'    => 'global',
            'theme_slug'   => 'test-theme',
            'override_key' => 'color_primary',
            'value'        => '#FF0000',
            'is_active'    => true,
        ]);

        // Act
        $snapshot = $this->service->createManual(
            'global',
            'test-theme',
            'Meu Backup Importante',
            1,
        );

        // Assert
        $this->assertFalse($snapshot->is_auto);
        $this->assertEquals('Meu Backup Importante', $snapshot->name);
    }

    /** @test */
    public function create_manual_includes_custom_css(): void
    {
        // Arrange
        BrandKitCustomCss::create([
            'scope_key'   => 'global',
            'theme_slug'  => 'test-theme',
            'name'        => 'Custom Styles',
            'css_content' => '.test { color: red; }',
            'target'      => 'admin',
            'is_enabled'  => true,
            'priority'    => 100,
        ]);

        // Act
        $snapshot = $this->service->createManual('global', 'test-theme', 'With CSS', 1);

        // Assert
        $this->assertNotEmpty($snapshot->custom_css_data);
        $this->assertCount(1, $snapshot->custom_css_data);
        $this->assertEquals('.test { color: red; }', $snapshot->custom_css_data[0]['css_content']);
    }

    // =========================================================================
    // RESTORE TESTS (TRANSACTIONAL)
    // =========================================================================

    /** @test */
    public function restore_is_atomic(): void
    {
        // Arrange: estado inicial
        BrandKitOverride::create([
            'scope_key'    => 'global',
            'theme_slug'   => 'test-theme',
            'override_key' => 'color_primary',
            'value'        => '#INITIAL',
            'is_active'    => true,
        ]);

        // Cria snapshot do estado inicial
        $snapshot = $this->service->createManual('global', 'test-theme', 'Initial State', 1);

        // Muda para novo estado
        BrandKitOverride::where('override_key', 'color_primary')->update(['value' => '#CHANGED']);

        // Act: restore
        $result = $this->service->restore($snapshot->id, 1);

        // Assert
        $this->assertTrue($result);

        $restored = BrandKitOverride::where('override_key', 'color_primary')->first();
        $this->assertEquals('#INITIAL', $restored->value);
    }

    /** @test */
    public function restore_clears_existing_data_before_restore(): void
    {
        // Arrange: estado inicial com 1 override
        BrandKitOverride::create([
            'scope_key'    => 'global',
            'theme_slug'   => 'test-theme',
            'override_key' => 'color_primary',
            'value'        => '#INITIAL',
            'is_active'    => true,
        ]);

        $snapshot = $this->service->createManual('global', 'test-theme', 'Snapshot', 1);

        // Adiciona mais overrides
        BrandKitOverride::create([
            'scope_key'    => 'global',
            'theme_slug'   => 'test-theme',
            'override_key' => 'color_success',
            'value'        => '#EXTRA',
            'is_active'    => true,
        ]);

        // Confirma que temos 2 overrides
        $this->assertEquals(2, BrandKitOverride::count());

        // Act: restore
        $this->service->restore($snapshot->id, 1);

        // Assert: deve ter apenas 1 override (do snapshot)
        $this->assertEquals(1, BrandKitOverride::where('is_active', true)->count());
    }

    /** @test */
    public function restore_creates_backup_before_restoring(): void
    {
        // Arrange
        BrandKitOverride::create([
            'scope_key'    => 'global',
            'theme_slug'   => 'test-theme',
            'override_key' => 'color_primary',
            'value'        => '#BEFORE',
            'is_active'    => true,
        ]);

        $snapshot = $this->service->createManual('global', 'test-theme', 'Original', 1);

        BrandKitOverride::where('override_key', 'color_primary')->update(['value' => '#AFTER']);

        $snapshotCountBefore = BrandKitSnapshot::count();

        // Act
        $this->service->restore($snapshot->id, 1);

        // Assert: deve ter criado um auto-snapshot antes do restore
        $this->assertEquals($snapshotCountBefore + 1, BrandKitSnapshot::count());

        $autoBackup = BrandKitSnapshot::where('is_auto', true)->latest()->first();
        $this->assertStringStartsWith('Auto:', $autoBackup->name);
    }

    /** @test */
    public function restore_handles_css_data(): void
    {
        // Arrange: snapshot com CSS
        BrandKitCustomCss::create([
            'scope_key'   => 'global',
            'theme_slug'  => 'test-theme',
            'name'        => 'Original CSS',
            'css_content' => '.original { color: blue; }',
            'target'      => 'admin',
            'is_enabled'  => true,
            'priority'    => 100,
        ]);

        $snapshot = $this->service->createManual('global', 'test-theme', 'With CSS', 1);

        // Muda CSS
        BrandKitCustomCss::truncate();
        BrandKitCustomCss::create([
            'scope_key'   => 'global',
            'theme_slug'  => 'test-theme',
            'name'        => 'New CSS',
            'css_content' => '.new { color: red; }',
            'target'      => 'login',
            'is_enabled'  => false,
            'priority'    => 50,
        ]);

        // Act: restore
        $this->service->restore($snapshot->id, 1);

        // Assert: CSS original restaurado
        $css = BrandKitCustomCss::first();
        $this->assertEquals('Original CSS', $css->name);
        $this->assertEquals('.original { color: blue; }', $css->css_content);
    }

    // =========================================================================
    // LIST AND DELETE TESTS
    // =========================================================================

    /** @test */
    public function list_returns_snapshots_ordered_by_date(): void
    {
        // Arrange
        BrandKitOverride::create([
            'scope_key'    => 'global',
            'theme_slug'   => 'test-theme',
            'override_key' => 'color_primary',
            'value'        => '#FF0000',
            'is_active'    => true,
        ]);

        // Cria snapshots com timestamps diferentes usando Carbon
        $first = $this->service->createManual('global', 'test-theme', 'First', 1);
        $first->created_at = now()->subMinutes(3);
        $first->save();

        $second = $this->service->createManual('global', 'test-theme', 'Second', 1);
        $second->created_at = now()->subMinutes(2);
        $second->save();

        $third = $this->service->createManual('global', 'test-theme', 'Third', 1);
        $third->created_at = now()->subMinutes(1);
        $third->save();

        // Act
        $list = $this->service->list('global', 'test-theme');

        // Assert: mais recente primeiro (Third tem o menor subMinutes)
        $this->assertEquals('Third', $list[0]->name);
        $this->assertEquals('First', $list[2]->name);
    }

    /** @test */
    public function delete_removes_manual_snapshot(): void
    {
        // Arrange
        BrandKitOverride::create([
            'scope_key'    => 'global',
            'theme_slug'   => 'test-theme',
            'override_key' => 'color_primary',
            'value'        => '#FF0000',
            'is_active'    => true,
        ]);

        $snapshot = $this->service->createManual('global', 'test-theme', 'To Delete', 1);

        // Act
        $result = $this->service->delete($snapshot->id);

        // Assert
        $this->assertTrue($result);
        $this->assertNull(BrandKitSnapshot::find($snapshot->id));
    }

    /** @test */
    public function delete_does_not_remove_auto_snapshot_by_default(): void
    {
        // Arrange
        BrandKitOverride::create([
            'scope_key'    => 'global',
            'theme_slug'   => 'test-theme',
            'override_key' => 'color_primary',
            'value'        => '#FF0000',
            'is_active'    => true,
        ]);

        $snapshot = $this->service->createAutoBeforeChange('global', 'test-theme', 1);

        // Act
        $result = $this->service->delete($snapshot->id);

        // Assert: não deve deletar
        $this->assertFalse($result);
        $this->assertNotNull(BrandKitSnapshot::find($snapshot->id));
    }

    /** @test */
    public function delete_removes_auto_snapshot_when_allowed(): void
    {
        // Arrange
        BrandKitOverride::create([
            'scope_key'    => 'global',
            'theme_slug'   => 'test-theme',
            'override_key' => 'color_primary',
            'value'        => '#FF0000',
            'is_active'    => true,
        ]);

        $snapshot = $this->service->createAutoBeforeChange('global', 'test-theme', 1);

        // Act: com allowAutoDelete = true
        $result = $this->service->delete($snapshot->id, true);

        // Assert
        $this->assertTrue($result);
        $this->assertNull(BrandKitSnapshot::find($snapshot->id));
    }
}
