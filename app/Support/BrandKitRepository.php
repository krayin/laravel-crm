<?php

namespace App\Support;

use App\Models\BrandKitCustomCss;
use App\Models\BrandKitOverride;
use App\Models\BrandKitSnapshot;
use Illuminate\Support\Facades\DB;

class BrandKitRepository
{
    public function __construct(
        private BrandKitResolver $resolver,
        private CssValidator $cssValidator,
    ) {}

    // ========================
    // Overrides
    // ========================

    /**
     * Retorna todos os overrides ativos para scope/theme
     */
    public function getOverrides(string $scopeKey, string $themeSlug): array
    {
        return BrandKitOverride::query()
            ->where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->orderBy('override_key')
            ->get()
            ->toArray();
    }

    /**
     * Atualiza ou cria um override
     */
    public function setOverride(
        string $scopeKey,
        string $themeSlug,
        string $overrideKey,
        ?string $value,
        ?int $userId = null,
    ): BrandKitOverride {
        $override = BrandKitOverride::updateOrCreate(
            [
                'scope_key' => $scopeKey,
                'theme_slug' => $themeSlug,
                'override_key' => $overrideKey,
            ],
            [
                'value' => $value,
                'is_active' => true,
                'updated_by' => $userId,
            ],
        );

        $this->resolver->invalidate($scopeKey, $themeSlug);

        return $override;
    }

    /**
     * Atualiza múltiplos overrides de uma vez
     */
    public function setOverrides(
        string $scopeKey,
        string $themeSlug,
        array $overrides,
        ?int $userId = null,
    ): int {
        $count = 0;

        DB::transaction(function () use (
            $scopeKey,
            $themeSlug,
            $overrides,
            $userId,
            &$count,
        ) {
            foreach ($overrides as $key => $value) {
                BrandKitOverride::updateOrCreate(
                    [
                        'scope_key' => $scopeKey,
                        'theme_slug' => $themeSlug,
                        'override_key' => $key,
                    ],
                    [
                        'value' => $value,
                        'is_active' => true,
                        'updated_by' => $userId,
                    ],
                );
                $count++;
            }
        });

        $this->resolver->invalidate($scopeKey, $themeSlug);

        return $count;
    }

    /**
     * Remove um override (hard delete)
     */
    public function deleteOverride(
        string $scopeKey,
        string $themeSlug,
        string $overrideKey,
    ): bool {
        $deleted = BrandKitOverride::query()
            ->where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->where('override_key', $overrideKey)
            ->delete();

        if ($deleted) {
            $this->resolver->invalidate($scopeKey, $themeSlug);
        }

        return $deleted > 0;
    }

    /**
     * Remove todos os overrides para scope/theme
     */
    public function resetOverrides(string $scopeKey, string $themeSlug): int
    {
        $deleted = BrandKitOverride::query()
            ->where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->delete();

        if ($deleted) {
            $this->resolver->invalidate($scopeKey, $themeSlug);
        }

        return $deleted;
    }

    // ========================
    // Custom CSS
    // ========================

    /**
     * Retorna todos os custom CSS entries para scope/theme
     */
    public function getCustomCss(string $scopeKey, string $themeSlug): array
    {
        return BrandKitCustomCss::query()
            ->where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->orderBy('target')
            ->orderBy('priority')
            ->get()
            ->toArray();
    }

    /**
     * Cria ou atualiza uma entrada de CSS custom
     * Retorna null se CSS for inválido
     */
    public function setCustomCss(
        string $scopeKey,
        string $themeSlug,
        string $target,
        string $css,
        string $name = 'Custom CSS',
        int $priority = 10,
        bool $isEnabled = true,
        ?int $userId = null,
    ): ?BrandKitCustomCss {
        // Valida CSS antes de salvar
        if (trim($css) !== '' && !$this->cssValidator->isValid($css)) {
            return null;
        }

        $entry = BrandKitCustomCss::updateOrCreate(
            [
                'scope_key' => $scopeKey,
                'theme_slug' => $themeSlug,
                'target' => $target,
                'name' => $name,
            ],
            [
                'css' => $css,
                'priority' => $priority,
                'is_enabled' => $isEnabled,
                'updated_by' => $userId,
            ],
        );

        $this->resolver->invalidate($scopeKey, $themeSlug);

        return $entry;
    }

    /**
     * Toggle habilitado/desabilitado de uma entrada CSS
     */
    public function toggleCustomCss(int $id, bool $isEnabled): bool
    {
        $entry = BrandKitCustomCss::find($id);

        if (!$entry) {
            return false;
        }

        $entry->update(['is_enabled' => $isEnabled]);

        $this->resolver->invalidate($entry->scope_key, $entry->theme_slug);

        return true;
    }

    /**
     * Remove uma entrada de CSS custom
     */
    public function deleteCustomCss(int $id): bool
    {
        $entry = BrandKitCustomCss::find($id);

        if (!$entry) {
            return false;
        }

        $scopeKey = $entry->scope_key;
        $themeSlug = $entry->theme_slug;

        $entry->delete();

        $this->resolver->invalidate($scopeKey, $themeSlug);

        return true;
    }

    /**
     * Remove todo CSS custom para scope/theme
     */
    public function resetCustomCss(string $scopeKey, string $themeSlug): int
    {
        $deleted = BrandKitCustomCss::query()
            ->where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->delete();

        if ($deleted) {
            $this->resolver->invalidate($scopeKey, $themeSlug);
        }

        return $deleted;
    }

