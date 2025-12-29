<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Middleware\HandleThemePreview;
use App\Support\ThemeCache;
use App\Support\ThemeConfigResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controller para ações de rollback de tema.
 *
 * Upgrade-safe: não modifica packages/, apenas adiciona funcionalidades.
 * Rotas definidas em routes/web.php, protegidas por middleware.
 */
class ThemeController extends Controller
{
    /**
     * Restaura o tema para o padrão.
     *
     * - Define selected_theme = 'default'
     * - Define is_active = false
     * - Limpa session de preview
     * - Registra auditoria
     */
    public function restore(Request $request): RedirectResponse
    {
        // Busca estado atual para auditoria
        $current = DB::table('theme_configs')
            ->where('id', 1)
            ->first(['selected_theme', 'is_active']);

        $oldTheme = $current->selected_theme ?? 'default';
        $wasActive = (bool) ($current->is_active ?? false);

        // Atualiza para padrão
        DB::table('theme_configs')
            ->where('id', 1)
            ->update([
                'selected_theme' => 'default',
                'is_active'      => false,
                'previous_theme' => $oldTheme,
                'updated_at'     => now(),
            ]);

        // Limpa caches via helper centralizado
        ThemeCache::flush();

        // Limpa preview session
        session()->forget(HandleThemePreview::SESSION_KEY);

        // Auditoria
        Log::info('theme.restored', [
            'user_id'    => auth()->guard('user')->id(),
            'user_email' => auth()->guard('user')->user()?->email,
            'old_theme'  => $oldTheme,
            'was_active' => $wasActive,
            'ip'         => $request->ip(),
            'timestamp'  => now()->toIso8601String(),
        ]);

        return redirect()
            ->route('admin.settings.theme.index')
            ->with('success', 'Tema restaurado para o padrão com sucesso.');
    }

    /**
     * Volta para o tema anterior (rollback).
     *
     * - Define selected_theme = previous_theme
     * - Mantém is_active
     * - Registra auditoria
     */
    public function rollback(Request $request): RedirectResponse
    {
        // Busca estado atual
        $current = DB::table('theme_configs')
            ->where('id', 1)
            ->first(['selected_theme', 'previous_theme']);

        $currentTheme = $current->selected_theme ?? 'default';
        $previousTheme = $current->previous_theme ?? null;

        // Verifica se há tema anterior
        if (empty($previousTheme)) {
            return redirect()
                ->route('admin.settings.theme.index')
                ->with('warning', 'Não há tema anterior para restaurar.');
        }

        // Verifica se o tema anterior ainda existe
        if (! ThemeConfigResolver::themeExists($previousTheme)) {
            Log::warning('theme.rollback_failed', [
                'user_id'        => auth()->guard('user')->id(),
                'previous_theme' => $previousTheme,
                'reason'         => 'theme_not_found',
            ]);

            return redirect()
                ->route('admin.settings.theme.index')
                ->with(
                    'error',
                    "O tema anterior '{$previousTheme}' não está mais disponível.",
                );
        }

        // Atualiza para tema anterior
        DB::table('theme_configs')
            ->where('id', 1)
            ->update([
                'selected_theme' => $previousTheme,
                'previous_theme' => $currentTheme, // Swap para permitir re-rollback
                'updated_at'     => now(),
            ]);

        // Limpa caches via helper centralizado
        ThemeCache::flush();

        // Limpa preview session
        session()->forget(HandleThemePreview::SESSION_KEY);

        // Auditoria
        Log::info('theme.rollback', [
            'user_id'    => auth()->guard('user')->id(),
            'user_email' => auth()->guard('user')->user()?->email,
            'old'        => $currentTheme,
            'new'        => $previousTheme,
            'ip'         => $request->ip(),
            'timestamp'  => now()->toIso8601String(),
        ]);

        return redirect()
            ->route('admin.settings.theme.index')
            ->with(
                'success',
                "Tema revertido para '{$previousTheme}' com sucesso.",
            );
    }
}
