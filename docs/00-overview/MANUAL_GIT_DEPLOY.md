# Manual de Git e Deploy - Guia Completo para Iniciantes

> Este manual explica de forma didática como usar Git e GitHub para gerenciar e fazer deploy do seu projeto Krayin CRM.

**Última atualização:** 2024-12-24  
**Autor:** DevLead  
**Nível:** Iniciante a Intermediário

---

## Índice

1. [Conceitos Básicos](#1-conceitos-básicos)
2. [Estrutura do Projeto](#2-estrutura-do-projeto)
3. [Comandos Essenciais](#3-comandos-essenciais)
4. [Fluxo de Trabalho](#4-fluxo-de-trabalho)
5. [Instalação no Servidor](#5-instalação-no-servidor)
6. [Atualizações e Deploy](#6-atualizações-e-deploy)
7. [Branches - Linhas do Tempo](#7-branches---linhas-do-tempo)
8. [Situações Comuns](#8-situações-comuns)
9. [Resolução de Problemas](#9-resolução-de-problemas)
10. [Comandos de Referência Rápida](#10-comandos-de-referência-rápida)

---

## 1. Conceitos Básicos

### O que é Git?

Git é um **sistema de controle de versão**. Pense nele como um "histórico de alterações" do seu código.

```
Sem Git:
  projeto_v1.zip
  projeto_v2.zip
  projeto_v2_final.zip
  projeto_v2_final_FINAL.zip  ← Confuso!

Com Git:
  projeto/
    └── (Git guarda todas as versões internamente)
        ├── versão 1 (janeiro)
        ├── versão 2 (fevereiro)
        ├── versão 3 (março)
        └── versão 4 (atual)  ← Organizado!
```

### O que é GitHub?

GitHub é um **site que armazena seu código na nuvem**.

```
┌─────────────────┐                    ┌─────────────────┐
│  SEU COMPUTADOR │                    │     GITHUB      │
│                 │                    │     (nuvem)     │
│  Onde você      │  ◄── internet ──►  │  Backup seguro  │
│  trabalha       │                    │  Compartilhar   │
│                 │                    │  Colaborar      │
└─────────────────┘                    └─────────────────┘
```

### Analogia: Git é como Google Docs

| Google Docs | Git/GitHub |
|-------------|------------|
| Documento | Código do projeto |
| Histórico de versões | Commits |
| Salvar | git commit |
| Sincronizar na nuvem | git push |
| Abrir em outro PC | git pull / git clone |

---

## 2. Estrutura do Projeto

### Repositórios

```
REPOSITÓRIOS DO PROJETO:

origin (Krayin oficial)
  └── https://github.com/krayin/laravel-crm
      └── Código original do Krayin (não mexemos aqui)

myfork (Seu fork/cópia)
  └── https://github.com/vitorbb1989/Krayingproject
      ├── 2.1                    ← Branch estável (backup)
      └── theme-refactor-clean   ← Branch com sistema de temas
```

### O que é Fork?

Fork é uma **cópia do projeto** para sua conta.

```
KRAYIN OFICIAL                    SEU FORK
┌─────────────────┐    fork     ┌─────────────────┐
│ krayin/         │ ─────────►  │ vitorbb1989/    │
│ laravel-crm     │             │ Krayingproject  │
│                 │             │                 │
│ (não pode      │             │ (você pode      │
│  modificar)    │             │  modificar!)    │
└─────────────────┘             └─────────────────┘
```

---

## 3. Comandos Essenciais

### Os 5 Comandos que Você Mais Vai Usar

```bash
# 1. VER STATUS (o que mudou?)
git status

# 2. BAIXAR ATUALIZAÇÕES (puxar da nuvem)
git pull

# 3. ADICIONAR ARQUIVOS (preparar para salvar)
git add .

# 4. SALVAR ALTERAÇÕES (criar ponto de salvamento)
git commit -m "descrição do que foi feito"

# 5. ENVIAR PARA NUVEM (backup no GitHub)
git push
```

### Explicação Visual

```
VOCÊ EDITA          PREPARA            SALVA              ENVIA
UM ARQUIVO       PARA COMMIT         LOCALMENTE         PRA NUVEM
     │                │                  │                  │
     ▼                ▼                  ▼                  ▼
  arquivo.php ──► git add . ──► git commit -m "..." ──► git push
     │                │                  │                  │
  (modificado)    (staged)          (committed)         (pushed)
```

---

## 4. Fluxo de Trabalho

### Ciclo Completo de Desenvolvimento

```
┌────────────────────────────────────────────────────────────────┐
│                     CICLO DE DESENVOLVIMENTO                    │
└────────────────────────────────────────────────────────────────┘

     ┌──────────┐
     │ 1. EDITA │  Você modifica arquivos no seu computador
     └────┬─────┘
          │
          ▼
     ┌──────────┐
     │ 2. TESTA │  Verifica se funciona localmente
     └────┬─────┘
          │
          ▼
     ┌──────────┐
     │ 3. ADD   │  git add . (prepara arquivos)
     └────┬─────┘
          │
          ▼
     ┌──────────┐
     │ 4. COMMIT│  git commit -m "mensagem" (salva versão)
     └────┬─────┘
          │
          ▼
     ┌──────────┐
     │ 5. PUSH  │  git push (envia para GitHub)
     └────┬─────┘
          │
          ▼
     ┌──────────┐
     │ 6. DEPLOY│  No servidor: git pull (atualiza produção)
     └──────────┘
```

### Exemplo Prático

```bash
# Você editou um arquivo...

# 1. Ver o que mudou
git status
#    modified: app/Http/Controllers/MeuController.php

# 2. Adicionar as mudanças
git add .

# 3. Salvar com uma mensagem
git commit -m "fix: corrige bug no controller"

# 4. Enviar para GitHub
git push

# 5. No servidor de produção
ssh usuario@servidor
cd /var/www/projeto
git pull
php artisan cache:clear
```

---

## 5. Instalação no Servidor

### Primeira Instalação (Clone)

Use quando for instalar o projeto pela **primeira vez** no servidor.

```bash
# 1. Conectar no servidor via SSH
ssh usuario@seu-servidor.com

# 2. Ir para pasta de sites
cd /var/www

# 3. Clonar (baixar) o repositório
git clone https://github.com/vitorbb1989/Krayingproject.git krayin

# 4. Entrar na pasta
cd krayin

# 5. Mudar para a branch correta
git checkout theme-refactor-clean

# 6. Instalar dependências PHP
composer install --no-dev --optimize-autoloader

# 7. Copiar arquivo de configuração
cp .env.example .env

# 8. Editar configurações (banco, email, etc)
nano .env

# 9. Gerar chave da aplicação
php artisan key:generate

# 10. Rodar migrações do banco
php artisan migrate

# 11. Criar link do storage
php artisan storage:link

# 12. Limpar caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 13. Ajustar permissões
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Diagrama da Instalação

```
GITHUB                              SERVIDOR
┌─────────────────┐                ┌─────────────────┐
│                 │   git clone    │                 │
│  Krayingproject │ ─────────────► │  /var/www/      │
│                 │                │  krayin/        │
└─────────────────┘                └─────────────────┘
                                          │
                                          ▼
                                   composer install
                                          │
                                          ▼
                                   php artisan migrate
                                          │
                                          ▼
                                      PRONTO!
```

---

## 6. Atualizações e Deploy

### Atualizando o Servidor (Pull)

Use quando quiser **atualizar** o servidor com novas mudanças.

```bash
# 1. Conectar no servidor
ssh usuario@seu-servidor.com

# 2. Ir para pasta do projeto
cd /var/www/krayin

# 3. Puxar atualizações
git pull

# 4. Instalar novas dependências (se houver)
composer install --no-dev --optimize-autoloader

# 5. Rodar novas migrações (se houver)
php artisan migrate --force

# 6. Limpar caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# 7. Pronto!
echo "Deploy concluído!"
```

### Script de Deploy Automático

Salve como `deploy.sh` no servidor:

```bash
#!/bin/bash
# deploy.sh - Script de deploy simplificado

echo "=== Iniciando Deploy ==="

cd /var/www/krayin

echo "1. Puxando atualizações..."
git pull

echo "2. Instalando dependências..."
composer install --no-dev --optimize-autoloader

echo "3. Rodando migrações..."
php artisan migrate --force

echo "4. Limpando caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo "=== Deploy Concluído! ==="
```

Executar:
```bash
chmod +x deploy.sh
./deploy.sh
```

### Comparação: Clone vs Pull

| Situação | Comando | Quando usar |
|----------|---------|-------------|
| Primeira instalação | `git clone` | Servidor novo, nunca teve o código |
| Atualização | `git pull` | Servidor já tem o código, quer atualizar |

```
CLONE (primeira vez):
  GitHub ════════════════════► Servidor
           copia TUDO

PULL (atualizações):
  GitHub ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ► Servidor
          só o que mudou
```

---

## 7. Branches - Linhas do Tempo

### O que são Branches?

Branches são **linhas do tempo paralelas** do seu código.

```
Imagine um livro:

  BRANCH "2.1" (versão estável)
  ────────────────────────────────────────►
  Cap 1    Cap 2    Cap 3    Cap 4
  
  
  BRANCH "theme-refactor-clean" (nova versão)
  ────────────────────────────────────────►
  Cap 1    Cap 2    Cap 3    Cap 4    Cap 5 (novo!)
```

### Suas Branches Atuais

```
myfork/
├── 2.1                    ← Versão estável (backup seguro)
│   └── Código original + ThemeManager básico
│
└── theme-refactor-clean   ← Versão nova (usar esta!)
    └── Tudo de 2.1 + Sistema de temas completo
```

### Comandos de Branch

```bash
# Ver em qual branch você está
git branch
#   2.1
# * theme-refactor-clean   ← asterisco = atual

# Ver todas as branches (locais e remotas)
git branch -a

# Trocar de branch
git checkout 2.1
git checkout theme-refactor-clean

# Criar nova branch
git checkout -b minha-nova-feature
```

### Quando Usar Cada Branch

| Branch | Quando usar | Segurança |
|--------|-------------|-----------|
| `2.1` | Emergência, rollback | ⭐⭐⭐ Mais segura |
| `theme-refactor-clean` | Produção com temas | ⭐⭐ Testada |

---

## 8. Situações Comuns

### Situação 1: "Fiz alteração e quero salvar"

```bash
# 1. Ver o que mudou
git status

# 2. Adicionar tudo
git add .

# 3. Salvar
git commit -m "descrição clara do que fez"

# 4. Enviar para GitHub
git push
```

### Situação 2: "Quero atualizar meu servidor"

```bash
# No servidor:
cd /var/www/krayin
git pull
php artisan cache:clear
```

### Situação 3: "Algo deu errado, quero voltar"

```bash
# Opção A: Descartar mudanças locais (não commitadas)
git checkout -- .

# Opção B: Voltar para último commit
git reset --hard HEAD

# Opção C: Voltar para branch estável
git checkout 2.1
```

### Situação 4: "Quero ver o histórico"

```bash
# Ver últimos commits
git log --oneline -10

# Ver o que mudou em cada commit
git log --oneline --stat -5
```

### Situação 5: "Conflito ao fazer pull"

```bash
# Git avisa: "CONFLICT in arquivo.php"

# 1. Abrir o arquivo e resolver manualmente
#    (procure por <<<<<<< e >>>>>>> no arquivo)

# 2. Depois de resolver
git add arquivo.php
git commit -m "fix: resolve conflito"
git push
```

### Situação 6: "Quero saber se estou atualizado"

```bash
# Buscar informações do GitHub (sem baixar)
git fetch

# Comparar local vs remoto
git status
#    Your branch is behind 'origin/main' by 3 commits
#    (seu código está 3 commits atrás)

# Atualizar
git pull
```

---

## 9. Resolução de Problemas

### Problema: "Permission denied"

```bash
# Erro: Permission denied (publickey)

# Solução: Configurar chave SSH
ssh-keygen -t ed25519 -C "seu-email@exemplo.com"
cat ~/.ssh/id_ed25519.pub
# Copie a chave e adicione no GitHub > Settings > SSH Keys
```

### Problema: "Não consigo fazer push"

```bash
# Erro: failed to push some refs

# Causa: Seu código está desatualizado

# Solução:
git pull --rebase
git push
```

### Problema: "Arquivo muito grande"

```bash
# Erro: File X is 100+ MB; exceeds GitHub's limit

# Solução: Adicionar ao .gitignore ANTES de commitar
echo "arquivo-grande.zip" >> .gitignore
git rm --cached arquivo-grande.zip
git commit -m "remove arquivo grande"
git push
```

### Problema: "Detached HEAD"

```bash
# Aviso: You are in 'detached HEAD' state

# Significado: Você não está em uma branch

# Solução: Voltar para uma branch
git checkout theme-refactor-clean
```

### Problema: "Mudanças locais seriam sobrescritas"

```bash
# Erro: Your local changes would be overwritten by merge

# Opção A: Salvar mudanças temporariamente
git stash
git pull
git stash pop

# Opção B: Descartar mudanças locais
git checkout -- .
git pull
```

---

## 10. Comandos de Referência Rápida

### Comandos do Dia a Dia

```bash
# ═══════════════════════════════════════════════════════════
# BÁSICOS
# ═══════════════════════════════════════════════════════════

git status              # Ver estado atual
git pull                # Baixar atualizações
git add .               # Adicionar todos arquivos
git commit -m "msg"     # Salvar com mensagem
git push                # Enviar para GitHub

# ═══════════════════════════════════════════════════════════
# BRANCHES
# ═══════════════════════════════════════════════════════════

git branch              # Listar branches
git checkout NOME       # Trocar de branch
git checkout -b NOME    # Criar branch nova

# ═══════════════════════════════════════════════════════════
# HISTÓRICO
# ═══════════════════════════════════════════════════════════

git log --oneline -10   # Ver últimos 10 commits
git diff                # Ver mudanças não commitadas
git show COMMIT         # Ver detalhes de um commit

# ═══════════════════════════════════════════════════════════
# DESFAZER
# ═══════════════════════════════════════════════════════════

git checkout -- .       # Descartar mudanças não salvas
git reset --hard HEAD   # Voltar ao último commit
git revert COMMIT       # Desfazer um commit específico

# ═══════════════════════════════════════════════════════════
# INFORMAÇÕES
# ═══════════════════════════════════════════════════════════

git remote -v           # Ver repositórios remotos
git branch -a           # Ver todas as branches
git log --graph         # Ver histórico visual
```

### Fluxo Visual Completo

```
┌─────────────────────────────────────────────────────────────────┐
│                    FLUXO COMPLETO DO GIT                        │
└─────────────────────────────────────────────────────────────────┘

  WORKING DIR          STAGING            LOCAL             REMOTE
  (seus arquivos)      (preparados)      (commits)         (GitHub)
       │                   │                 │                 │
       │                   │                 │                 │
       │    git add .      │                 │                 │
       │ ────────────────► │                 │                 │
       │                   │                 │                 │
       │                   │  git commit     │                 │
       │                   │ ──────────────► │                 │
       │                   │                 │                 │
       │                   │                 │   git push      │
       │                   │                 │ ───────────────►│
       │                   │                 │                 │
       │                   │                 │   git pull      │
       │ ◄─────────────────┼─────────────────┼─────────────────│
       │                   │                 │                 │
```

---

## Checklist de Deploy

### Antes do Deploy

```
[ ] Código testado localmente
[ ] Todos os commits feitos (git status limpo)
[ ] Push feito para GitHub
[ ] Backup do banco de dados (se necessário)
```

### Durante o Deploy

```bash
# No servidor:
cd /var/www/krayin
git pull
composer install --no-dev
php artisan migrate --force
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Depois do Deploy

```
[ ] Site abrindo normalmente
[ ] Login funcionando
[ ] Funcionalidades principais OK
[ ] Verificar logs de erro
```

---

## Glossário

| Termo | Significado |
|-------|-------------|
| **Repository (Repo)** | Pasta do projeto com histórico Git |
| **Clone** | Copiar repositório do GitHub para seu PC |
| **Commit** | Salvar uma versão do código |
| **Push** | Enviar commits para o GitHub |
| **Pull** | Baixar atualizações do GitHub |
| **Branch** | Linha do tempo paralela do código |
| **Merge** | Juntar duas branches |
| **Fork** | Cópia de um repositório para sua conta |
| **Remote** | Repositório na nuvem (GitHub) |
| **Origin** | Nome padrão do remote principal |
| **HEAD** | Posição atual no histórico |
| **Staging** | Área de preparação antes do commit |

---

## Links Úteis

- [Seu Repositório](https://github.com/vitorbb1989/Krayingproject)
- [Branch de Produção](https://github.com/vitorbb1989/Krayingproject/tree/theme-refactor-clean)
- [GitHub Docs](https://docs.github.com/pt)
- [Git Cheat Sheet](https://education.github.com/git-cheat-sheet-education.pdf)

---

*Manual criado para o projeto Krayin CRM - Theme System*
