<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ThemeSelectionResolver
{
    private const CACHE_KEY = 'theme.selected_slug.v1';
    private const TTL = 300; // 5min (leve e seguro)

    public function getSelectedThemeSlug(): string
    {
        return Cache::remember(self::CACHE_KEY, self::TTL, function () {
            // 1) DB theme_configs (id=1)
            try {
                $row = DB::table('theme_configs')->where('id', 1)->first();

                if ($row && isset($row->is_active) && (int)$row->is_active === 1) {
                    $slug = (string)($row->selected_theme ?? '');
                    $slug = $this->sanitizeSlug($slug);

                    if ($slug !== '') {
                        return $slug;
                    }
                }
            } catch (\Throwable $e) {
                // Se tabela/coluna nao existir, nao quebra o app
            }

            // 2) config fallback (se existir)
            $cfg = (string) config('theme.current', '');
            $cfg = $this->sanitizeSlug($cfg);
            if ($cfg !== '') {
                return $cfg;
            }

            return 'default';
        });
    }

    public function invalidate(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function sanitizeSlug(string $value): string
    {
        $v = strtolower(trim($value));
        $v = preg_replace('/[^a-z0-9_\-]/', '', $v);
        return $v ?? '';
    }
}
