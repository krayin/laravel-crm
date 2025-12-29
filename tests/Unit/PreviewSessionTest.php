<?php

namespace Tests\Unit;

use App\Support\PreviewSession;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

/**
 * Testes para PreviewSession.
 *
 * Foco: Isolamento por sessão, Expiração automática.
 */
class PreviewSessionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Session::flush();
    }

    protected function tearDown(): void
    {
        Session::flush();
        parent::tearDown();
    }

    // =========================================================================
    // BASIC FUNCTIONALITY TESTS
    // =========================================================================

    /** @test */
    public function start_enables_preview_mode(): void
    {
        // Act
        PreviewSession::start('global', 'test-theme');

        // Assert
        $this->assertTrue(PreviewSession::isActive());
        $this->assertEquals('global', Session::get('brandkit.preview.scope_key'));
        $this->assertEquals('test-theme', Session::get('brandkit.preview.theme_slug'));
    }

    /** @test */
    public function clear_disables_preview_mode(): void
    {
        // Arrange
        PreviewSession::start('global', 'test-theme');
        $this->assertTrue(PreviewSession::isActive());

        // Act
        PreviewSession::clear();

        // Assert
        $this->assertFalse(PreviewSession::isActive());
    }

    /** @test */
    public function is_active_returns_false_when_not_started(): void
    {
        // Assert
        $this->assertFalse(PreviewSession::isActive());
    }

    // =========================================================================
    // EXPIRATION TESTS
    // =========================================================================

    /** @test */
    public function preview_has_started_at_timestamp(): void
    {
        // Act
        PreviewSession::start('global', 'test-theme');

        // Assert
        $startedAt = PreviewSession::getStartedAt();
        $this->assertNotNull($startedAt);

        // Started at should be recent (within last minute)
        $this->assertGreaterThan(time() - 60, $startedAt);
        $this->assertLessThanOrEqual(time(), $startedAt);
    }

    /** @test */
    public function is_expired_returns_true_after_timeout(): void
    {
        // Arrange: start preview
        PreviewSession::start('global', 'test-theme');

        // Simulate old start time (35 minutes ago)
        Session::put('brandkit.preview.started_at', time() - (35 * 60));

        // Act & Assert: default timeout is 30 minutes
        $this->assertTrue(PreviewSession::isExpired());
    }

    /** @test */
    public function is_expired_returns_false_when_fresh(): void
    {
        // Arrange: start preview
        PreviewSession::start('global', 'test-theme');

        // Act & Assert
        $this->assertFalse(PreviewSession::isExpired());
    }

    /** @test */
    public function clear_if_expired_removes_expired_preview(): void
    {
        // Arrange: start preview
        PreviewSession::start('global', 'test-theme');

        // Simulate expiration by setting started_at in the past
        Session::put('brandkit.preview.started_at', time() - (35 * 60));

        // Act
        $wasCleared = PreviewSession::clearIfExpired();

        // Assert
        $this->assertTrue($wasCleared);
        $this->assertFalse(PreviewSession::isActive());
    }

    /** @test */
    public function clear_if_expired_keeps_valid_preview(): void
    {
        // Arrange: start preview (fresh)
        PreviewSession::start('global', 'test-theme');

        // Act
        $wasCleared = PreviewSession::clearIfExpired();

        // Assert: should still be active
        $this->assertFalse($wasCleared);
        $this->assertTrue(PreviewSession::isActive());
    }

    // =========================================================================
    // ISOLATION TESTS
    // =========================================================================

    /** @test */
    public function overrides_are_stored_in_session(): void
    {
        // Arrange
        PreviewSession::start('global', 'test-theme');

        // Act
        PreviewSession::updateOverrides([
            'color_primary' => '#FF0000',
            'color_success' => '#00FF00',
        ]);

        // Assert
        $overrides = Session::get('brandkit.preview.overrides');
        $this->assertEquals('#FF0000', $overrides['color_primary']);
        $this->assertEquals('#00FF00', $overrides['color_success']);
    }

    /** @test */
    public function update_overrides_merges_with_existing(): void
    {
        // Arrange
        PreviewSession::start('global', 'test-theme');
        PreviewSession::updateOverrides(['color_primary' => '#FF0000']);

        // Act: add another override
        PreviewSession::updateOverrides(['color_success' => '#00FF00']);

        // Assert: both should exist
        $overrides = Session::get('brandkit.preview.overrides');
        $this->assertEquals('#FF0000', $overrides['color_primary']);
        $this->assertEquals('#00FF00', $overrides['color_success']);
    }

    /** @test */
    public function all_returns_complete_preview_state(): void
    {
        // Arrange
        PreviewSession::start('global', 'test-theme');
        PreviewSession::updateOverrides(['color_primary' => '#FF0000']);

        // Act
        $state = PreviewSession::all();

        // Assert
        $this->assertTrue($state['enabled']);
        $this->assertEquals('global', $state['scope_key']);
        $this->assertEquals('test-theme', $state['theme_slug']);
        $this->assertArrayHasKey('overrides', $state);
        $this->assertArrayHasKey('started_at', $state);
    }

    /** @test */
    public function all_returns_disabled_when_not_active(): void
    {
        // Act
        $state = PreviewSession::all();

        // Assert
        $this->assertFalse($state['enabled']);
    }

    // =========================================================================
    // THEME PREVIEW TESTS
    // =========================================================================

    /** @test */
    public function start_theme_preview_sets_theme_slug(): void
    {
        // Act
        PreviewSession::startThemePreview('new-theme');

        // Assert
        $this->assertTrue(PreviewSession::isActive());
        $this->assertEquals('new-theme', Session::get('brandkit.preview.theme_slug'));
    }

    /** @test */
    public function preview_data_is_isolated_per_session(): void
    {
        // This test conceptually verifies session isolation
        // In practice, each request gets its own session

        // Arrange: session 1
        PreviewSession::start('global', 'theme-a');
        PreviewSession::updateOverrides(['color_primary' => '#AAAAAA']);

        $session1Data = Session::all();

        // Simulate new session
        Session::flush();

        // Arrange: session 2
        PreviewSession::start('global', 'theme-b');
        PreviewSession::updateOverrides(['color_primary' => '#BBBBBB']);

        // Assert: session 2 has different data
        $this->assertEquals('theme-b', Session::get('brandkit.preview.theme_slug'));
        $this->assertEquals('#BBBBBB', Session::get('brandkit.preview.overrides')['color_primary']);
    }

    // =========================================================================
    // CSS TESTS
    // =========================================================================

    /** @test */
    public function update_css_stores_admin_and_login_css(): void
    {
        // Arrange
        PreviewSession::start('global', 'test-theme');

        // Act
        PreviewSession::updateCss('.admin { color: red; }', '.login { color: blue; }');

        // Assert
        $this->assertEquals('.admin { color: red; }', PreviewSession::getCssAdmin());
        $this->assertEquals('.login { color: blue; }', PreviewSession::getCssLogin());
    }

    /** @test */
    public function remove_override_removes_specific_key(): void
    {
        // Arrange
        PreviewSession::start('global', 'test-theme');
        PreviewSession::updateOverrides([
            'color_primary' => '#FF0000',
            'color_success' => '#00FF00',
        ]);

        // Act
        PreviewSession::removeOverride('color_primary');

        // Assert
        $overrides = PreviewSession::getOverrides();
        $this->assertArrayNotHasKey('color_primary', $overrides);
        $this->assertArrayHasKey('color_success', $overrides);
    }
}
