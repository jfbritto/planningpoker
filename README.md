# Planning Poker

Sistema simples e objetivo para estimativas ágeis usando Planning Poker.

## Tecnologias

- Laravel 10
- MySQL 8.0
- Docker & Docker Compose
- Nginx

## Requisitos

- Docker
- Docker Compose

## Instalação Local (Docker)

1. Clone o repositório
2. Copie o arquivo `.env.example` para `.env`
3. Execute `docker-compose up -d`
4. Execute `docker-compose exec app composer install`
5. Execute `docker-compose exec app php artisan key:generate`
6. Execute `docker-compose exec app php artisan migrate`
7. Acesse `http://localhost:8080`

## Deploy no HostGator

Consulte o arquivo `DEPLOY.md` para instruções detalhadas de deploy.

## Funcionalidades

- Criar salas de Planning Poker
- Participantes podem entrar nas salas
- Adicionar histórias/tarefas para estimar
- Votar usando cartões de Planning Poker (0, 1, 2, 3, 5, 8, 13, 21, 34, 55, 89, ?)
- Revelar votos e ver resultados
- Modo observador (não vota)

## Estrutura do Banco de Dados

- **rooms**: Salas de Planning Poker
- **stories**: Histórias/Tarefas para estimar
- **participants**: Participantes das salas
- **votes**: Votos dos participantes

# planningpoker

