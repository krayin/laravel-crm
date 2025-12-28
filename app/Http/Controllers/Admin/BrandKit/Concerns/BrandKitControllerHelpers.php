<?php

namespace App\Http\Controllers\Admin\BrandKit\Concerns;

use App\Support\ThemeSlugResolver;
use Illuminate\Http\Request;

trait BrandKitControllerHelpers
{
    private function validateBase(Request $request, array $extraRules = []): array
    {
        $rules = array_merge([
            'scope_key'  => ['nullable','string','max:64'],
            'theme_slug' => ['nullable','string','max:64'],
        ], $extraRules);

        $validated = $request->validate($rules);

        // Resolve theme_slug: request > preview session > DB selected > 'default'
        $themeSlugResolver = app(ThemeSlugResolver::class);
        $resolvedThemeSlug = $themeSlugResolver->resolveFromRequest(
            $validated['theme_slug'] ?? null
        );

        return [
            'scope_key'  => $validated['scope_key'] ?? 'global',
            'theme_slug' => $resolvedThemeSlug,
        ] + $validated;
    }

    private function respond(Request $request, array $payload, string $flashMessage)
    {
        if ($request->expectsJson()) {
            return response()->json($payload);
        }

        return redirect()->back()->with('success', $flashMessage);
    }

    private function userId(Request $request): ?int
    {
        return optional($request->user())->id;
    }

    private function autoSnapshot(string $scopeKey, string $themeSlug, Request $request, string $action): void
    {
        $name = 'AUTO: ' . $action . ' @ ' . now()->format('Y-m-d H:i:s');

        $this->repo->createSnapshot(
            $scopeKey,
            $themeSlug,
            $name,
            $this->userId($request),
            true
        );
    }

    private function autoSnapshotFromCssId(int $cssId, Request $request, string $action): void
    {
        $css = \App\Models\BrandKitCustomCss::query()->findOrFail($cssId);
        $this->autoSnapshot($css->scope_key, $css->theme_slug, $request, $action);
    }

    private function autoSnapshotFromSnapshotId(int $snapshotId, Request $request, string $action): void
    {
        $snap = \App\Models\BrandKitSnapshot::query()->findOrFail($snapshotId);
        $this->autoSnapshot($snap->scope_key, $snap->theme_slug, $request, $action);
    }
}
