# ✅ Checklist de Testes Restantes - ThemeManager

**Data**: 22/12/2024
**Status Atual**: Funcionalidades principais funcionando

---

## ✅ JÁ TESTADO E FUNCIONANDO

- [x] ✅ **Ativação do Tema** (Theme Active = Yes/No)
- [x] ✅ **Cores Customizadas** (6 cores)
  - color_primary
  - color_primary_dark
  - color_primary_light
  - color_success
  - color_warning
  - color_danger
- [x] ✅ **Logos** (funcionando via JavaScript)
  - logo_main
  - logo_light (dark mode)
  - logo_icon (mobile)
- [x] ✅ **Favicon** (funcionando)

---

## 🔄 PENDENTE DE TESTE

### 1️⃣ Login Page Customization

#### 1.1. Login Background Image
**Campos**:
```
- login_bg_image (upload de imagem)
- login_bg_zoom (50-200)
- login_bg_opacity (0-100)
- login_show_powered_by (Yes/No)
```

**Como testar**:
1. Acesse: http://127.0.0.1:8000/admin/settings/theme
2. Faça upload de imagem em "Login Background Image"
3. Configure Zoom (ex: 120)
4. Configure Opacity (ex: 70)
5. Desmarque "Show Powered By" se quiser
6. Salve
7. **Faça logout**: http://127.0.0.1:8000/admin/logout
8. Verifique se a página de login tem o background customizado

**Onde ver o resultado**: Página de login (`/admin/login`)

---

#### 1.2. Login Card Custom

**Campos**:
```
- login_card_enabled (Yes/No)
- login_card_bg_image (upload de imagem)
- login_card_bg_opacity (0-100)
- login_card_overlay_color (rgba color)
- login_card_title (texto)
- login_card_subtitle (texto)
- login_card_sparkles (Yes/No)
- login_card_help_link (Yes/No)
- login_card_support_email (email)
```

**Como testar**:
1. Ative "Enable Login Card" = Yes
2. Configure título: "Bem-vindo ao Sistema"
3. Configure subtítulo: "Acesse sua conta CRM"
4. Configure email de suporte: "suporte@empresa.com.br"
5. Faça upload de imagem de fundo do card (opcional)
6. Ative "Show Sparkles" (efeito visual)
7. Ative "Show Help Link"
8. Salve
9. **Faça logout**
10. Verifique se o card de login tem as customizações

**Onde ver o resultado**: Página de login (`/admin/login`)

**O que esperar**:
- Card com título e subtítulo customizados
- Sparkles animados (se ativado)
- Link "Precisa de ajuda?" (se ativado)
- Background do card customizado (se enviou imagem)

---

### 2️⃣ Empty States Customization

**Empty States** são as imagens SVG que aparecem quando não há dados em uma seção.

**Campos disponíveis** (9 empty states):
```
- empty_state_activities
- empty_state_calls
- empty_state_emails
- empty_state_meetings
- empty_state_notes
- empty_state_organizations
- empty_state_persons
- empty_state_leads
- empty_state_products
```

**Como testar**:

#### Passo 1: Fazer Upload dos SVGs
1. Acesse: http://127.0.0.1:8000/admin/settings/theme
2. Role até a seção "Empty States"
3. Faça upload de um SVG customizado para cada estado vazio
4. Salve

#### Passo 2: Verificar Cada Empty State

**a) Activities**
- URL: http://127.0.0.1:8000/admin/activities
- Se não houver activities cadastradas, verá o empty state
- Deve mostrar seu SVG customizado

**b) Calls**
- URL: http://127.0.0.1:8000/admin/activities?type=call
- Empty state de ligações

**c) Emails**
- URL: http://127.0.0.1:8000/admin/mail/inbox
- Empty state de emails

**d) Meetings**
- URL: http://127.0.0.1:8000/admin/activities?type=meeting
- Empty state de reuniões

**e) Notes**
- URL: http://127.0.0.1:8000/admin/contacts/persons/{id}/notes
- Acesse um contato e vá em notas (se vazio)

**f) Organizations**
- URL: http://127.0.0.1:8000/admin/contacts/organizations
- Se não houver organizações

**g) Persons**
- URL: http://127.0.0.1:8000/admin/contacts/persons
- Se não houver pessoas

**h) Leads**
- URL: http://127.0.0.1:8000/admin/leads
- Se não houver leads

**i) Products**
- URL: http://127.0.0.1:8000/admin/products
- Se não houver produtos

**Dica**: Se já tem dados cadastrados, você pode:
1. Criar um novo usuário/conta de teste
2. Ou usar filtros para mostrar resultado vazio
3. Ou inspecionar elemento para verificar o SVG

---

### 3️⃣ Toggle Ativação/Desativação

**Teste de Integração**: Verificar se desativar o tema remove as customizações

**Como testar**:
1. Com tema customizado ativo
2. Mude "Theme Active" para "No"
3. Salve
4. Recarregue página (Ctrl+Shift+R)

**O que deve acontecer**:
- ❌ Logos voltam ao padrão Krayin
- ❌ Cores voltam ao azul padrão
- ❌ Favicon volta ao padrão
- ❌ Login page volta ao padrão
- ❌ Empty states voltam ao padrão

**Depois ative novamente**:
1. Mude "Theme Active" para "Yes"
2. Salve
3. Recarregue

**O que deve acontecer**:
- ✅ Tudo volta ao customizado

---

### 4️⃣ Deletar Logos/Imagens

