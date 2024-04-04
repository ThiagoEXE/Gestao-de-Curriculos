## Pré-requisitos:

-Sistema: Windows Server<br>
-Composer: versão 1.7.2<br>
-PHP: versão 5.6.24<br>

Para começar é preciso ter instalado e configurado o [XAMPP](https://www.youtube.com/watch?v=FkuKKtBUK_I) em sua máquina. 

Recomendamos o uso do [VSCODE](https://code.visualstudio.com/) para editar o código.

Instale o [SGBD MYSQL-WORKBANCH](https://www.youtube.com/watch?v=aian0uwqtgE) para fazer algumas operações na base de dados como: exportação, importação etc...


## Instalação
Para intalar o sistema do zero siga os seguintes passos: certifique-se de ter instalado o [XAMPP](https://www.apachefriends.org/blog/new_xampp_20221001.html) com a versão correspondente do Php, para este projeto usamos a versão 5.6.24

Para verificar a versão do Php digite no terminal:

```bash
php --version
```

<br><br>
Inicie o XAMPP e clique em 'start' em: Apache e Mysql, você pode visualizar a base de dados local clicando em 'Admin'
<br><br>

![Untitled](./imagens/xampp.png)

## Passo 1

Para instalar o projeto faça um clone para a sua máquina.

```bash
  git clone https://Thiago_BarbosaOttrans@bitbucket.org/ottrans/curriculo.git
```


Quando clonar o projeto o mesmo virá na versão mais recente, caso queira instalar outra versão você pode usar o comando abaixo, se não pode ignorar essa etapa:

```bash
git checkout nome_da_branch
```

Para listar todas as branches execute:
```bash
git branch --list
```
<br><br>
![Untitled](./imagens/branch.png)
<br><br>
Exemplo de mudança de branch:
```bash
git checkout versao1.0
```

Seu projeto irá para a versão 1.0

Note que o projeto foi clonado na pasta 'htdocs', para que seja possível executá-lo através do XAMPP.

Em seguida acesse a pasta do projeto:
```bash
  cd curriculo
```

Dentro da pasta digite o comando abaixo no termial para abrir o VSCODE:
```bash
  code .
```

## Versão 2.0

Primeiro é preciso executar o script de criação/atualização do banco de dados para isso verifique o arquivo .htaccess que esta na raiz do projeto e remova as diretivas abaixo caso existam, para que eseja possivel executar os scripts via web:

```bash
<Files "versionamento-banco.php">
    Order Allow,Deny
    Deny from all
</Files>
<Files "query_insert_into_usuario.php">
    Order Allow,Deny
    Deny from all
</Files>

```

Agora acesse no browser a seguinte url para criar o banco de dados:
```bash
ip-do-servidor:porta/curriculo/versionamento-banco.php
exemplo: 10.1.2.89:8081/curriculo/versionamento-banco.php
```

Isso irá criar as tabelas necessárias para o projeto no banco de dados, agora acesse novamente pelo browser a url abaixo para criar um usuário:

```bash
ip-do-servidor:porta/curriculo/query_insert_into_usuario.php
exemplo: 10.1.2.89:8081/curriculo/query_insert_into_usuario.php
```

Em seguida adicione novamente as diretivas de restrição de acesso no arquivo .htaccess, ou você pode remover o arquivo existente e renomear o arquivo .htaccess-new para .htaccess, pois o mesmo já possui essas diretivas de bloqueio:

```bash
<Files "versionamento-banco.php">
    Order Allow,Deny
    Deny from all
</Files>
<Files "query_insert_into_usuario.php">
    Order Allow,Deny
    Deny from all
</Files>
```
#### Siga para o Passo 2...

## Versão 1.0

Execute no terminal o seguinte comando para criar o banco de dados:
```bash
php query_create_data_base_wp_ottrans.php
```

O próximo passo é criar as tabelas, exetute os comando abaixo:
```bash
php query_create_table_banco_talentos.php
```
```bash
php query_create_table_vagas.php
```
<br><br>
![Untitled](./imagens/querys.png)

Note que as saídas indicaram que o banco e as tabelas foram criados, no exemplo acima as mesmas já existiam.

Pronto! Verifique no XAMPP se o banco e as tabelas foram criadas.
<br><br>
![Untitled](./imagens/banco-talentos.png)
<br><br>
![Untitled](./imagens/vagas.png)

#### Siga para o Passo 2...

### Versão 1.5
Caso voce use a versão 1.5 será necessário criar uma nova coluna chamada 'status_processo' na tabela 'banco_talentos'. Se estiver em ambiente de teste você pode pegar um backup dos dados importá-lo para seu banco atual e depois executar o comando abaixo:

```bash
php query_alter_table_banco_talentos.php
```

![Untitled](./imagens/alter-table.png)

Note que ao fazer isso todos os registros serão atualizados para o status de 'Cadastrado'. O erro acima é devido a tabela ja ter sido atualizada antes.

#### Siga para o Passo 2...

## Passo 2
Agora você apenas precisa executar o comando do composer na raiz do projeto para instalar as bibliotecas necessárias:

```bash
composer install
```
Após isso o projeto já estará funcionando, lembre-se de estar com o Apache funcionado no painel do Xampp
## Autores

- [@thiagoexe](https://github.com/ThiagoEXE)
- [@jorgenery](https://github.com/jorgenery)


## Links úteis 
[Página do candidato](http://10.1.2.81:8070/curriculo/)

[Banco de Talentos](http://10.1.2.81:8070/curriculo/bancotalentos/)

[Lista de Funções](http://10.1.2.81:8070/curriculo/bancotalentos/lista-de-funcoes.php)


