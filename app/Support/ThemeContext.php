<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Value object imutável que representa o contexto do tema atual.
 * Usado para passar informações de tema para as views de forma type-safe.
 */
final class ThemeContext
{
    /**
     * @param bool   $enabled      Se o sistema de temas está ativo
     * @param string $slug         Slug do tema selecionado
     * @param array  $config       Configurações gerais (cores, logos)
     * @param array  $loginConfig  Configurações específicas do login
     * @param bool   $isPreview    Se está em modo preview (não persistido)
     */
    public function __construct(
        public readonly bool $enabled,
        public readonly string $slug,
        public readonly array $config,
        public readonly array $loginConfig,
        public readonly bool $isPreview = false,
    ) {}

    /**
     * Retorna classes CSS para o body baseado no estado do tema.
     */
    public function bodyClasses(): string
    {
        $classes = [];

        if ($this->enabled) {
            $classes[] = "theme-enabled";
            $classes[] = "theme-" . $this->slug;
        } else {
            $classes[] = "theme-disabled";
        }

        // Adiciona classe de preview se estiver nesse modo
        if ($this->isPreview) {
            $classes[] = "theme-preview-mode";
        }

        // Sempre adiciona classe de login (útil para estilos específicos)
        $classes[] = "theme-login";

        // Adiciona classe de background se tiver imagem configurada
        if ($this->enabled && !empty($this->loginConfig["bg_image"])) {
            $classes[] = "theme-login-bg";
        }

        // Adiciona classe de card customizado se habilitado
        if ($this->enabled && !empty($this->loginConfig["card_enabled"])) {
            $classes[] = "theme-login-card-custom";
        }

        return implode(" ", $classes);
    }

    /**
     * Verifica se está em modo preview.
     */
    public function inPreviewMode(): bool
    {
        return $this->isPreview;
    }

    /**
     * Obtém valor de configuração geral com fallback.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (!$this->enabled) {
            return $default;
        }

        return $this->config[$key] ?? $default;
    }

    /**
     * Obtém valor de configuração de login com fallback.
     */
    public function login(string $key, mixed $default = null): mixed
    {
        if (!$this->enabled) {
            return $default;
        }

        return $this->loginConfig[$key] ?? $default;
    }

    /**
     * Obtém URL do logo por tipo (main, light, icon).
     * Retorna null se tema desativado ou logo não configurado.
     */
    public function logo(string $type): ?string
    {
        if (!$this->enabled) {
            return null;
        }

        $key = "logo_{$type}";
        $filename = $this->config[$key] ?? null;

        if (empty($filename)) {
            return null;
        }

        // Assets do tema ficam em storage/app/public/themes/{slug}/
        // ou no path legado theme-manager/
        if (
            Storage::disk("public")->exists("themes/{$this->slug}/{$filename}")
        ) {
            return Storage::disk("public")->url(
                "themes/{$this->slug}/{$filename}",
            );
        }

        // Fallback para path legado (theme-manager/)
        if (Storage::disk("public")->exists("theme-manager/{$filename}")) {
            return Storage::disk("public")->url("theme-manager/{$filename}");
        }

        return null;
    }

    /**
     * Obtém URL do CSS externo do tema (opcional).
     */
    public function cssUrl(): ?string
    {
        if (!$this->enabled) {
            return null;
        }

        $cssPath = "themes/{$this->slug}/theme.css";

        if (Storage::disk("public")->exists($cssPath)) {
            return Storage::disk("public")->url($cssPath) .
                "?v=" .
                filemtime(storage_path("app/public/{$cssPath}"));
        }

        return null;
    }

    /**
     * Obtém URL do background de login.
     */
    public function loginBgUrl(): ?string
    {
        if (!$this->enabled) {
            return null;
        }

        $filename = $this->loginConfig["bg_image"] ?? null;

        if (empty($filename)) {
            return null;
        }

        // Se já é URL completa
        if (str_starts_with($filename, "http")) {
            return $filename;
        }

        // Tenta no diretório do tema
        if (
            Storage::disk("public")->exists("themes/{$this->slug}/{$filename}")
        ) {
            return Storage::disk("public")->url(
                "themes/{$this->slug}/{$filename}",
            );
        }

        // Fallback para path legado
        if (Storage::disk("public")->exists("theme-manager/{$filename}")) {
            return Storage::disk("public")->url("theme-manager/{$filename}");
        }

        return null;
    }

    /**
     * Obtém URL do background do card de login.
     */
    public function loginCardBgUrl(): ?string
    {
        if (!$this->enabled || empty($this->loginConfig["card_enabled"])) {
            return null;
        }

        $filename = $this->loginConfig["card_bg_image"] ?? null;

        if (empty($filename)) {
            return null;
        }

        if (str_starts_with($filename, "http")) {
            return $filename;
        }

        if (
            Storage::disk("public")->exists("themes/{$this->slug}/{$filename}")
        ) {
            return Storage::disk("public")->url(
                "themes/{$this->slug}/{$filename}",
            );
        }

        if (Storage::disk("public")->exists("theme-manager/{$filename}")) {
            return Storage::disk("public")->url("theme-manager/{$filename}");
        }

        return null;
    }

    /**
     * Verifica se o card customizado está habilitado.
     */
    public function hasCustomCard(): bool
    {
        return $this->enabled && !empty($this->loginConfig["card_enabled"]);
    }

    /**
     * Verifica se deve mostrar "Powered by".
     */
    public function showPoweredBy(): bool
    {
        if (!$this->enabled) {
            return true; // Padrão quando tema desativado
        }

        return (bool) ($this->loginConfig["show_powered_by"] ?? true);
    }

    /**
     * Retorna contexto vazio/desativado (para fallback).
     */
    public static function disabled(): self
    {
        return new self(
            enabled: false,
            slug: "default",
            config: [],
            loginConfig: [],
        );
    }
}
