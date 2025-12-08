# 📋 Planning Poker - Passo a Passo Completo

Este documento contém todas as instruções para configurar, desenvolver e fazer deploy do sistema de Planning Poker.

## ✅ Checklist de Tarefas

### Fase 1: Configuração Inicial do Projeto
- [x] Criar estrutura base do projeto Laravel com Docker
- [x] Configurar docker-compose.yml com Laravel, MySQL e Nginx
- [x] Criar Dockerfile para Laravel
- [x] Configurar banco de dados e migrations
- [x] Criar modelos e controllers para Planning Poker
- [x] Criar rotas e views básicas
- [x] Implementar funcionalidades de Planning Poker (criar sala, votar, revelar)
- [x] Criar documentação MD com passo a passo completo
- [x] Criar arquivos de configuração (.env.example, README)
- [ ] Validar com lint, format, test e test:e2e

---

## 🚀 Instalação e Configuração Local (Docker)

### Pré-requisitos
- Docker instalado
- Docker Compose instalado
- Git (opcional)

### Passo 1: Preparar o Ambiente

1. **Navegue até o diretório do projeto:**
   ```bash
   cd /Users/joaofilipibritto/Projetos/planningpoker
   ```

2. **Copie o arquivo de ambiente:**
   ```bash
   cp .env.example .env
   ```

3. **Ajuste as configurações no arquivo `.env` se necessário:**
   - As configurações padrão já estão corretas para Docker
   - `DB_HOST=db` (nome do serviço no docker-compose)
   - `DB_DATABASE=planning_poker`
   - `DB_USERNAME=planning_poker`
   - `DB_PASSWORD=root`

### Passo 2: Iniciar os Containers Docker

1. **Suba os containers:**
   ```bash
   docker-compose up -d
   ```

2. **Verifique se os containers estão rodando:**
   ```bash
   docker-compose ps
   ```

   Você deve ver 3 containers:
   - `planning_poker_app` (Laravel)
   - `planning_poker_webserver` (Nginx)
   - `planning_poker_db` (MySQL)

### Passo 3: Instalar Dependências do Laravel

1. **Instale as dependências do Composer:**
   ```bash
   docker-compose exec app composer install
   ```

2. **Gere a chave da aplicação:**
   ```bash
   docker-compose exec app php artisan key:generate
   ```

### Passo 4: Configurar o Banco de Dados

1. **Execute as migrations:**
   ```bash
   docker-compose exec app php artisan migrate
   ```

   Isso criará as seguintes tabelas:
   - `sessions` - Sessões do Laravel
   - `cache` e `cache_locks` - Cache do Laravel
   - `jobs`, `job_batches` e `failed_jobs` - Sistema de filas
   - `rooms` - Salas de Planning Poker
   - `stories` - Histórias/Tarefas
   - `participants` - Participantes
   - `votes` - Votos

### Passo 5: Acessar a Aplicação

1. **Abra seu navegador e acesse:**
   ```
   http://localhost:8080
   ```

2. **Você deve ver a página inicial do Planning Poker**

---

## 🧪 Testando a Aplicação Localmente

### Teste Básico de Funcionalidade

1. **Criar uma Sala:**
   - Clique em "Criar Nova Sala"
   - Digite um nome (ex: "Sprint 2024 - Backend")
   - Clique em "Criar Sala"

2. **Entrar na Sala:**
   - Você será redirecionado para a sala
   - Digite seu nome
   - Clique em "Entrar"

3. **Adicionar uma História:**
   - Preencha o título (ex: "Implementar autenticação")
   - Adicione uma descrição (opcional)
   - Clique em "Adicionar História"

4. **Votar:**
   - Clique em um dos cartões de Planning Poker
   - Seu voto será registrado automaticamente

5. **Revelar Votos:**
   - Clique em "Revelar Votos"
   - Veja os resultados e a média dos votos

6. **Testar com Múltiplos Participantes:**
   - Abra a mesma URL em uma aba anônima
   - Entre na sala com outro nome
   - Vote e veja os resultados em tempo real

---

## 📦 Deploy no HostGator (Plano M)

### Pré-requisitos
- Conta HostGator com plano M
- Acesso SSH (habilitado no painel)
- Acesso ao cPanel
- Banco de dados MySQL criado no cPanel

### Passo 1: Preparar o Projeto para Produção

1. **Ajuste o arquivo `.env` para produção:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://seudominio.com.br
   
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=seu_banco_de_dados
   DB_USERNAME=seu_usuario_db
   DB_PASSWORD=sua_senha_db
   ```

2. **Otimize a aplicação:**
   ```bash
   docker-compose exec app php artisan config:cache
   docker-compose exec app php artisan route:cache
   docker-compose exec app php artisan view:cache
   ```

### Passo 2: Criar Banco de Dados no HostGator

1. **Acesse o cPanel da HostGator**

2. **Crie um banco de dados MySQL:**
   - Vá em "MySQL Databases"
   - Crie um novo banco (ex: `usuario_planningpoker`)
   - Anote o nome completo do banco (geralmente `usuario_planningpoker`)

3. **Crie um usuário MySQL:**
   - Crie um novo usuário
   - Defina uma senha forte
   - Anote o nome completo do usuário (geralmente `usuario_dbuser`)

4. **Associe o usuário ao banco:**
   - Adicione o usuário ao banco de dados
   - Conceda todas as permissões (ALL PRIVILEGES)

### Passo 3: Upload dos Arquivos

1. **Compacte o projeto (excluindo arquivos desnecessários):**
   ```bash
   # No diretório do projeto
   tar -czf planningpoker.tar.gz \
     --exclude='node_modules' \
     --exclude='.git' \
     --exclude='.env' \
     --exclude='storage/logs/*' \
     --exclude='storage/framework/cache/*' \
     --exclude='storage/framework/sessions/*' \
     --exclude='storage/framework/views/*' \
     .
   ```

2. **Faça upload via cPanel File Manager ou FTP:**
   - Acesse o File Manager no cPanel
   - Navegue até `public_html` (ou subdomínio/diretório específico)
   - Faça upload do arquivo compactado
   - Extraia o arquivo

   **OU**

   - Use um cliente FTP (FileZilla, WinSCP, etc.)
   - Conecte-se ao servidor
   - Faça upload de todos os arquivos para o diretório correto

### Passo 4: Configurar Permissões

1. **Via SSH ou File Manager, defina as permissões:**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chmod -R 755 public
   ```