**Como testar**:
1. Com um logo já enviado
2. Marque checkbox "Delete Logo Main"
3. Salve
4. Verifique se logo voltou ao padrão

**Repetir para**:
- Logo Light
- Logo Icon
- Favicon
- Login Background
- Login Card Background
- Cada Empty State

---

### 5️⃣ Validações de Segurança

#### 5.1. Teste de SVG Malicioso
**Como testar**:
1. Crie um arquivo SVG com JavaScript:
```xml
<svg xmlns="http://www.w3.org/2000/svg">
  <script>alert("XSS")</script>
  <circle cx="50" cy="50" r="40"/>
</svg>
```
2. Tente fazer upload
3. **Esperado**: Arquivo é sanitizado (script removido)

#### 5.2. Teste de Cor Inválida
**Como testar**:
1. Tente salvar cor inválida: `#ZZZZZZ`
2. **Esperado**: Validação rejeita

#### 5.3. Teste de Arquivo Não Permitido
**Como testar**:
1. Tente fazer upload de `.exe` ou `.php`
2. **Esperado**: Validação rejeita

---

### 6️⃣ Performance e Cache

**Como testar**:
1. Acesse uma página do admin
2. Abra DevTools (F12) → Network
3. Recarregue página
4. Verifique se CSS do tema foi carregado
5. Recarregue novamente
6. **Esperado**: Cache funcionando (304 Not Modified)

---

### 7️⃣ Responsividade

**Como testar em Mobile**:
1. Abra DevTools (F12)
2. Clique no ícone de dispositivo móvel
3. Selecione iPhone/Android
4. Navegue pelo admin

**O que verificar**:
- Logo mobile aparece (se configurou `logo_icon`)
- Layout responsivo funciona
- Cores aplicadas corretamente
- Touch funciona

---

### 8️⃣ Dark Mode (se Krayin suportar)

**Como testar**:
1. Se Krayin tem toggle de dark mode
2. Ative dark mode
3. Verifique se `logo_light` aparece
4. Verifique se cores se adaptam

---

## 📊 RESUMO DO QUE TESTAR

### Prioridade ALTA (testar primeiro):
1. ✅ Login Background (1.1)
2. ✅ Login Card (1.2)
3. ✅ Toggle ativação/desativação (3)

### Prioridade MÉDIA:
4. ⏳ Empty States (2) - pelo menos 2-3
5. ⏳ Deletar imagens (4)
6. ⏳ Validações de segurança (5)

### Prioridade BAIXA:
7. ⏳ Performance (6)
8. ⏳ Responsividade (7)
9. ⏳ Dark mode (8)

---

## 🧪 SCRIPT DE TESTE RÁPIDO

Para testar rapidamente se tudo está configurado:

```bash
# Via terminal (tinker)
php artisan tinker

# Verificar configuração atual
$config = \Webkul\ThemeManager\Models\ThemeConfig::getInstance();
echo "Tema ativo: " . ($config->is_active ? "SIM" : "NÃO") . "\n";
echo "Logo main: " . ($config->logo_main ?: "não configurado") . "\n";
echo "Login BG: " . ($config->login_bg_image ?: "não configurado") . "\n";
echo "Login Card: " . ($config->login_card_enabled ? "ATIVO" : "inativo") . "\n";
echo "Empty state leads: " . ($config->empty_state_leads ?: "não configurado") . "\n";
```

---

## 📋 CHECKLIST VISUAL RESUMIDO

### Funcionalidades Básicas:
- [x] ✅ Tema ativo/inativo
- [x] ✅ 6 cores customizadas
- [x] ✅ Logo main
- [x] ✅ Logo light (dark mode)
- [x] ✅ Logo icon (mobile)
- [x] ✅ Favicon

### Funcionalidades Avançadas (TESTAR):
- [ ] ⏳ Login background image
- [ ] ⏳ Login background zoom/opacity
- [ ] ⏳ Login card customizado
- [ ] ⏳ Login card sparkles
- [ ] ⏳ Login card help link
- [ ] ⏳ 9 empty states SVG
- [ ] ⏳ Delete de logos/imagens
- [ ] ⏳ Validação de segurança (SVG/cores)
- [ ] ⏳ Cache funcionando
- [ ] ⏳ Responsivo em mobile
- [ ] ⏳ Dark mode (se aplicável)

---

## 💡 DICAS PARA TESTAR

### Login Page:
```
Para ver a página de login, você precisa fazer logout:
http://127.0.0.1:8000/admin/logout
```

### Empty States:
```
Se você tem muitos dados, use filtros para forçar resultado vazio
ou crie uma conta de teste nova
```

### SVGs para Teste:
```
Baixe SVGs grátis de:
- https://undraw.co (illustrations)
- https://www.svgrepo.com (icons)
- https://heroicons.com (icons)
```

### DevTools Console:
```
F12 → Console
Procure por: "🎨 ThemeManager: Iniciando..."
Verifique se não há erros JavaScript
```

---

## 🎯 PRÓXIMO PASSO SUGERIDO

**TESTE AGORA** (ordem recomendada):

1. **Login Background**:
   - Upload de imagem
   - Configurar zoom e opacity
   - Logout e verificar

2. **Login Card**:
   - Ativar card
   - Configurar textos
   - Logout e verificar

3. **Empty States** (escolha 2-3):
   - Leads (mais fácil de testar)
   - Products
   - Persons

4. **Toggle Ativação**:
   - Desativar tema
   - Verificar se voltou ao padrão
   - Ativar novamente

---

**Última atualização**: 22/12/2024
**Próxima ação**: Testar Login Page e Login Card
