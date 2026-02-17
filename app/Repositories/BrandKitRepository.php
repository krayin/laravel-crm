<?php

namespace App\Repositories;

use App\Models\BrandKitCustomCss;
use App\Models\BrandKitOverride;
use App\Models\BrandKitSnapshot;
use App\Support\BrandKitResolver;
use App\Support\CssValidator;
use Illuminate\Support\Facades\DB;

final class BrandKitRepository
{
    public function __construct(
        private BrandKitResolver $resolver,
        private CssValidator $cssValidator,
    ) {}

    public function setOverride(
        string $scopeKey,
        string $themeSlug,
        string $overrideKey,
        $value,
        ?int $userId = null,
    ): BrandKitOverride {
        // Regra: value null/'' => desativa (fallback preset)
        $isActive = ! (
            $value === null ||
            (is_string($value) && trim($value) === '')
        );

        $row = BrandKitOverride::updateOrCreate(
            [
                'scope_key'    => $scopeKey,
                'theme_slug'   => $themeSlug,
                'override_key' => $overrideKey,
            ],
            [
                'value'      => $value,
                'is_active'  => $isActive,
                'updated_by' => $userId,
            ],
        );

        $this->resolver->invalidate($scopeKey, $themeSlug);

        return $row;
    }

    public function resetOverride(
        string $scopeKey,
        string $themeSlug,
        string $overrideKey,
    ): void {
        BrandKitOverride::where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->where('override_key', $overrideKey)
            ->update(['is_active' => false]);

        $this->resolver->invalidate($scopeKey, $themeSlug);
    }

    public function resetAllOverrides(string $scopeKey, string $themeSlug): void
    {
        BrandKitOverride::where('scope_key', $scopeKey)
            ->where('theme_slug', $themeSlug)
            ->update(['is_active' => false]);

        $this->resolver->invalidate($scopeKey, $themeSlug);
    }

    public function addCustomCss(
        string $scopeKey,
        string $themeSlug,
        string $name,
        string $cssContent,
        string $target = 'admin',
        ?int $userId = null,
    ): BrandKitCustomCss {
        $errors = $this->cssValidator->validate($cssContent);
        if (! empty($errors)) {
            throw new \InvalidArgumentException(implode(' ', $errors));
        }

        $css = BrandKitCustomCss::create([
            'scope_key'   => $scopeKey,
            'theme_slug'  => $themeSlug,
            'name'        => $name,
            'css_content' => $this->cssValidator->sanitize($cssContent),
            'target'      => $target,
            'is_enabled'  => false, // segurança: usuário ativa depois
            'priority'    => 100,
            'created_by'  => $userId,
        ]);

        $this->resolver->invalidate($scopeKey, $themeSlug);

        return $css;
    }

    public function toggleCustomCss(int $cssId): BrandKitCustomCss
    {
        $css = BrandKitCustomCss::findOrFail($cssId);
        $css->update(['is_enabled' => ! $css->is_enabled]);

        $this->resolver->invalidate($css->scope_key, $css->theme_slug);

        return $css;
    }

    public function deleteCustomCss(int $cssId): void
    {
        $css = BrandKitCustomCss::findOrFail($cssId);
        $scopeKey = $css->scope_key;
        $themeSlug = $css->theme_slug;

        $css->delete();
        $this->resolver->invalidate($scopeKey, $themeSlug);
    }

    public function createSnapshot(
        string $scopeKey,
        string $themeSlug,
        string $name,
        ?int $userId = null,
        bool $isAuto = false,
    ): BrandKitSnapshot {
        return BrandKitSnapshot::create([
            'scope_key'        => $scopeKey,
            'theme_slug'       => $themeSlug,
            'name'             => $name,
            'snapshot_version' => 1,
            'overrides_data'   => BrandKitOverride::where('scope_key', $scopeKey)
                ->where('theme_slug', $themeSlug)
                ->get()
                ->toArray(),
            'custom_css_data' => BrandKitCustomCss::where(
                'scope_key',
                $scopeKey,
            )
                ->where('theme_slug', $themeSlug)
                ->get()
                ->toArray(),
            'is_auto'    => $isAuto,
            'created_by' => $userId,
        ]);
    }

    public function restoreSnapshot(int $snapshotId): void
    {
        $snapshot = BrandKitSnapshot::findOrFail($snapshotId);

        DB::transaction(function () use ($snapshot) {
            BrandKitOverride::where('scope_key', $snapshot->scope_key)
                ->where('theme_slug', $snapshot->theme_slug)
                ->delete();

            foreach ($snapshot->overrides_data as $row) {
                unset($row['id'], $row['created_at'], $row['updated_at']);
                BrandKitOverride::create($row);
            }

            BrandKitCustomCss::where('scope_key', $snapshot->scope_key)
                ->where('theme_slug', $snapshot->theme_slug)
                ->delete();

            if (! empty($snapshot->custom_css_data)) {
                foreach ($snapshot->custom_css_data as $row) {
                    unset($row['id'], $row['created_at'], $row['updated_at']);
                    BrandKitCustomCss::create($row);
                }
            }
        });

        $this->resolver->invalidate(
            $snapshot->scope_key,
            $snapshot->theme_slug,
        );
    }
}
