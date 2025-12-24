# 📋 INSTRUÇÕES: Teste de Upload login_bg_image

**Data**: 22/12/2024
**Objetivo**: Descobrir por que upload não salva no banco

---

## ✅ PREPARAÇÃO CONCLUÍDA

### 1. ✅ Verificado Storage
```
Arquivos existentes em storage/app/public/theme-manager/:
- 1766361510_logo_main_xpPo9ckg.png
- 1766361682_logo_icon_OTfoFob2.png
- 1766362017_favicon_HnB4saB7.ico
- 1766362046_logo_light_wCFgYmTx.png

Resultado: NENHUM arquivo com "login_bg" no nome
```

**Conclusão**: ❌ Upload de login_bg_image NUNCA salvou arquivo no storage.

---

### 2. ✅ Teste Manual de Salvamento
```bash
php test_login_bg_upload.php
```

**Resultado**:
```
✅ SUCESSO: Salvamento manual funciona!
   Problema está no Repository ou Upload.
```

**Conclusão**: ✅ Model e banco funcionam. Problema é no Repository.

---

### 3. ✅ Debug Log Adicionado

**Arquivo**: `packages/Webkul/ThemeManager/src/Repositories/ThemeConfigRepository.php`

**Linhas 92-101**:
```php
// DEBUG - Remover depois
if ($field === 'login_bg_image') {
    \Log::info('🔍 DEBUG login_bg_image', [
        'field' => $field,
        'isset' => isset($data[$field]),
        'is_uploadedfile' => isset($data[$field]) && $data[$field] instanceof UploadedFile,
        'data_type' => isset($data[$field]) ? get_class($data[$field]) : 'not set',
        'all_data_keys' => array_keys($data),
    ]);
}
```

**Status**: ✅ Debug log inserido e caches limpos.

---

## 🧪 TESTE NECESSÁRIO

### Passo 1: Fazer Upload de login_bg_image

1. Acesse: http://127.0.0.1:8000/admin/settings/theme
2. Faça upload de qualquer imagem JPG/PNG em "Login Background Image"
3. Clique em "Save Settings"
4. Aguarde resposta

---

### Passo 2: Verificar Logs

Execute:
```bash
powershell.exe -Command "Get-Content 'C:\Users\Usuario\Desktop\Krayin-\laravel-crm\storage\logs\laravel.log' -Tail 50 | Select-String -Pattern 'DEBUG login_bg_image' -Context 0,3"
```

**Ou simplesmente**:
```bash
tail -50 storage/logs/laravel.log | grep "DEBUG login_bg"
```

---

### Passo 3: Analisar Resultado

#### ✅ Se aparecer o log:
```json
{
    "field": "login_bg_image",
    "isset": true,
    "is_uploadedfile": true,
    "data_type": "Illuminate\\Http\\UploadedFile",
    "all_data_keys": [...]
}
```

**Significa**: Upload chegou corretamente ao Repository.
**Próximo passo**: Verificar por que Repository não salvou (validação? extensão?).

---

#### ⚠️ Se aparecer:
```json
{
    "field": "login_bg_image",
    "isset": true,
    "is_uploadedfile": false,  ← PROBLEMA
    "data_type": "string",     ← Tipo errado!
}
```

**Significa**: Arquivo não é UploadedFile (merge falhou).
**Causa**: Controller não está fazendo `array_merge()` corretamente.

---

#### ❌ Se aparecer:
```json
{
    "field": "login_bg_image",
    "isset": false,  ← PROBLEMA
    "data_type": "not set",
}
```

**Significa**: Campo nem chegou ao Repository.
**Causa**: Controller não está enviando `login_bg_image` no array.

---

#### ❌ Se NÃO aparecer nenhum log:

**Significa**: Repository nem executou o foreach (erro antes).
**Causa**: Erro fatal ou exception antes do loop.

---

## 📊 DIAGNÓSTICO POR CENÁRIO

### Cenário A: `is_uploadedfile: true`
**Problema**: Validação de extensão ou tamanho.

**Solução**: Verificar método `isAllowedExtension()` na linha 115.

**Verificar também**:
- Linha 109: `if ($file->getSize() === 0)` - arquivo vazio?
- Linha 115: Extensão permitida?

---

### Cenário B: `is_uploadedfile: false`
**Problema**: Merge de arquivos não funciona.

**Solução**: Verificar ThemeController.php linha 103:
```php
array_merge($request->all(), $request->allFiles())
```

Pode estar sendo sobrescrito pelo `$request->all()`.

**Trocar para**:
```php
array_merge($request->allFiles(), $request->all())
```
(Ordem invertida - arquivos primeiro).

---

### Cenário C: `isset: false`
**Problema**: Campo não chega ao Repository.

**Solução**: Adicionar debug no Controller também:
```php
\Log::info('Controller antes de update', [
    'all_keys' => array_keys($request->all()),
    'files_keys' => array_keys($request->allFiles()),
    'merged_keys' => array_keys(array_merge($request->all(), $request->allFiles())),
]);
```

---

## 🚀 AÇÃO IMEDIATA

**FAÇA AGORA**:
1. Upload de imagem em http://127.0.0.1:8000/admin/settings/theme
2. Copie os logs do debug
3. Compartilhe o resultado

**Comando rápido**:
```bash
powershell.exe -Command "Get-Content 'C:\Users\Usuario\Desktop\Krayin-\laravel-crm\storage\logs\laravel.log' -Tail 100 | Select-String -Pattern 'DEBUG login_bg' -Context 0,5"
```

---

**Aguardando teste do usuário** ⏳
