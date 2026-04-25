Guia de deploy para Hostinger (FTP - public_html)

Resumo rápido
- Hostinger só aceita upload via FTP para `public_html`.
- Como você não tem Composer no servidor, gere `vendor/` localmente e envie via FTP.
- Esta pasta `deploy/` contém um `index.php` e `.htaccess` prontos para subir em `public_html`.

Passo a passo

1) No seu PC (pasta do projeto)
- Abra terminal na raiz do projeto e rode:

```bash
composer install --no-dev --optimize-autoloader --no-interaction
```

Isto cria a pasta `vendor/` necessária.

2) Arquivos a enviar por FTP (ordem recomendada)
- Envie toda a árvore do projeto para o servidor dentro de `public_html` (se você não tem acesso acima do webroot):
  - `app/`, `bootstrap/`, `config/`, `database/`, `resources/`, `routes/`, `storage/`, `vendor/`, `artisan`, `composer.json`, etc.
  - Substitua/mande o arquivo `public_html/index.php` pelo `deploy/public_html_index.php` (renomeie para `index.php` no servidor).
  - Suba `deploy/public_html.htaccess` como `.htaccess` dentro de `public_html`.
  - Suba `public/health.html` (já criado) para `public_html/health.html`.

Observação: Não é ideal colocar todo projeto dentro de `public_html`, mas é a opção quando não há acesso acima do webroot.

3) Ajustar `index.php`
- Use o arquivo `deploy/public_html_index.php` que já vem com os caminhos ajustados (require em `vendor` e `bootstrap` no mesmo nível de `index.php`).

4) Subir `.env`
- Preferível: coloque `.env` fora do `public_html` (se tiver acesso ao diretório acima). Caso contrário, coloque em `public_html/.env` e proteja-o (veja `.htaccess`).
- No `.env` do servidor, configure `APP_ENV=production` e `APP_DEBUG=false`.

5) Permissões (via File Manager do Hostinger)
- Pastas: `755`
- Arquivos: `644`
- `storage` e `bootstrap/cache`: `775` (ou `755` se 775 não estiver disponível)

6) Limpar caches (se sem SSH)
- Sem SSH: delete manualmente arquivos em `bootstrap/cache/*.php` gerados e limpe `storage/framework/views`, `storage/framework/cache`.
- Com SSH: execute:
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

7) Testes pós-deploy
- Acesse `https://seu-dominio/health.html` — deve mostrar `OK`.
- Acesse `https://seu-dominio/` — se apresentar erro de autoload, verifique se `public_html/vendor/composer/autoload_real.php` existe.
- Se mostrar erro 500, temporariamente ative `APP_DEBUG=true` no `.env` para ver detalhes, depois reverta para `false`.

Segurança adicional para `.env` (já no `.htaccess` enviado)
- O `.htaccess` bloqueia acesso direto a `.env` e tenta proteger `storage`/`vendor`.

Se quiser, eu posso:
- Gerar um arquivo ZIP do `vendor/` local (não posso executar `composer` no seu PC), ou
- Gerar patches dos arquivos `index.php` e `.htaccess` (já feitos nesta pasta `deploy/`) para você baixar e copiar.

Checklist rápida depois do upload
- [ ] `public_html/index.php` atualizado (use `deploy/public_html_index.php`)
- [ ] `public_html/.htaccess` atualizado (use `deploy/public_html.htaccess`)
- [ ] `public_html/vendor/composer/autoload_real.php` presente
- [ ] `public_html/health.html` acessível
- [ ] Permissões ajustadas
- [ ] `.env` preenchido com credenciais do MySQL e `APP_DEBUG=false`

Se quiser, eu gero agora o `index.php` e `.htaccess` no repositório (já gerei). Vou orientar você a enviar o `vendor/` por FTP. Se precisar do passo-a-passo para o FileZilla, digo agora.