    // ========================
    // Snapshots
    // ========================

    /**
     * Lista todos os snapshots para scope/theme
     */
    public function getSnapshots(string $scopeKey, string $themeSlug): array
    {
        return BrandKitSnapshot::query()
            ->where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->orderByDesc('created_at')
            ->get()
            ->toArray();
    }

    /**
     * Cria um snapshot do estado atual
     */
    public function createSnapshot(
        string $scopeKey,
        string $themeSlug,
        string $name,
        ?int $userId = null,
    ): BrandKitSnapshot {
        $overrides = BrandKitOverride::query()
            ->where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->get()
            ->map(fn($o) => [
                'override_key' => $o->override_key,
                'value' => $o->value,
                'is_active' => $o->is_active,
            ])
            ->toArray();

        $customCss = BrandKitCustomCss::query()
            ->where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->get()
            ->map(fn($c) => [
                'name' => $c->name,
                'target' => $c->target,
                'css' => $c->css,
                'priority' => $c->priority,
                'is_enabled' => $c->is_enabled,
            ])
            ->toArray();

        return BrandKitSnapshot::create([
            'scope_key' => $scopeKey,
            'theme_slug' => $themeSlug,
            'name' => $name,
            'snapshot_version' => 1,
            'overrides_data' => $overrides,
            'custom_css_data' => $customCss,
            'created_by' => $userId,
        ]);
    }

    /**
     * Restaura um snapshot
     */
    public function restoreSnapshot(int $snapshotId, ?int $userId = null): bool
    {
        $snapshot = BrandKitSnapshot::find($snapshotId);

        if (!$snapshot) {
            return false;
        }

        $scopeKey = $snapshot->scope_key;
        $themeSlug = $snapshot->theme_slug;

        DB::transaction(function () use (
            $scopeKey,
            $themeSlug,
            $snapshot,
            $userId,
        ) {
            // Limpa overrides atuais
            BrandKitOverride::query()
                ->where('scope_key', $scopeKey)
                ->where('theme_slug', $themeSlug)
                ->delete();

            // Restaura overrides do snapshot
            foreach ($snapshot->overrides_data as $override) {
                BrandKitOverride::create([
                    'scope_key' => $scopeKey,
                    'theme_slug' => $themeSlug,
                    'override_key' => $override['override_key'],
                    'value' => $override['value'],
                    'is_active' => $override['is_active'] ?? true,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }

            // Limpa CSS atual
            BrandKitCustomCss::query()
                ->where('scope_key', $scopeKey)
                ->where('theme_slug', $themeSlug)
                ->delete();

            // Restaura CSS do snapshot
            foreach ($snapshot->custom_css_data as $css) {
                BrandKitCustomCss::create([
                    'scope_key' => $scopeKey,
                    'theme_slug' => $themeSlug,
                    'name' => $css['name'],
                    'target' => $css['target'],
                    'css' => $css['css'],
                    'priority' => $css['priority'] ?? 10,
                    'is_enabled' => $css['is_enabled'] ?? true,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        });

        $this->resolver->invalidate($scopeKey, $themeSlug);

        return true;
    }

    /**
     * Remove um snapshot
     */
    public function deleteSnapshot(int $id): bool
    {
        return BrandKitSnapshot::destroy($id) > 0;
    }

    // ========================
    // Reset completo
    // ========================

    /**
     * Reset completo: remove overrides e CSS custom (volta ao preset puro)
     */
    public function resetAll(string $scopeKey, string $themeSlug): array
    {
        $overridesDeleted = $this->resetOverrides($scopeKey, $themeSlug);
        $cssDeleted = $this->resetCustomCss($scopeKey, $themeSlug);

        return [
            'overrides_deleted' => $overridesDeleted,
            'css_deleted' => $cssDeleted,
        ];
    }

    // ========================
    // Preview
    // ========================

    /**
     * Gera config para preview (não persiste)
     */
    public function previewConfig(
        string $scopeKey,
        string $themeSlug,
        array $overrides,
        ?string $customCssAdmin = null,
        ?string $customCssLogin = null,
    ): array {
        // Resolve config atual
        $resolved = $this->resolver->resolve($scopeKey, $themeSlug);

        // Aplica overrides temporários
        $config = $resolved['config'];

        foreach ($overrides as $key => $value) {
            if ($value !== null && trim((string) $value) !== '') {
                $config[$key] = $value;
            }
        }

        // Aplica CSS temporário (se válido)
        $cssAdmin = $resolved['custom_css_admin'];
        $cssLogin = $resolved['custom_css_login'];

        if ($customCssAdmin !== null) {
            if (
                trim($customCssAdmin) === '' ||
                $this->cssValidator->isValid($customCssAdmin)
            ) {
                $cssAdmin = $this->cssValidator->sanitize($customCssAdmin);
            }
        }

        if ($customCssLogin !== null) {
            if (
                trim($customCssLogin) === '' ||
                $this->cssValidator->isValid($customCssLogin)
            ) {
                $cssLogin = $this->cssValidator->sanitize($customCssLogin);
            }
        }

        return [
            'config' => $config,
            'custom_css_admin' => $cssAdmin,
            'custom_css_login' => $cssLogin,
            'theme_slug' => $themeSlug,
            'scope_key' => $scopeKey,
            'is_preview' => true,
        ];
    }
}
