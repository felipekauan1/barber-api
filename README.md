# Barber API 💈

Esta é uma API REST desenvolvida em **Laravel 8** para o gerenciamento completo de serviços de barbearia. O projeto engloba desde a autenticação segura de clientes até a gestão de agendamentos, favoritos e busca geolocalizada de profissionais.

A aplicação foi estruturada focando em escalabilidade e experiência do usuário, utilizando o padrão **MVC**, segurança via **JWT** e processamento de imagens para perfis.

## 🛠️ Tecnologias Utilizadas

  * **PHP 7.4+**
  * **Laravel 8.2**
  * **MySQL** (Eloquent ORM)
  * **JWT Auth** (Autenticação Stateless)
  * **Intervention Image** (Manipulação de Avatares)
  * **PSR-4** (Padrão de Autoload)
  * **Tradução de Validação** (Suporte total a `pt-BR`)

## 📋 Funcionalidades Principais

  * **Auth System:** Fluxo completo de autenticação (Login, Register, Logout, Refresh) utilizando JWT.
  * **Geo & Search:** Listagem de barbeiros com suporte a busca por nome e localização.
  * **Agendamentos:** Sistema de reserva de horários com validação de disponibilidade por barbeiro.
  * **User Experience:** Sistema de "Favoritos" para barbeiros e histórico de agendamentos do usuário.
  * **Profile Management:** Atualização de dados cadastrais e upload de foto de perfil com redimensionamento automático.

## 🛣️ Endpoints da API

### Autenticação & Usuário

  - `POST /api/user` - Cadastro de novo cliente.
  - `POST /api/auth/login` - Login e geração de Token JWT.
  - `GET /api/user` - Recupera dados do perfil logado.
  - `POST /api/user/avatar` - Upload de foto de perfil.
  - `GET /api/user/appointments` - Lista de agendamentos do cliente.

### Barbeiros & Serviços

  - `GET /api/barbers` - Lista todos os barbeiros disponíveis.
  - `GET /api/barber/{id}` - Informações detalhadas, serviços e horários de um profissional.
  - `POST /api/barber/{id}/appointment` - Reserva de um serviço específico.
  - `GET /api/search?q={nome}` - Busca rápida de profissionais.

## 🚀 Como Instalar e Rodar

### Pré-requisitos

  * PHP 7.4 ou superior
  * Composer
  * Servidor MySQL

### Passo a passo

1.  Faça o clone do repositório:

    ```bash
    git clone https://github.com/felipekauan1/barber-api.git
    ```

2.  Instale as dependências:

    ```bash
    composer install
    ```

3.  Configure o arquivo de ambiente:

      * Renomeie o arquivo `.env.example` para `.env`.
      * Configure as credenciais do seu banco de dados MySQL.
      * Gere a chave da aplicação: `php artisan key:generate`.
      * Gere o segredo do JWT: `php artisan jwt:secret`.

4.  Execute as migrations para criar as tabelas:

    ```bash
    php artisan migrate
    ```

5.  Inicie o servidor local:

    ```bash
    php artisan serve
    ```
