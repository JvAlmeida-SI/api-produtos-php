# 🚀 API de Gerenciamento de Produtos com Autenticação JWT

## 🔧 Tecnologias Utilizadas

### Backend
- **Laravel 10** (PHP 8.1+)
- **MySQL 8** (Database relacional)
- **JWT Auth** (Autenticação via tokens)
- **Eloquent ORM** (Mapeamento objeto-relacional)

### Testes
- **PHPUnit** (Testes unitários e de feature)
- **Mockery** (Para mocks em testes unitários)

## 🏛 **Arquitetura**
### Camadas Principais
- **Controllers**: Manipulação de requisições HTTP
- **Services**: Lógica de negócio
- **Repositories**: Acesso a dados
- **Models**: Entidades do sistema

### Padrões Arquiteturais
- Clean Architecture
- Injeção de Dependência
- Separação de Responsabilidades

### Fluxo de Requisição
Rota → Middleware → Controller → Service → Repository → Model

## 🌐 Endpoints Principais
### Autenticação
- POST /api/auth/register - Registrar novo usuário
- POST /api/auth/login - Login (obter token JWT)
- POST /api/auth/logout - Invalidar token

### Produtos
- GET /api/products - Listar produtos (com filtros)
- POST /api/products - Criar produto (autenticado)
- PUT /api/products/{id} - Atualizar produto (autenticado)

## 🧩 Padrões de Projeto
### Principais Implementações
- Repository Pattern (Separação da camada de dados)
- Service Layer (Centralização da lógica de negócio)
- Dependency Injection (Injeção de dependências)
- Factory Method (Para criação de objetos complexos)
- Strategy (Para diferentes algoritmos de filtragem)

## ✅ Testes Implementados
### Testes Unitários
- Tests/Unit/Services (Lógica de negócio)
- Tests/Unit/Repositories (Acesso a dados)

### Testes de Feature
- Tests/Feature/Auth (Autenticação JWT)
- Tests/Feature/Products (CRUD de produtos)
- Tests/Feature/Categories (CRUD de categorias)

## Cobertura de Testes
- Autenticação e autorização
- CRUD completo de produtos
- Filtros e buscas
- Validações de dados
- Relacionamentos entre entidades

## 🛠 Comandos Essenciais
### Configuração inicial
- composer install
- cp .env.example .env
- php artisan key:generate
- php artisan jwt:secret
- php artisan migrate --seed

### Execução
- php artisan serve

### Testes
- php artisan test