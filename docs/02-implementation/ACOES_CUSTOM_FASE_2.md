# 📋 Ações Custom - Fase 2

**Objetivo**: Pequenos ajustes e refinamentos para implementar depois
**Formato**: Itens marcados com `#Ajuste` pelo usuário

---

## 📝 Lista de Ajustes

### 1. Dropdown "Theme Active" sempre em branco ✅ RESOLVIDO
**Data**: 22/12/2024 23:50
**Resolvido em**: 22/12/2024 23:55
**Problema**: O dropdown de ativação do tema fica sempre em branco, não mostrando se está ativo ou não.
**Status**: ✅ **CORRIGIDO** - Substituído componente x-admin por select nativo

**Localização**:
- Arquivo: `packages/Webkul/ThemeManager/Resources/views/admin/settings/theme/index.blade.php`
- Linhas: 55-65

**Solução implementada**:
- ❌ Removido componente `<x-admin::form.control-group.control>` (bug: ignora `:value`)
- ✅ Substituído por `<select>` nativo HTML com classes Tailwind
- ✅ Atributo `selected` aplicado corretamente
- ✅ Mantida mesma aparência visual

**Documentação**: [ENTREGA_DROPDOWN_IS_ACTIVE.md](ENTREGA_DROPDOWN_IS_ACTIVE.md)
**Tempo**: 25 minutos

---

### 2. Background de login aparece enorme na área admin ✅ RESOLVIDO
**Data**: 22/12/2024 23:50
**Resolvido em**: 22/12/2024 23:55 (antes deste #Ajuste ser criado)
**Problema**: Quando faz upload do background para tela de login, ele aparece enorme na parte admin (dashboard).
**Status**: ✅ **JÁ CORRIGIDO** - CSS global foi removido durante a saga do login background

**Localização**:
- Arquivo: `packages/Webkul/ThemeManager/Resources/views/components/theme-styles.blade.php`
- JavaScript: Linhas 549-581

**Causa (original)**:
- CSS aplicava em `body, .min-h-screen` GLOBALMENTE (linhas 445-477)
- JavaScript verificava `pathname.includes('login')` corretamente

**Solução implementada**:
- ❌ **REMOVIDO CSS COMPLETO** (linhas 445-477)
- ✅ **MANTIDO APENAS JavaScript** (linhas 549-581)
- ✅ JavaScript verifica URL: `if (pathname.includes('login'))`
- ✅ Background aplica **SOMENTE** na página de login

**Documentação**: [SAGA_LOGIN_BACKGROUND.md](SAGA_LOGIN_BACKGROUND.md) - Seção "DOR 3"
**Tempo**: Resolvido como parte da saga (3 horas total)

---

**Última atualização**: 22/12/2024 23:57
**Total de ajustes**: 2
**Resolvidos**: 2 ✅
**Pendentes**: 0
