# Krayin CRM - Guia de Deploy com Docker Swarm

## Índice

1. [Visão Geral](#visão-geral)
2. [Pré-requisitos](#pré-requisitos)
3. [Arquitetura](#arquitetura)
4. [Preparação do Ambiente](#preparação-do-ambiente)
5. [Build da Imagem](#build-da-imagem)
6. [Deploy no Swarm](#deploy-no-swarm)
7. [Configuração de Secrets](#configuração-de-secrets)
8. [Scaling e Alta Disponibilidade](#scaling-e-alta-disponibilidade)
9. [Monitoramento](#monitoramento)
10. [Troubleshooting](#troubleshooting)
11. [Comandos Úteis](#comandos-úteis)

---

## Visão Geral

Este guia documenta o processo de deploy do Krayin CRM em um cluster Docker Swarm para ambiente de produção.

### Componentes do Stack

| Serviço | Imagem | Réplicas | Função |
|---------|--------|----------|--------|
| **app** | krayin/crm | 2+ | PHP-FPM (aplicação) |
| **nginx** | nginx:1.25-alpine | 2+ | Web server / Reverse proxy |
| **mysql** | mysql:8.0 | 1 | Banco de dados |
| **redis** | redis:7-alpine | 1 | Cache, sessões, filas |
| **queue** | krayin/crm | 2+ | Workers de fila |
| **scheduler** | krayin/crm | 1 | Tarefas agendadas |
| **traefik** | traefik:v2.10 | global | Ingress / SSL |

---

## Pré-requisitos

### Hardware Mínimo (Produção)

```
Manager Nodes: 3 (para HA)
Worker Nodes: 2+
RAM por node: 4GB mínimo
CPU por node: 2 cores mínimo
Disco: 50GB+ SSD
```

### Software

```bash
# Versões mínimas
Docker Engine: 20.10+
Docker Compose: 2.0+

# Verificar versão
docker --version
docker compose version
```

### Rede

```
Portas abertas entre nodes:
- TCP 2377 (Swarm management)
- TCP/UDP 7946 (Node communication)
- UDP 4789 (Overlay network)
- TCP 80, 443 (HTTP/HTTPS)
```

---

## Arquitetura

```
                    ┌──────────────────────────────────────────┐
                    │              INTERNET                    │
                    └────────────────┬─────────────────────────┘
                                     │
                    ┌────────────────▼─────────────────────────┐
                    │           TRAEFIK (Global)               │
                    │      Load Balancer + SSL Termination     │
                    └────────────────┬─────────────────────────┘
                                     │
         ┌───────────────────────────┼───────────────────────────┐
         │                           │                           │
┌────────▼────────┐        ┌─────────▼────────┐        ┌────────▼────────┐
│   NGINX (1)     │        │   NGINX (2)      │        │   NGINX (N)     │
│   Port 80       │        │   Port 80        │        │   Port 80       │
└────────┬────────┘        └─────────┬────────┘        └────────┬────────┘
         │                           │                           │
         └───────────────────────────┼───────────────────────────┘
                                     │
         ┌───────────────────────────┼───────────────────────────┐
         │                           │                           │
┌────────▼────────┐        ┌─────────▼────────┐        ┌────────▼────────┐
│    APP (1)      │        │    APP (2)       │        │    APP (N)      │
│   PHP-FPM       │        │   PHP-FPM        │        │   PHP-FPM       │
│   Port 9000     │        │   Port 9000      │        │   Port 9000     │
└────────┬────────┘        └─────────┬────────┘        └────────┬────────┘
         │                           │                           │
         └───────────────────────────┼───────────────────────────┘
                                     │
              ┌──────────────────────┼──────────────────────┐
              │                      │                      │
     ┌────────▼────────┐    ┌────────▼────────┐    ┌───────▼───────┐
     │     MYSQL       │    │     REDIS       │    │    QUEUE      │
     │   (Primary)     │    │    (Cache)      │    │   Workers     │
     └─────────────────┘    └─────────────────┘    └───────────────┘
```

---

## Preparação do Ambiente

### 1. Inicializar o Swarm (Manager Node)

```bash
# No primeiro manager node
docker swarm init --advertise-addr <IP_DO_MANAGER>

# Saída mostrará token para workers
# docker swarm join --token SWMTKN-xxx <IP>:2377
```

### 2. Adicionar Worker Nodes

```bash
# Em cada worker node, execute o comando do passo anterior
docker swarm join --token SWMTKN-xxx <MANAGER_IP>:2377
```

### 3. Criar Labels nos Nodes

```bash
# No manager, marque nodes para serviços específicos
docker node update --label-add db=true <NODE_ID_PARA_DB>
docker node update --label-add role=worker <NODE_ID_WORKER>
```

### 4. Criar Rede Overlay

```bash
# Rede para Traefik (externa)
docker network create --driver overlay --attachable traefik-public

# Verificar
docker network ls
```

### 5. Criar Diretórios de Dados

```bash
# Em TODOS os nodes (ou usar storage distribuído)
sudo mkdir -p /data/krayin/{mysql,redis,storage}
sudo chown -R 1000:1000 /data/krayin/storage
sudo chown -R 999:999 /data/krayin/mysql
```

---

## Build da Imagem

### 1. Clone o Repositório

```bash
git clone https://github.com/vitorbb1989/Krayingproject.git
cd Krayingproject
git checkout theme-refactor-clean
```

### 2. Build Local (Desenvolvimento)

```bash
# Build padrão
docker build -t krayin/crm:latest .

# Com tag de versão
docker build -t krayin/crm:2.1.5 .
```

### 3. Push para Registry (Produção)

```bash
# Docker Hub
docker login
docker tag krayin/crm:latest seu-usuario/krayin-crm:latest
docker push seu-usuario/krayin-crm:latest

# Registry privado
docker tag krayin/crm:latest registry.exemplo.com/krayin/crm:latest
docker push registry.exemplo.com/krayin/crm:latest
```

---

## Configuração de Secrets

### 1. Criar Secrets

```bash
# APP_KEY (gerar com Laravel)
php artisan key:generate --show | docker secret create app_key -

# Ou manualmente
echo "base64:sua-chave-aqui" | docker secret create app_key -

# Senha do banco
echo "senha_segura_mysql" | docker secret create db_password -

# Senha root do banco
echo "senha_root_mysql" | docker secret create db_root_password -
```

### 2. Listar Secrets

```bash
docker secret ls
```

### 3. Inspecionar Secret (metadados apenas)

```bash
docker secret inspect app_key
```

---

## Deploy no Swarm

### 1. Configurar Variáveis de Ambiente

```bash
# Criar arquivo .env para o stack
cp .env.docker .env

# Editar com suas configurações
nano .env
```

### 2. Deploy do Stack

```bash
# Deploy inicial
docker stack deploy -c docker-stack.yml krayin

# Com arquivo de ambiente
docker stack deploy -c docker-stack.yml --env-file .env krayin
```

### 3. Verificar Status

```bash
# Ver todos os serviços
docker stack services krayin

# Ver tarefas (containers)
docker stack ps krayin

# Ver logs de um serviço
docker service logs krayin_app -f
```

### 4. Executar Migrations

```bash
# Opção 1: Entrar em um container
docker exec -it $(docker ps -q -f name=krayin_app) bash
php artisan migrate --force

# Opção 2: Via docker service
docker service update --env-add RUN_MIGRATIONS=true krayin_app
# Aguardar e depois remover
docker service update --env-rm RUN_MIGRATIONS krayin_app
```

---

## Scaling e Alta Disponibilidade

### Escalar Serviços

```bash
# Escalar aplicação para 5 réplicas
docker service scale krayin_app=5

# Escalar múltiplos serviços
docker service scale krayin_app=5 krayin_nginx=5 krayin_queue=3
```

### Atualizar Imagem (Rolling Update)

```bash
# Atualizar com nova versão
docker service update --image krayin/crm:2.1.6 krayin_app

# Atualizar com rollback automático em caso de falha
docker service update \
    --image krayin/crm:2.1.6 \
    --update-failure-action rollback \
    krayin_app
```

### Rollback Manual

```bash
# Voltar para versão anterior
docker service rollback krayin_app
```

---

## Monitoramento

### Healthcheck Status

```bash
# Ver health de todos os containers
docker ps --format "table {{.Names}}\t{{.Status}}"

# Inspecionar healthcheck específico
docker inspect --format='{{json .State.Health}}' <CONTAINER_ID> | jq
```

### Logs Centralizados

```bash
# Logs de um serviço
docker service logs krayin_app -f --tail 100

# Logs de todos os serviços
docker service logs krayin_app 2>&1 | head -50
docker service logs krayin_nginx 2>&1 | head -50
docker service logs krayin_mysql 2>&1 | head -50
```

### Métricas

```bash
# Uso de recursos por container
docker stats

# Uso de recursos por serviço
docker service ps krayin_app --format "table {{.Name}}\t{{.Node}}\t{{.CurrentState}}"
```

---

## Troubleshooting

### Container não inicia

```bash
# Ver logs do serviço
docker service logs krayin_app --tail 100

# Ver estado das tarefas
docker service ps krayin_app --no-trunc

# Inspecionar erro específico
docker inspect <TASK_ID>
```

### Problemas de Rede

```bash
# Verificar redes
docker network ls
docker network inspect krayin-internal

# Testar conectividade
docker exec -it <CONTAINER> ping mysql
docker exec -it <CONTAINER> ping redis
```

### Problemas de Volume

```bash
# Listar volumes
docker volume ls

# Inspecionar volume
docker volume inspect krayin-mysql-data

# Verificar permissões no host
ls -la /data/krayin/
```

### Reset Completo

```bash
# CUIDADO: Remove tudo!
docker stack rm krayin

# Aguardar remoção
watch docker stack ps krayin

# Limpar volumes (PERDA DE DADOS!)
docker volume prune -f
```

---

## Comandos Úteis

### Stack Management

```bash
# Deploy
docker stack deploy -c docker-stack.yml krayin

# Remover
docker stack rm krayin

# Listar stacks
docker stack ls

# Listar serviços do stack
docker stack services krayin

# Listar tasks do stack
docker stack ps krayin
```

### Service Management

```bash
# Escalar
docker service scale krayin_app=3

# Atualizar
docker service update --image krayin/crm:v2 krayin_app

# Rollback
docker service rollback krayin_app

# Forçar atualização
docker service update --force krayin_app

# Logs
docker service logs -f krayin_app
```

### Execução de Comandos

```bash
# Entrar no container
docker exec -it $(docker ps -q -f name=krayin_app) bash

# Executar artisan
docker exec -it $(docker ps -q -f name=krayin_app) php artisan migrate:status

# Limpar cache
docker exec -it $(docker ps -q -f name=krayin_app) php artisan cache:clear
```

### Backup

```bash
# Backup do MySQL
docker exec $(docker ps -q -f name=krayin_mysql) \
    mysqldump -u root -p krayin > backup_$(date +%Y%m%d).sql

# Backup do Redis
docker exec $(docker ps -q -f name=krayin_redis) \
    redis-cli BGSAVE
```

---

## Checklist de Deploy

### Antes do Deploy

- [ ] Cluster Swarm inicializado
- [ ] Rede `traefik-public` criada
- [ ] Diretórios de dados criados em todos os nodes
- [ ] Secrets criados (app_key, db_password, db_root_password)
- [ ] Imagem buildada e no registry
- [ ] DNS configurado para o domínio

### Durante o Deploy

- [ ] `docker stack deploy` executado
- [ ] Todos os serviços em estado "Running"
- [ ] Healthchecks passando
- [ ] Migrations executadas

### Após o Deploy

- [ ] Acessar URL e verificar login
- [ ] Testar funcionalidades críticas
- [ ] Verificar logs por erros
- [ ] Configurar monitoramento
- [ ] Documentar versão deployada

---

## Contato

Para problemas ou dúvidas:
- GitHub: https://github.com/vitorbb1989/Krayingproject
- Branch: `theme-refactor-clean`
