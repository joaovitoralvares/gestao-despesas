# Gestão de Despesas

Api para aplicação de gerenciamento de despesas, com as seguintes funcionalidades.
- Registro de usuários
- Login
- Autenticação via cookies
- Cadastro de despesas
- Listagem e visualização de despesas
- Exclusão de despesas
- Notificação por email ao cadastrar despesa

## Usuários

As features referentes ao Domínio de Usuários foram implementadas utilizando o [Laravel Fortify](https://laravel.com/docs/10.x/fortify), para que inicialmente
pudesse focar em implementar as features do Domínio de Despesas, o qual é o foco principal da aplicação.

## Sistema de autenticação

A autenticação foi implementada utilizado o [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum), visando que a api será utilizada por uma SPA. Todas as rotas relacionadas as Despesas estão protegidas por autenticação. Além disso, todas as rotas possuem [CSRF Protection](https://laravel.com/docs/10.x/csrf#main-content).

## Despesas

Todas as features relacionadas ao domínio de despesas estão cobertas por testes.
Ao cadastrar uma despesa, o usuário recebe uma notificação por email informando que a despesa foi cadastrada.
O envio da notificação é feito de forma assíncrona, sendo enviada para uma fila no redis e sendo consumida posteriormente por um worker, o qual realizará o envio do email. 

## Requisitos

É necessário que tenha o [git](https://git-scm.com/) e o [Docker](https://www.docker.com/) instalados em sua máquina.

## Setup e execução

Primeiramente, clone o projeto com o seguinte comando:
```console
git clone https://github.com/joaovitoralvares/gestao-despesas.git
```
Mude para o diretório do projeto:
```console
cd gestao-despesas
```
Crie o arquivo .env:
```console
cp .env.example .env
```

Instale as dependências:
```console
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

Inicie a aplicação:
```console
./vendor/bin/sail up -d

```

Execute o comando:
```console
./vendor/bin/sail artisan key:generate
```

Execute as migrations:
```console
./vendor/bin/sail artisan migrate
```

Após início da aplicação, ela estará disponível em http://localhost/

Para processar as notificações de email que forem adicionadas a fila, execute o seguinte comando:
```console
./vendor/bin/sail artisan queue:work redis
```
Para testar o envio dos emails em ambiente de desenvolvimento, foi utilizado o [Malpit](https://github.com/axllent/mailpit).
Os emails enviados estarão acessíveis através da url http://localhost:8025/

## Documentação

Atualmente, existe esta collection no postman com exemplos referentes aos endpoints de usuários e despesas.
https://www.postman.com/crimson-robot-271477/workspace/gestao-despesas/collection/15814016-30e6a835-ae99-458a-a579-a3d415c94d24

### TO DO:
- Adicionar documentação da api utilizando swagger ui
