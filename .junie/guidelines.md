# Guidelines de Desenvolvimento – semtran2 (Laravel 12 + Filament 3)

Este documento consolida práticas e instruções específicas deste projeto para desenvolvedores experientes trabalhando com Laravel + Filament. Foco: setup consistente, execução de testes reprodutível (inclui um teste exemplo comprovadamente funcional), e diretrizes práticas para evoluir o código mantendo a qualidade e a coesão arquitetural.

## 1. Build e Configuração

- Requisitos:
  - PHP 8.2+.
  - Extensões PHP típicas do stack Laravel (mbstring, openssl, pdo, pdo_mysql/sqlite, tokenizer, xml, ctype, json, fileinfo).
  - Node 18+ para assets (Vite) se precisar rodar front.
  - Composer 2.x.

- Instalação:
  - composer install
  - cp .env.example .env
  - php artisan key:generate
  - Configure DB no .env (MySQL ou SQLite). Para fluxo rápido local com SQLite: DB_CONNECTION=sqlite e comente demais variáveis de conexão; crie o arquivo: touch database/database.sqlite
  - php artisan migrate --seed
    - Importante: este projeto possui o seeder Database\Seeders\RbacSeeder que provisiona permissões (spatie/laravel-permission), papéis (admin/gestor) e um usuário admin padrão: admin@semtran.local / secret123. Ajuste conforme necessário.
  - php artisan storage:link (se houver upload/armazenamento local)
  - Opcional (desenvolvimento): composer run dev
    - Atalhos: sobe servidor, fila, logs e Vite de forma concorrente.

- Notas específicas:
  - Filament 3.x está instalado. Após atualizações de dependências, o script post-autoload-dump executa filament:upgrade automaticamente (composer.json). Verifique logs após composer update.
  - Para jobs/filas durante desenvolvimento, queue:listen já está previsto no script dev. Em produção, utilize um worker dedicado (ex.: Supervisor) e mude QUEUE_CONNECTION conforme a infraestrutura.

## 2. Testes

### 2.1. Configuração de Testes

- phpunit.xml já força ambiente de testes isolado usando SQLite in-memory:
  - DB_CONNECTION=sqlite
  - DB_DATABASE=:memory:
  - CACHE/SESSION/Mail/Queue em drivers de teste (array/sync) para determinismo.
- Em testes de Feature que interagem com o DB, utilize RefreshDatabase para migrações por teste e isolamento.

### 2.2. Como Executar

- Suite completa: ./vendor/bin/phpunit
- Suites específicas: ./vendor/bin/phpunit --testsuite Feature
- Teste único por filtro: ./vendor/bin/phpunit --filter UserFactoryTest
- Via artisan (respeitando phpunit.xml): php artisan test

### 2.3. Adicionando Novos Testes

- Padrões do projeto:
  - Feature Tests: tests/Feature, herdam de Tests\TestCase e, se necessário, usam Illuminate\Foundation\Testing\RefreshDatabase.
  - Unit Tests: tests/Unit, herdam de PHPUnit\Framework\TestCase (sem bootstrapping de framework) quando não precisam de container/DB.
  - Factories: prefira model factories para setup de dados (User::factory(), etc.).
  - Permissões/Filament: ao testar páginas/ações Filament, crie um usuário com o(s) papel(is)/permissões necessários e use actingAs($user) antes de acessar rotas Filament.

- Exemplos de snippets úteis:
  - Isolamento de DB por teste:
    - use RefreshDatabase;
  - Usuário com papel admin (provisionado pelo seeder):
    - $admin = User::factory()->create([...]); // ou use o usuário seeded quando executar contra um DB com seeders rodados
  - Testando página Filament protegida:
    - $this->actingAs($admin)->get('/admin')->assertStatus(200); // ajuste rota conforme necessidade

### 2.4. Exemplo de Teste (Comprovado)

Foi criado um teste simples que valida o pipeline de testes com DB em memória e factories. Rodei localmente e passou.

