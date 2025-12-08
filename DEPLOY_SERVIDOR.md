# 🚀 Comandos para Deploy no Servidor

## 📋 Checklist Pré-Deploy

- [ ] Arquivos enviados para o servidor
- [ ] Acesso SSH configurado
- [ ] Banco de dados MySQL criado no cPanel
- [ ] Credenciais do banco de dados anotadas

---

## 🔧 Comandos Essenciais (Execute na Ordem)

### 1. Navegar até o diretório do projeto

```bash
cd /home/seu_usuario/public_html/planningpoker
# OU se estiver em subdomínio:
cd /home/seu_usuario/public_html/subdominio
# OU se estiver em subdiretório:
cd /home/seu_usuario/public_html/pasta/planningpoker
```

### 2. Criar arquivo .env (se não existir)

```bash
# Copiar do exemplo
cp .env.example .env

# Editar o arquivo .env com as credenciais do servidor
nano .env
# OU
vi .env
```

**Configurações importantes no .env:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com.br

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nome_completo_do_banco
DB_USERNAME=nome_completo_do_usuario
DB_PASSWORD=senha_do_banco

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### 3. Instalar dependências do Composer

```bash
# Instalar apenas dependências de produção (sem dev)
composer install --no-dev --optimize-autoloader

# Se der erro de memória, aumentar limite:
php -d memory_limit=512M /usr/bin/composer install --no-dev --optimize-autoloader
```

### 4. Gerar chave da aplicação

```bash
php artisan key:generate
```

### 5. Configurar permissões

```bash
# Criar diretórios se não existirem
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Definir permissões
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public

# Se necessário, ajustar proprietário (substitua 'usuario' pelo seu usuário)
chown -R usuario:usuario storage bootstrap/cache
```

### 6. Executar migrations (criar tabelas)

```bash
php artisan migrate --force
```

### 7. Limpar e otimizar cache

```bash
# Limpar todos os caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Criar cache para produção (melhora performance)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Verificar se está funcionando

```bash
# Verificar versão do Laravel
php artisan --version

# Verificar rotas
php artisan route:list

# Ver logs (se houver erro)
tail -f storage/logs/laravel.log
```

---

## 🔍 Verificações Importantes

### Verificar versão do PHP

```bash
php -v
# Deve ser PHP 7.3 ou 8.0 (conforme composer.json)
```

### Verificar extensões PHP necessárias

```bash
php -m | grep -E "pdo|mbstring|openssl|tokenizer|xml|ctype|json|bcmath"
```

**Extensões necessárias:**
- pdo_mysql
- mbstring
- openssl
- tokenizer
- xml
- ctype
- json
- bcmath

### Verificar se o Document Root está correto

O Document Root deve apontar para a pasta `public` do projeto:
```
/home/usuario/public_html/planningpoker/public
```

---

## 🛠️ Comandos de Manutenção

### Limpar cache completo

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Recriar cache de produção

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Ver logs de erro

```bash
tail -f storage/logs/laravel.log
```

### Limpar banco de dados (CUIDADO!)

```bash
php artisan migrate:fresh
# Isso apaga TODAS as tabelas e recria
```

---

## ⚠️ Problemas Comuns

### Erro: "Class not found"
```bash
composer dump-autoload
php artisan config:clear
```

### Erro: "Permission denied" em storage
```bash
chmod -R 755 storage bootstrap/cache
```

### Erro: "No application encryption key"
```bash
php artisan key:generate
```

### Erro de conexão com banco
- Verifique as credenciais no `.env`
- Verifique se o banco foi criado no cPanel
- Verifique se o usuário tem permissões no banco

---

## 📝 Resumo Rápido (Copie e Cole)

```bash
# 1. Navegar até o projeto
cd /home/seu_usuario/public_html/planningpoker

# 2. Criar .env
cp .env.example .env
nano .env  # Editar com suas credenciais

# 3. Instalar dependências
composer install --no-dev --optimize-autoloader

# 4. Gerar chave
php artisan key:generate

# 5. Permissões
chmod -R 755 storage bootstrap/cache

# 6. Migrations
php artisan migrate --force

# 7. Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## ✅ Após Executar os Comandos

1. Acesse o domínio no navegador
2. Teste criar uma sala
3. Verifique se não há erros
4. Se houver erros, verifique os logs: `tail -f storage/logs/laravel.log`
