<?php

namespace App\Services;

use App\Models\BrandKitCustomCss;
use App\Models\BrandKitOverride;
use App\Models\BrandKitSnapshot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Serviço transacional para gerenciar snapshots do BrandKit.
 *
 * Responsabilidades:
 * - Criar snapshots automáticos antes de alterações
 * - Criar snapshots manuais (usuário)
 * - Restaurar estado a partir de snapshot (atômico)
 */
class BrandKitSnapshotService
{
    private const SNAPSHOT_VERSION = 1;

    private const MAX_AUTO_SNAPSHOTS = 10;

    /**
     * Cria snapshot automático ANTES de qualquer alteração.
     * Deve ser chamado no início de operações de save/update.
     */
    public function createAutoBeforeChange(
        string $scopeKey,
        string $themeSlug,
        ?int $userId = null,
    ): ?BrandKitSnapshot {
        try {
            $currentState = $this->captureCurrentState($scopeKey, $themeSlug);

            // Não cria snapshot se não há dados
            if (empty($currentState['overrides']) && empty($currentState['css'])) {
                return null;
            }

            $snapshot = BrandKitSnapshot::create([
                'scope_key'        => $scopeKey,
                'theme_slug'       => $themeSlug,
                'name'             => 'Auto: '.now()->format('Y-m-d H:i:s'),
                'snapshot_version' => self::SNAPSHOT_VERSION,
                'overrides_data'   => $currentState['overrides'],
                'custom_css_data'  => $currentState['css'],
                'is_auto'          => true,
                'created_by'       => $userId,
            ]);

            // Limpa snapshots antigos (mantém últimos N automáticos)
            $this->cleanupOldAutoSnapshots($scopeKey, $themeSlug);

            return $snapshot;
        } catch (\Throwable $e) {
            Log::warning('BrandKitSnapshotService: Falha ao criar auto-snapshot', [
                'scope_key'  => $scopeKey,
                'theme_slug' => $themeSlug,
                'error'      => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Cria snapshot manual (solicitado pelo usuário).
     */
    public function createManual(
        string $scopeKey,
        string $themeSlug,
        string $name,
        ?int $userId = null,
    ): BrandKitSnapshot {
        $currentState = $this->captureCurrentState($scopeKey, $themeSlug);

        return BrandKitSnapshot::create([
            'scope_key'        => $scopeKey,
            'theme_slug'       => $themeSlug,
            'name'             => $name,
            'snapshot_version' => self::SNAPSHOT_VERSION,
            'overrides_data'   => $currentState['overrides'],
            'custom_css_data'  => $currentState['css'],
            'is_auto'          => false,
            'created_by'       => $userId,
        ]);
    }

    /**
     * Restaura estado a partir de um snapshot.
     * ATÔMICO: Se falhar no meio, faz rollback completo.
     *
     * @throws \Throwable Se a restauração falhar
     */
    public function restore(
        int $snapshotId,
        ?int $userId = null,
    ): bool {
        $snapshot = BrandKitSnapshot::findOrFail($snapshotId);

        return DB::transaction(function () use ($snapshot, $userId) {
            $scopeKey = $snapshot->scope_key;
            $themeSlug = $snapshot->theme_slug;

            // 1. Cria backup do estado atual antes do restore
            $this->createAutoBeforeChange($scopeKey, $themeSlug, $userId);

            // 2. Limpa overrides atuais (hard delete para restore limpo)
            BrandKitOverride::where('scope_key', $scopeKey)
                ->where('theme_slug', $themeSlug)
                ->delete();

            // 3. Restaura overrides do snapshot
            $overridesData = $snapshot->overrides_data ?? [];
            foreach ($overridesData as $item) {
                BrandKitOverride::create([
                    'scope_key'    => $scopeKey,
                    'theme_slug'   => $themeSlug,
                    'override_key' => $item['override_key'],
                    'value'        => $item['value'],
                    'is_active'    => $item['is_active'] ?? true,
                    'updated_by'   => $userId,
                ]);
            }

            // 4. Limpa CSS customizado atual
            BrandKitCustomCss::where('scope_key', $scopeKey)
                ->where('theme_slug', $themeSlug)
                ->delete();

            // 5. Restaura CSS do snapshot
            $cssData = $snapshot->custom_css_data ?? [];
            foreach ($cssData as $item) {
                BrandKitCustomCss::create([
                    'scope_key'   => $scopeKey,
                    'theme_slug'  => $themeSlug,
                    'name'        => $item['name'],
                    'css_content' => $item['css_content'],
                    'target'      => $item['target'] ?? 'admin',
                    'is_enabled'  => $item['is_enabled'] ?? true,
                    'priority'    => $item['priority'] ?? 100,
                    'created_by'  => $userId,
                ]);
            }

            Log::info('BrandKitSnapshotService: Snapshot restaurado', [
                'snapshot_id'        => $snapshot->id,
                'scope_key'          => $scopeKey,
                'theme_slug'         => $themeSlug,
                'restored_overrides' => count($overridesData),
                'restored_css'       => count($cssData),
            ]);

            return true;
        });
    }

    /**
     * Lista snapshots disponíveis para um scope/theme.
     *
     * @return \Illuminate\Database\Eloquent\Collection<BrandKitSnapshot>
     */
    public function list(
        string $scopeKey,
        string $themeSlug,
        int $limit = 20,
    ): \Illuminate\Database\Eloquent\Collection {
        return BrandKitSnapshot::where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Exclui um snapshot (apenas manuais podem ser excluídos pelo usuário).
     */
    public function delete(int $snapshotId, bool $allowAutoDelete = false): bool
    {
        $snapshot = BrandKitSnapshot::find($snapshotId);

        if (! $snapshot) {
            return false;
        }

        if ($snapshot->is_auto && ! $allowAutoDelete) {
            return false;
        }

        return (bool) $snapshot->delete();
    }

    /**
     * Captura o estado atual de overrides e CSS.
     *
     * @return array{overrides: array, css: array}
     */
    private function captureCurrentState(string $scopeKey, string $themeSlug): array
    {
        $overrides = BrandKitOverride::where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->where('is_active', true)
            ->get(['override_key', 'value', 'is_active'])
            ->map(fn ($o) => [
                'override_key' => $o->override_key,
                'value'        => $o->value,
                'is_active'    => $o->is_active,
            ])
            ->toArray();

        $css = BrandKitCustomCss::where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->get(['name', 'css_content', 'target', 'is_enabled', 'priority'])
            ->map(fn ($c) => [
                'name'        => $c->name,
                'css_content' => $c->css_content,
                'target'      => $c->target,
                'is_enabled'  => $c->is_enabled,
                'priority'    => $c->priority,
            ])
            ->toArray();

        return [
            'overrides' => $overrides,
            'css'       => $css,
        ];
    }

    /**
     * Remove snapshots automáticos antigos, mantendo os últimos N.
     */
    private function cleanupOldAutoSnapshots(string $scopeKey, string $themeSlug): void
    {
        $autoSnapshots = BrandKitSnapshot::where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->where('is_auto', true)
            ->orderByDesc('created_at')
            ->get();

        if ($autoSnapshots->count() > self::MAX_AUTO_SNAPSHOTS) {
            $toDelete = $autoSnapshots->slice(self::MAX_AUTO_SNAPSHOTS);
            BrandKitSnapshot::whereIn('id', $toDelete->pluck('id'))->delete();
        }
    }
}