Arquivo: tests/Feature/UserFactoryTest.php

Conteúdo:

```
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_user_with_factory(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }
}
```

Execução (exemplos):
- ./vendor/bin/phpunit --filter UserFactoryTest
- php artisan test --filter UserFactoryTest

Resultado esperado: OK (1 test, 1 assertion)

## 3. Diretrizes de Desenvolvimento (Laravel + Filament)

- Organização de Recursos Filament:
  - Siga o padrão Resource/Pages/RelationManagers do Filament. Centralize regras de visibilidade/habilitação em métodos estáticos/coesos (como canSee/canFill) para que formulários e tabelas mantenham a mesma regra sem duplicações.
  - Use Section/Grid para compor formulários com legibilidade. Prefira callbacks tipados (Get $get) e visibilidade habilitada por policies/permissions (Spatie Permission) para consistência RBAC.

- Autorização e RBAC:
  - Utilize spatie/laravel-permission para vincular roles/permissoes; padronize os nomes como já existente no projeto (ex.: report.view, report.create, report.section.equipe.fill, etc.).
  - Policies devem delegar para permissões quando fizer sentido; mantenha regras de seção mais granulares (ex.: section.informacoes_gerais.view/fill) para bloquear/permitir campos em nível de UI.

- Migrations e Seeds:
  - Mantenha migrações idempotentes e seeds reexecutáveis (firstOrCreate/sync). O seeder RbacSeeder já segue este padrão.
  - Para desenvolvimento local repetitivo: php artisan migrate:fresh --seed

- Fábricas e Testabilidade:
  - Sempre que adicionar colunas obrigatórias, atualize as factories correspondentes. Garanta defaults sensatos para reduzir o custo de setup de testes.

- Estilo de Código e Qualidade:
  - Use Laravel Pint: ./vendor/bin/pint
  - Siga PSR-12 e convenções do Laravel (nomes de classes, pastas e namespaces). Evite helpers globais dentro de domínio; prefira serviços injetáveis quando a lógica crescer.
  - Configure DTOs/Actions quando fluxos de formulário crescerem, para manter Resources enxutos.

- Observabilidade/Depuração:
  - APP_DEBUG=true em desenvolvimento. Em testes (phpunit.xml) recursos pesados como Telescope/Pulse estão desativados.
  - Logs: utilize laravel/pail (composer run dev inclui pail). Para issues de policies/permissions, verifique o gate e os caches de permissões (php artisan permission:cache-reset) quando aplicável.

- Assets e Front:
  - Vite em dev: npm run dev; produção: npm run build. Filament injeta assets automaticamente; verifique conflitos ao personalizar tema.

- Filas e Jobs:
  - Em dev/tests: QUEUE_CONNECTION=sync para determinismo. Em produção, use driver robusto (redis, sqs), idempotência e backoff apropriados.

## 4. Notas Específicas do Projeto

- Filament 3 + Laravel 12: após atualizar dependências, execute migrations/upgrade do Filament caso haja alterações em pacotes oficiais.
- Recursos relevantes já existentes (exemplos): ReportResource, DepartmentResource, UserResource; policies associadas; seed de RBAC para permissões e papéis.
- Convenção de permissões já utilizada no projeto: entidade.ação (ex.: user.update) e seções: report.section.<secao>.(view|fill). Mantenha essas convenções ao criar módulos novos.

## 5. Fluxo de Setup Rápido (TL;DR)

1) composer install
2) cp .env.example .env && php artisan key:generate
3) Configurar DB (SQLite: touch database/database.sqlite; setar DB_CONNECTION=sqlite)
4) php artisan migrate --seed
5) Opcional: composer run dev
6) Testes: ./vendor/bin/phpunit

Se precisar logar no Filament (admin): admin@semtran.local / secret123 (gerado pelo RbacSeeder).

---

Este guia é vivo: ao introduzir novos módulos (Resources, Policies, Seeds, migrações), atualize as seções correspondentes com decisões arquiteturais e comandos de operação/validação.
