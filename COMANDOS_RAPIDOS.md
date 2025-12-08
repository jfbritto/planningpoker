# 🚀 Comandos Rápidos - Subir a Aplicação

## ⚡ Início Rápido (Copie e Cole)

```bash
# 1. Criar arquivo .env (se não existir)
cp .env.example .env

# 2. Subir os containers Docker
docker-compose up -d

# 3. Instalar dependências do Composer
docker-compose exec app composer install

# 4. Gerar chave da aplicação
docker-compose exec app php artisan key:generate

# 5. Executar migrations (criar tabelas)
docker-compose exec app php artisan migrate

# 6. Acessar no navegador
# http://localhost:8080
```

---

## 📋 Passo a Passo Detalhado

### Passo 1: Verificar/Criar arquivo .env

```bash
# Verificar se existe
ls -la .env

# Se não existir, criar a partir do exemplo
cp .env.example .env
```

### Passo 2: Subir os Containers

```bash
# Subir em background (-d = detached)
docker-compose up -d

# Verificar se estão rodando
docker-compose ps

# Ver logs (opcional)
docker-compose logs -f
```

**Containers que devem estar rodando:**
- `planning_poker_app` (Laravel)
- `planning_poker_webserver` (Nginx)
- `planning_poker_db` (MySQL)

### Passo 3: Instalar Dependências

```bash
# Instalar pacotes do Composer
docker-compose exec app composer install

# Se der erro de permissão, ajuste:
docker-compose exec app chmod -R 755 storage bootstrap/cache
```

### Passo 4: Configurar Aplicação

```bash
# Gerar chave de criptografia
docker-compose exec app php artisan key:generate

# Limpar cache (se necessário)
docker-compose exec app php artisan config:clear
```

### Passo 5: Configurar Banco de Dados

```bash
# Executar migrations (criar tabelas)
docker-compose exec app php artisan migrate

# Se quiser ver o status das migrations
docker-compose exec app php artisan migrate:status
```

### Passo 6: Acessar a Aplicação

Abra no navegador:
```
http://localhost:8080
```

---

## 🔍 Comandos Úteis

### Verificar Status

```bash
# Ver containers rodando
docker-compose ps

# Ver logs da aplicação
docker-compose logs -f app

# Ver logs do banco
docker-compose logs -f db

# Ver logs do webserver
docker-compose logs -f webserver
```

### Parar/Iniciar Containers

```bash
# Parar containers
docker-compose down

# Parar e remover volumes (CUIDADO: apaga dados)
docker-compose down -v

# Reiniciar containers
docker-compose restart

# Parar e subir novamente
docker-compose down && docker-compose up -d
```

### Comandos Artisan (Laravel)

```bash
# Limpar todos os caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Ver rotas
docker-compose exec app php artisan route:list

# Executar testes
docker-compose exec app php artisan test
```

### Acessar Container

```bash
# Entrar no container da aplicação
docker-compose exec app bash

# Entrar no container do banco
docker-compose exec db bash

# Acessar MySQL
docker-compose exec db mysql -u planning_poker -proot planning_poker
```

---

## 🐛 Solução de Problemas

### Erro: Porta já em uso

```bash
# Verificar o que está usando a porta 8080
lsof -i :8080

# Ou mudar a porta no docker-compose.yml
# Altere "8080:80" para outra porta, ex: "8081:80"
```

### Erro: Container não inicia

```bash
# Ver logs detalhados
docker-compose logs

# Reconstruir containers
docker-compose up -d --build
```

### Erro: Permissão negada

```bash
# Ajustar permissões
docker-compose exec app chmod -R 755 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Erro: Banco de dados não conecta

```bash
# Verificar se o container do banco está rodando
docker-compose ps db

# Ver logs do banco
docker-compose logs db

# Testar conexão
docker-compose exec app php artisan tinker
# Depois no tinker: DB::connection()->getPdo();
```

### Limpar tudo e começar do zero

```bash
# Parar e remover tudo
docker-compose down -v

# Remover imagens (opcional)
docker-compose rm -f

# Subir novamente
docker-compose up -d --build
```

---

## ✅ Checklist de Verificação

Após executar os comandos, verifique:

- [ ] Containers estão rodando: `docker-compose ps`
- [ ] Arquivo `.env` existe e tem `APP_KEY` preenchido
- [ ] Migrations foram executadas: `docker-compose exec app php artisan migrate:status`
- [ ] Site abre em `http://localhost:8080`
- [ ] Consegue criar uma sala
- [ ] Consegue entrar na sala
- [ ] Consegue adicionar uma história
- [ ] Consegue votar

---

## 🎯 Comando Único (Tudo de uma vez)

Se quiser executar tudo em sequência:

```bash
cp .env.example .env && \
docker-compose up -d && \
sleep 10 && \
docker-compose exec app composer install && \
docker-compose exec app php artisan key:generate && \
docker-compose exec app php artisan migrate && \
echo "✅ Aplicação pronta! Acesse: http://localhost:8080"
```

---

## 📝 Notas Importantes

1. **Primeira vez:** Pode demorar alguns minutos para baixar as imagens Docker
2. **Banco de dados:** Os dados persistem mesmo após `docker-compose down` (não use `-v`)
3. **Porta 8080:** Se estiver em uso, altere no `docker-compose.yml`
4. **.env:** Nunca commite o arquivo `.env` no Git (já está no .gitignore)

