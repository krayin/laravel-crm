<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * Validador de assets para temas.
 * Valida formatos, tamanhos e integridade de arquivos de tema.
 */
class ThemeAssetValidator
{
    /**
     * Extensões permitidas por tipo de asset.
     */
    private const ALLOWED_EXTENSIONS = [
        'image'   => ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'],
        'favicon' => ['ico', 'png', 'svg'],
        'css'     => ['css'],
        'json'    => ['json'],
    ];

    /**
     * MIME types permitidos.
     */
    private const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/svg+xml',
        'image/webp',
        'image/x-icon',
        'image/vnd.microsoft.icon',
        'text/css',
        'application/json',
    ];

    /**
     * Tamanho máximo por tipo (em bytes).
     */
    private const MAX_SIZES = [
        'logo'       => 2 * 1024 * 1024,      // 2MB
        'favicon'    => 512 * 1024,         // 512KB
        'background' => 10 * 1024 * 1024, // 10MB
        'css'        => 1 * 1024 * 1024,        // 1MB
        'default'    => 5 * 1024 * 1024,    // 5MB
    ];

    /**
     * Dimensões recomendadas (para warnings, não erros).
     */
    private const RECOMMENDED_DIMENSIONS = [
        'logo_main'     => ['min_width' => 100, 'max_width' => 500, 'min_height' => 30, 'max_height' => 150],
        'logo_icon'     => ['min_width' => 32, 'max_width' => 128, 'min_height' => 32, 'max_height' => 128],
        'favicon'       => ['min_width' => 16, 'max_width' => 64, 'min_height' => 16, 'max_height' => 64],
        'login_bg'      => ['min_width' => 1280, 'min_height' => 720],
        'login_card_bg' => ['min_width' => 400, 'min_height' => 300],
    ];

    /**
     * Erros encontrados na validação.
     */
    private array $errors = [];

    /**
     * Warnings (não bloqueiam, mas são reportados).
     */
    private array $warnings = [];

    /**
     * Valida um arquivo uploadado.
     */
    public function validateUploadedFile(
        UploadedFile $file,
        string $fieldType = 'default'
    ): bool {
        $this->errors = [];
        $this->warnings = [];

        // 1. Extensão permitida
        if (! $this->validateExtension($file, $fieldType)) {
            return false;
        }

        // 2. MIME type
        if (! $this->validateMimeType($file)) {
            return false;
        }

        // 3. Tamanho
        if (! $this->validateSize($file, $fieldType)) {
            return false;
        }

        // 4. Integridade do arquivo
        if (! $this->validateIntegrity($file)) {
            return false;
        }

        // 5. Dimensões (apenas warning para imagens)
        $this->checkDimensions($file, $fieldType);

        // 6. Segurança (SVG, etc)
        if (! $this->validateSecurity($file)) {
            return false;
        }

        return true;
    }

    /**
     * Valida um diretório de tema completo.
     */
    public function validateThemeDirectory(string $themePath): bool
    {
        $this->errors = [];
        $this->warnings = [];

        // 1. Diretório existe
        if (! File::isDirectory($themePath)) {
            $this->errors[] = "Diretório do tema não existe: {$themePath}";

            return false;
        }

        // 2. theme.json existe e é válido
        $themeJsonPath = $themePath.DIRECTORY_SEPARATOR.'theme.json';
        if (! $this->validateThemeJson($themeJsonPath)) {
            return false;
        }

        // 3. Valida assets referenciados no theme.json
        $themeData = json_decode(File::get($themeJsonPath), true);
        $this->validateReferencedAssets($themePath, $themeData);

        return empty($this->errors);
    }

    /**
     * Valida o theme.json.
     */
    public function validateThemeJson(string $path): bool
    {
        if (! File::exists($path)) {
            $this->errors[] = 'Arquivo theme.json não encontrado';

            return false;
        }

        $content = File::get($path);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->errors[] = 'theme.json inválido: '.json_last_error_msg();

            return false;
        }

        // Campos obrigatórios
        $required = ['name', 'version'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->errors[] = "Campo obrigatório ausente no theme.json: {$field}";
            }
        }

        // Validar formato de cores
        $colorFields = [
            'color_primary', 'color_primary_dark', 'color_primary_light',
            'color_success', 'color_warning', 'color_danger',
        ];

        foreach ($colorFields as $field) {
            if (isset($data[$field]) && ! $this->isValidColor($data[$field])) {
                $this->errors[] = "Cor inválida em {$field}: {$data[$field]} (use formato #RRGGBB)";
            }
        }

        // Validar valores numéricos
        $numericFields = [
            'login_bg_zoom'         => [50, 200],
            'login_bg_opacity'      => [0, 100],
            'login_card_bg_opacity' => [0, 100],
        ];

        foreach ($numericFields as $field => [$min, $max]) {
            if (isset($data[$field])) {
                $value = $data[$field];
                if (! is_numeric($value) || $value < $min || $value > $max) {
                    $this->errors[] = "{$field} deve ser um número entre {$min} e {$max}";
                }
            }
        }

        // Validar booleanos
        $boolFields = [
            'login_show_powered_by', 'login_card_enabled',
            'login_card_sparkles', 'login_card_help_link',
        ];

        foreach ($boolFields as $field) {
            if (isset($data[$field]) && ! is_bool($data[$field])) {
                $this->warnings[] = "{$field} deveria ser boolean (true/false), não string";
            }
        }

        return empty($this->errors);
    }

    /**
     * Valida assets referenciados no theme.json.
     */
    private function validateReferencedAssets(string $themePath, array $themeData): void
    {
        $assetFields = [
            'logo_main'           => 'logo',
            'logo_light'          => 'logo',
            'logo_icon'           => 'logo',
            'favicon'             => 'favicon',
            'login_bg_image'      => 'background',
            'login_card_bg_image' => 'background',
        ];

        foreach ($assetFields as $field => $type) {
            if (empty($themeData[$field])) {
                continue;
            }

            $assetPath = $themePath.DIRECTORY_SEPARATOR.$themeData[$field];

            if (! File::exists($assetPath)) {
                $this->warnings[] = "Asset referenciado não encontrado: {$themeData[$field]}";

                continue;
            }

            // Validar tamanho
            $size = File::size($assetPath);
            $maxSize = self::MAX_SIZES[$type] ?? self::MAX_SIZES['default'];

            if ($size > $maxSize) {
                $maxMB = round($maxSize / 1024 / 1024, 1);
                $sizeMB = round($size / 1024 / 1024, 2);
                $this->warnings[] = "{$field}: arquivo muito grande ({$sizeMB}MB, máximo {$maxMB}MB)";
            }

            // Validar extensão
            $extension = strtolower(pathinfo($assetPath, PATHINFO_EXTENSION));
            $allowedExts = $type === 'favicon'
                ? self::ALLOWED_EXTENSIONS['favicon']
                : self::ALLOWED_EXTENSIONS['image'];

            if (! in_array($extension, $allowedExts)) {
                $this->errors[] = "{$field}: extensão não permitida ({$extension})";
            }
        }
    }

    /**
     * Valida extensão do arquivo.
     */
    private function validateExtension(UploadedFile $file, string $fieldType): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());

        $allowed = match ($fieldType) {
            'favicon' => self::ALLOWED_EXTENSIONS['favicon'],
            'css'     => self::ALLOWED_EXTENSIONS['css'],
            default   => self::ALLOWED_EXTENSIONS['image'],
        };

        if (! in_array($extension, $allowed)) {
            $this->errors[] = "Extensão não permitida: {$extension}. Permitidos: ".implode(', ', $allowed);

            return false;
        }

        return true;
    }

    /**
     * Valida MIME type.
     */
    private function validateMimeType(UploadedFile $file): bool
    {
        $mime = $file->getMimeType();

        if (! in_array($mime, self::ALLOWED_MIMES)) {
            $this->errors[] = "Tipo de arquivo não permitido: {$mime}";

            return false;
        }

        return true;
    }

    /**
     * Valida tamanho do arquivo.
     */
    private function validateSize(UploadedFile $file, string $fieldType): bool
    {
        $size = $file->getSize();
        $maxSize = self::MAX_SIZES[$fieldType] ?? self::MAX_SIZES['default'];

        if ($size > $maxSize) {
            $maxMB = round($maxSize / 1024 / 1024, 1);
            $sizeMB = round($size / 1024 / 1024, 2);
            $this->errors[] = "Arquivo muito grande: {$sizeMB}MB (máximo: {$maxMB}MB)";

            return false;
        }

        return true;
    }

    /**
     * Valida integridade do arquivo.
     */
    private function validateIntegrity(UploadedFile $file): bool
    {
        // Verifica se o arquivo pode ser lido
        if (! $file->isValid()) {
            $this->errors[] = 'Arquivo corrompido ou upload incompleto';

            return false;
        }

        $extension = strtolower($file->getClientOriginalExtension());

        // Para imagens raster, tenta carregar
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $imageInfo = @getimagesize($file->getPathname());
            if ($imageInfo === false) {
                $this->errors[] = 'Arquivo de imagem inválido ou corrompido';

                return false;
            }
        }

        return true;
    }

    /**
     * Verifica dimensões (apenas warnings).
     */
    private function checkDimensions(UploadedFile $file, string $fieldType): void
    {
        $extension = strtolower($file->getClientOriginalExtension());

        // SVG não tem dimensões fixas
        if ($extension === 'svg') {
            return;
        }

        // Só verifica imagens raster
        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return;
        }

        $imageInfo = @getimagesize($file->getPathname());
        if ($imageInfo === false) {
            return;
        }

        [$width, $height] = $imageInfo;
        $recommended = self::RECOMMENDED_DIMENSIONS[$fieldType] ?? null;

        if (! $recommended) {
            return;
        }

        if (isset($recommended['min_width']) && $width < $recommended['min_width']) {
            $this->warnings[] = "Largura recomendada mínima: {$recommended['min_width']}px (atual: {$width}px)";
        }

        if (isset($recommended['min_height']) && $height < $recommended['min_height']) {
            $this->warnings[] = "Altura recomendada mínima: {$recommended['min_height']}px (atual: {$height}px)";
        }

        if (isset($recommended['max_width']) && $width > $recommended['max_width']) {
            $this->warnings[] = "Largura recomendada máxima: {$recommended['max_width']}px (atual: {$width}px)";
        }

        if (isset($recommended['max_height']) && $height > $recommended['max_height']) {
            $this->warnings[] = "Altura recomendada máxima: {$recommended['max_height']}px (atual: {$height}px)";
        }
    }

    /**
     * Valida segurança (especialmente para SVG).
     */
    private function validateSecurity(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());

        // SVG pode conter scripts maliciosos
        if ($extension === 'svg') {
            $content = file_get_contents($file->getPathname());

            // Padrões perigosos em SVG
            $dangerousPatterns = [
                '/<script/i',
                '/javascript:/i',
                '/on\w+\s*=/i', // onclick, onload, etc.
                '/<foreignObject/i',
                '/xlink:href\s*=\s*["\'](?!#)/i', // external references
            ];

            foreach ($dangerousPatterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    $this->errors[] = 'SVG contém conteúdo potencialmente malicioso';
                    Log::warning('[ThemeAssetValidator] Potentially malicious SVG blocked', [
                        'filename' => $file->getClientOriginalName(),
                        'pattern'  => $pattern,
                    ]);

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Valida formato de cor hexadecimal.
     */
    private function isValidColor(string $color): bool
    {
        // Aceita #RGB, #RRGGBB, ou RRGGBB
        return (bool) preg_match('/^#?([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color);
    }

    /**
     * Retorna erros da última validação.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Retorna warnings da última validação.
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * Retorna todos os problemas (erros + warnings).
     */
    public function getAllIssues(): array
    {
        return [
            'errors'   => $this->errors,
            'warnings' => $this->warnings,
        ];
    }

    /**
     * Verifica se a última validação passou.
     */
    public function passed(): bool
    {
        return empty($this->errors);
    }

    /**
     * Retorna mensagem formatada de todos os problemas.
     */
    public function getFormattedMessage(): string
    {
        $messages = [];

        if (! empty($this->errors)) {
            $messages[] = 'Erros:';
            foreach ($this->errors as $error) {
                $messages[] = "  - {$error}";
            }
        }

        if (! empty($this->warnings)) {
            $messages[] = 'Avisos:';
            foreach ($this->warnings as $warning) {
                $messages[] = "  - {$warning}";
            }
        }

        return implode("\n", $messages);
    }
}
