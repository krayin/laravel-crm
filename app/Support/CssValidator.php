<?php

namespace App\Support;

final class CssValidator
{
    /**
     * Padroes perigosos/bloqueados.
     * Observacao: CSS e texto; esse bloqueio e "camada 1".
     */
    private const BLOCKED_PATTERNS = [
        '/@import\b/i',
        '/@charset\b/i',
        '/@namespace\b/i',

        // Vetores classicos (legacy / browser quirks)
        '/expression\s*\(/i',
        '/behavior\s*:/i',
        '/-moz-binding\s*:/i',

        // URLs suspeitas
        '/url\s*\(\s*["\']?\s*javascript\s*:/i',
        '/url\s*\(\s*["\']?\s*data\s*:/i',
    ];

    /**
     * Tamanho maximo.
     */
    private const MAX_SIZE = 50 * 1024; // 50KB

    public function validate(string $css): array
    {
        $errors = [];
        $css = (string) $css;

        if (strlen($css) > self::MAX_SIZE) {
            $errors[] = 'CSS excede o tamanho maximo de 50KB.';
        }

        foreach (self::BLOCKED_PATTERNS as $pattern) {
            if (preg_match($pattern, $css)) {
                $errors[] = 'CSS contem conteudo nao permitido.';
                break;
            }
        }

        return $errors;
    }

    public function isValid(string $css): bool
    {
        return empty($this->validate($css));
    }

    /**
     * Sanitizacao defensiva (nao substitui validacao).
     * Remove trechos perigosos em vez de so bloquear.
     */
    public function sanitize(string $css): string
    {
        $out = $css;

        foreach (self::BLOCKED_PATTERNS as $pattern) {
            $out = preg_replace($pattern, '/* blocked */', $out) ?? $out;
        }

        return $out;
    }
}
