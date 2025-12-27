<?php

namespace App\Http\Controllers;

use App\Support\BrandKitRepository;
use App\Support\BrandKitResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandKitController extends Controller
{
    private const DEFAULT_SCOPE = 'global';

    public function __construct(
        private BrandKitRepository $repository,
        private BrandKitResolver $resolver,
    ) {}

    // ========================
    // Config (leitura)
    // ========================

    /**
     * GET /brand-kit/config
     * Retorna config resolvida (preset + overrides merged)
     */
    public function config(Request $request): JsonResponse
    {
        $scopeKey = $request->input('scope_key', self::DEFAULT_SCOPE);
        $themeSlug = $request->input('theme_slug', 'default');

        $resolved = $this->resolver->resolve($scopeKey, $themeSlug);

        return response()->json([
            'success' => true,
            'data' => $resolved,
        ]);
    }

    /**
     * GET /brand-kit/themes
     * Lista temas disponíveis
     */
    public function themes(): JsonResponse
    {
        $themes = $this->resolver->getAvailableThemes();

        return response()->json([
            'success' => true,
            'data' => $themes,
        ]);
    }

    // ========================
    // Overrides CRUD
    // ========================

    /**
     * GET /brand-kit/overrides
     */
    public function overrides(Request $request): JsonResponse
    {
        $scopeKey = $request->input('scope_key', self::DEFAULT_SCOPE);
        $themeSlug = $request->input('theme_slug', 'default');

        $overrides = $this->repository->getOverrides($scopeKey, $themeSlug);

        return response()->json([
            'success' => true,
            'data' => $overrides,
        ]);
    }

    /**
     * POST /brand-kit/overrides
     * Cria ou atualiza um override
     */
    public function storeOverride(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scope_key' => 'nullable|string|max:50',
            'theme_slug' => 'nullable|string|max:50',
            'override_key' => 'required|string|max:100',
            'value' => 'nullable|string',
        ]);

        $scopeKey = $validated['scope_key'] ?? self::DEFAULT_SCOPE;
        $themeSlug = $validated['theme_slug'] ?? 'default';
        $userId = $request->user()?->id;

        $override = $this->repository->setOverride(
            $scopeKey,
            $themeSlug,
            $validated['override_key'],
            $validated['value'] ?? null,
            $userId,
        );

        return response()->json([
            'success' => true,
            'data' => $override,
        ]);
    }

    /**
     * POST /brand-kit/overrides/batch
     * Atualiza múltiplos overrides
     */
    public function batchOverrides(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scope_key' => 'nullable|string|max:50',
            'theme_slug' => 'nullable|string|max:50',
            'overrides' => 'required|array',
            'overrides.*' => 'nullable|string',
        ]);

        $scopeKey = $validated['scope_key'] ?? self::DEFAULT_SCOPE;
        $themeSlug = $validated['theme_slug'] ?? 'default';
        $userId = $request->user()?->id;

        $count = $this->repository->setOverrides(
            $scopeKey,
            $themeSlug,
            $validated['overrides'],
            $userId,
        );

        return response()->json([
            'success' => true,
            'message' => "{$count} overrides updated",
            'count' => $count,
        ]);
    }

    /**
     * DELETE /brand-kit/overrides/{key}
     */
    public function deleteOverride(Request $request, string $key): JsonResponse
    {
        $scopeKey = $request->input('scope_key', self::DEFAULT_SCOPE);
        $themeSlug = $request->input('theme_slug', 'default');

        $deleted = $this->repository->deleteOverride($scopeKey, $themeSlug, $key);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Override deleted' : 'Override not found',
        ]);
    }

    // ========================
    // Custom CSS CRUD
    // ========================

    /**
     * GET /brand-kit/css
     */
    public function customCss(Request $request): JsonResponse
    {
        $scopeKey = $request->input('scope_key', self::DEFAULT_SCOPE);
        $themeSlug = $request->input('theme_slug', 'default');

        $css = $this->repository->getCustomCss($scopeKey, $themeSlug);

        return response()->json([
            'success' => true,
            'data' => $css,
        ]);
    }

    /**
     * POST /brand-kit/css
     */
    public function storeCss(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scope_key' => 'nullable|string|max:50',
            'theme_slug' => 'nullable|string|max:50',
            'target' => 'required|string|in:admin,login',
            'name' => 'nullable|string|max:100',
            'css' => 'required|string',
            'priority' => 'nullable|integer|min:0|max:100',
            'is_enabled' => 'nullable|boolean',
        ]);

        $scopeKey = $validated['scope_key'] ?? self::DEFAULT_SCOPE;
        $themeSlug = $validated['theme_slug'] ?? 'default';
        $userId = $request->user()?->id;

        $entry = $this->repository->setCustomCss(
            $scopeKey,
            $themeSlug,
            $validated['target'],
            $validated['css'],
            $validated['name'] ?? 'Custom CSS',
            $validated['priority'] ?? 10,
            $validated['is_enabled'] ?? true,
            $userId,
        );

        if ($entry === null) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid CSS: contains blocked patterns',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $entry,
        ]);
    }

    /**
     * PATCH /brand-kit/css/{id}/toggle
     */
    public function toggleCss(int $id): JsonResponse
    {
        $entry = \App\Models\BrandKitCustomCss::find($id);

        if (!$entry) {
            return response()->json([
                'success' => false,
                'message' => 'CSS entry not found',
            ], 404);
        }

        $this->repository->toggleCustomCss($id, !$entry->is_enabled);

        return response()->json([
            'success' => true,
            'is_enabled' => !$entry->is_enabled,
        ]);
    }

    /**
     * DELETE /brand-kit/css/{id}
     */
    public function deleteCss(int $id): JsonResponse
    {
        $deleted = $this->repository->deleteCustomCss($id);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'CSS deleted' : 'CSS not found',
        ]);
    }

    // ========================
    // Snapshots
    // ========================

    /**
     * GET /brand-kit/snapshots
     */
    public function snapshots(Request $request): JsonResponse
    {
        $scopeKey = $request->input('scope_key', self::DEFAULT_SCOPE);
        $themeSlug = $request->input('theme_slug', 'default');

        $snapshots = $this->repository->getSnapshots($scopeKey, $themeSlug);

        return response()->json([
            'success' => true,
            'data' => $snapshots,
        ]);
    }

    /**
     * POST /brand-kit/snapshots
     */
    public function createSnapshot(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scope_key' => 'nullable|string|max:50',
            'theme_slug' => 'nullable|string|max:50',
            'name' => 'required|string|max:100',
        ]);

        $scopeKey = $validated['scope_key'] ?? self::DEFAULT_SCOPE;
        $themeSlug = $validated['theme_slug'] ?? 'default';
        $userId = $request->user()?->id;

        $snapshot = $this->repository->createSnapshot(
            $scopeKey,
            $themeSlug,
            $validated['name'],
            $userId,
        );

        return response()->json([
            'success' => true,
            'data' => $snapshot,
        ]);
    }

    /**
     * POST /brand-kit/snapshots/{id}/restore
     */
    public function restoreSnapshot(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()?->id;
        $restored = $this->repository->restoreSnapshot($id, $userId);

        return response()->json([
            'success' => $restored,
            'message' => $restored ? 'Snapshot restored' : 'Snapshot not found',
        ]);
    }

    /**
     * DELETE /brand-kit/snapshots/{id}
     */
    public function deleteSnapshot(int $id): JsonResponse
    {
        $deleted = $this->repository->deleteSnapshot($id);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Snapshot deleted' : 'Snapshot not found',
        ]);
    }

    // ========================
    // Reset
    // ========================

    /**
     * POST /brand-kit/reset
     * Reset completo (volta ao preset puro)
     */
    public function reset(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scope_key' => 'nullable|string|max:50',
            'theme_slug' => 'nullable|string|max:50',
            'create_snapshot' => 'nullable|boolean',
        ]);

        $scopeKey = $validated['scope_key'] ?? self::DEFAULT_SCOPE;
        $themeSlug = $validated['theme_slug'] ?? 'default';
        $userId = $request->user()?->id;

        // Opcionalmente cria snapshot antes de resetar
        if ($validated['create_snapshot'] ?? false) {
            $this->repository->createSnapshot(
                $scopeKey,
                $themeSlug,
                'Auto-backup before reset',
                $userId,
            );
        }

        $result = $this->repository->resetAll($scopeKey, $themeSlug);

        return response()->json([
            'success' => true,
            'message' => 'Brand Kit reset to preset defaults',
            'data' => $result,
        ]);
    }

    // ========================
    // Preview
    // ========================

    /**
     * POST /brand-kit/preview
     * Gera config preview sem persistir
     */
    public function preview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scope_key' => 'nullable|string|max:50',
            'theme_slug' => 'nullable|string|max:50',
            'overrides' => 'nullable|array',
            'custom_css_admin' => 'nullable|string',
            'custom_css_login' => 'nullable|string',
        ]);

        $scopeKey = $validated['scope_key'] ?? self::DEFAULT_SCOPE;
        $themeSlug = $validated['theme_slug'] ?? 'default';

        $preview = $this->repository->previewConfig(
            $scopeKey,
            $themeSlug,
            $validated['overrides'] ?? [],
            $validated['custom_css_admin'] ?? null,
            $validated['custom_css_login'] ?? null,
        );

        return response()->json([
            'success' => true,
            'data' => $preview,
        ]);
    }

    /**
     * POST /brand-kit/cache/invalidate
     * Invalida cache manualmente
     */
    public function invalidateCache(Request $request): JsonResponse
    {
        $scopeKey = $request->input('scope_key', self::DEFAULT_SCOPE);
        $themeSlug = $request->input('theme_slug', 'default');

        $this->resolver->invalidate($scopeKey, $themeSlug);

        return response()->json([
            'success' => true,
            'message' => 'Cache invalidated',
        ]);
    }
}