2. **Crie os diretórios necessários se não existirem:**
   ```bash
   mkdir -p storage/framework/cache
   mkdir -p storage/framework/sessions
   mkdir -p storage/framework/views
   mkdir -p storage/logs
   ```

### Passo 5: Configurar o Ambiente de Produção

1. **Crie o arquivo `.env` no servidor:**
   - Copie o `.env.example` para `.env`
   - Edite com as credenciais do HostGator

2. **Via SSH, execute:**
   ```bash
   cd /home/usuario/public_html/planningpoker  # Ajuste o caminho
   php artisan key:generate
   php artisan config:cache
   php artisan migrate --force
   ```

### Passo 6: Configurar o Document Root

1. **No cPanel, configure o Document Root:**
   - Se o projeto está em `public_html/planningpoker`
   - O Document Root deve apontar para `public_html/planningpoker/public`

2. **Ou configure via `.htaccess` na raiz:**
   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteRule ^(.*)$ public/$1 [L]
   </IfModule>
   ```

### Passo 7: Verificar Requisitos do Laravel

1. **Verifique se o servidor atende aos requisitos:**
   - PHP >= 8.2
   - Extensões: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath
   - Mod_rewrite habilitado

2. **Se necessário, solicite ao suporte da HostGator para habilitar extensões**

### Passo 8: Testar a Aplicação

1. **Acesse o domínio/subdomínio no navegador**
2. **Teste todas as funcionalidades:**
   - Criar sala
   - Entrar na sala
   - Adicionar história
   - Votar
   - Revelar votos

---

## 🔧 Comandos Úteis

### Desenvolvimento Local

```bash
# Iniciar containers
docker-compose up -d

# Parar containers
docker-compose down

# Ver logs
docker-compose logs -f app

# Executar comandos Artisan
docker-compose exec app php artisan [comando]

# Acessar shell do container
docker-compose exec app bash

# Reinstalar dependências
docker-compose exec app composer install

# Limpar cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

### Produção (HostGator)

```bash
# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recriar cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Executar migrations
php artisan migrate --force

# Ver logs
tail -f storage/logs/laravel.log
```

---

## 📁 Estrutura do Projeto

```
planningpoker/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Controller.php
│   │       ├── RoomController.php
│   │       ├── StoryController.php
│   │       └── VoteController.php
│   └── Models/
│       ├── Participant.php
│       ├── Room.php
│       ├── Story.php
│       └── Vote.php
├── database/
│   └── migrations/
│       ├── 2024_01_01_000001_create_rooms_table.php
│       ├── 2024_01_01_000002_create_stories_table.php
│       ├── 2024_01_01_000003_create_participants_table.php
│       └── 2024_01_01_000004_create_votes_table.php
├── docker/
│   ├── mysql/
│   │   └── my.cnf
│   ├── nginx/
│   │   └── default.conf
│   └── php/
│       └── local.ini
├── public/
│   └── index.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       ├── rooms/
│       │   ├── create.blade.php
│       │   └── show.blade.php
│       └── welcome.blade.php
├── routes/
│   └── web.php
├── .dockerignore
├── .gitignore
├── composer.json
├── docker-compose.yml
├── Dockerfile
├── PASSO_A_PASSO.md
└── README.md
```

---

## 🐛 Solução de Problemas

### Problema: Containers não iniciam
**Solução:** Verifique se as portas 8080 e 3306 estão livres:
```bash
lsof -i :8080
lsof -i :3306
```

### Problema: Erro de conexão com banco
**Solução:** Verifique se o container do MySQL está rodando:
```bash
docker-compose ps
docker-compose logs db
```

### Problema: Erro 500 no HostGator
**Solução:** 
1. Verifique os logs: `storage/logs/laravel.log`
2. Verifique permissões: `chmod -R 755 storage bootstrap/cache`
3. Verifique o `.env` está configurado corretamente

### Problema: Página em branco
**Solução:**
1. Limpe o cache: `php artisan cache:clear`
2. Verifique se `APP_DEBUG=true` no `.env` para ver erros
3. Verifique permissões dos diretórios

---

## 📝 Notas Importantes

1. **Segurança:**
   - Sempre use `APP_DEBUG=false` em produção
   - Use senhas fortes para o banco de dados
   - Mantenha o Laravel atualizado

2. **Performance:**
   - Use cache em produção (`config:cache`, `route:cache`, `view:cache`)
   - Considere usar Redis para cache em produção (se disponível)

3. **Backup:**
   - Faça backup regular do banco de dados
   - Mantenha backup dos arquivos importantes

---

## ✅ Conclusão

Após seguir todos os passos, você terá:
- ✅ Sistema de Planning Poker funcionando localmente com Docker
- ✅ Sistema pronto para deploy no HostGator
- ✅ Documentação completa para referência futura

**Próximos passos opcionais:**
- Adicionar autenticação de usuários
- Implementar WebSockets para atualização em tempo real
- Adicionar histórico de estimativas
- Exportar resultados para PDF/Excel

