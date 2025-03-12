<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Logo do Laravel"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Status da Build"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total de Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Última Versão Estável"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="Licença"></a>
</p>

# SKY ARENA - Sistema de Ranking de Beach Tennis

## Sobre o Projeto

O **SKY ARENA** é uma plataforma completa desenvolvida em Laravel para gerenciamento de torneios e ranking de Beach Tennis. O sistema permite a organização de competições, acompanhamento de jogadores e manutenção de estatísticas em tempo real.

## Funcionalidades Principais

- **Gerenciamento de Torneios**
  - Criação de diversos formatos de torneio (Super 8 Individual, Super 12 Duplas)
  - Geração automática de partidas e rodadas
  - Configuração flexível de regras e pontuações

- **Cadastro de Jogadores**
  - Perfis completos com histórico de partidas
  - Sistema de busca avançada
  - Acompanhamento de estatísticas individuais

- **Sistema de Ranking**
  - Ranking geral baseado em pontos acumulados
  - Estatísticas detalhadas (vitórias, derrotas, parceiros)
  - Visualização pública do ranking

- **Gerenciamento de Partidas**
  - Registros de resultados
  - Histórico completo
  - Impacto automatizado no ranking

## Tecnologias Utilizadas

- **Backend:** Laravel 10.x
- **Frontend:** Tailwind CSS, Alpine.js
- **Autenticação:** Laravel Breeze
- **Banco de Dados:** MySQL

## Requisitos de Sistema

- PHP >= 8.1
- Composer
- Node.js e NPM
- MySQL

## Instalação

1. Clone o repositório
   ```bash
   git clone https://seurepositorio/skyarena.git
   cd skyarena
   ```

2. Instale as dependências
   ```bash
   composer install
   npm install
   ```

3. Configure o ambiente
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure seu banco de dados no arquivo `.env`

5. Execute as migrações
   ```bash
   php artisan migrate --seed
   ```

6. Compile os assets
   ```bash
   npm run build
   ```

7. Inicie o servidor
   ```bash
   php artisan serve
   ```

## Acesso ao Sistema

- **Área Administrativa:** /login
- **Visualização do Ranking:** /
- **Estatísticas de Jogadores:** /player-stats

## Créditos

Desenvolvido com Laravel, o framework PHP com sintaxe elegante e expressiva. Mais informações sobre o Laravel podem ser encontradas na [documentação oficial](https://laravel.com/docs).

## Licença

O sistema SKY ARENA é um software proprietário.
O Laravel framework é um software de código aberto licenciado sob a [licença MIT](https://opensource.org/licenses/MIT).

## Sobre o Laravel

Laravel é um framework de aplicações web com sintaxe expressiva e elegante. Acreditamos que o desenvolvimento deve ser uma experiência agradável e criativa para ser verdadeiramente gratificante. O Laravel elimina as dificuldades do desenvolvimento facilitando tarefas comuns utilizadas em muitos projetos web, como:

- [Motor de rotas simples e rápido](https://laravel.com/docs/routing).
- [Poderoso container de injeção de dependências](https://laravel.com/docs/container).
- Múltiplos back-ends para armazenamento de [sessão](https://laravel.com/docs/session) e [cache](https://laravel.com/docs/cache).
- [ORM de banco de dados](https://laravel.com/docs/eloquent) expressivo e intuitivo.
- [Migrações de schema](https://laravel.com/docs/migrations) independentes de banco de dados.
- [Processamento robusto de jobs em background](https://laravel.com/docs/queues).
- [Transmissão de eventos em tempo real](https://laravel.com/docs/broadcasting).

O Laravel é acessível, poderoso e fornece as ferramentas necessárias para aplicações grandes e robustas.

## Aprendendo Laravel

O Laravel possui a mais extensa e completa [documentação](https://laravel.com/docs) e biblioteca de tutoriais em vídeo entre todos os frameworks de aplicações web modernos, tornando fácil começar a usar o framework.

Você também pode experimentar o [Laravel Bootcamp](https://bootcamp.laravel.com), onde será guiado na construção de uma aplicação Laravel moderna do zero.

Se você não gosta de ler, o [Laracasts](https://laracasts.com) pode ajudar. O Laracasts contém milhares de tutoriais em vídeo sobre uma variedade de tópicos, incluindo Laravel, PHP moderno, testes unitários e JavaScript. Melhore suas habilidades explorando nossa abrangente biblioteca de vídeos.

## Patrocinadores do Laravel

Gostaríamos de estender nossos agradecimentos aos seguintes patrocinadores por financiarem o desenvolvimento do Laravel. Se você está interessado em se tornar um patrocinador, visite o [programa de parceiros do Laravel](https://partners.laravel.com).

### Parceiros Premium

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contribuindo

Obrigado por considerar contribuir para o framework Laravel! O guia de contribuição pode ser encontrado na [documentação do Laravel](https://laravel.com/docs/contributions).

## Código de Conduta

Para garantir que a comunidade Laravel seja acolhedora para todos, por favor, revise e respeite o [Código de Conduta](https://laravel.com/docs/contributions#code-of-conduct).

## Vulnerabilidades de Segurança

Se você descobrir uma vulnerabilidade de segurança no Laravel, envie um e-mail para Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). Todas as vulnerabilidades de segurança serão prontamente tratadas.
