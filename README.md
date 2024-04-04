
# Sistema Para gestão de curriculos

O sistema foi desenvolvido em php e tem como objetivo permitir cadastrar um currículo por meio de um formulário o curriculo é armazenado e os dados são salvos no Mysql atualmente a parte do formulário de cadastro do currículo, cadastro de vagas e a página de alteração do curriculo não foram desenvolvidas sendo assim o presente código contém as funcionalidades do back-end, a aplicação deverá permitir que a empresa que está fazendo a seleção visualize os currículos cadastrados podendo filtrar por nome, bairro, vaga etc. Além de permitir o cadastro de vagas e modificação do status do processo seletivo para o candidato<br>

A apalicação também envia um e-mail toda vez que um novo curriculo é cadastrado ou alterado para que funcione é preciso rodar o comando composer install para baixar a lib PHP_MAILER e colocar as informações de acesso como: servidor smtp, porta, email e senha segue o exemplo abaixo: <br>
```bash
 $mail = new PHPMailer(true);

    try {

        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->charSet = 'UTF-8';
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'teste@gmail.com';
        $mail->Password   = 'senha';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // ENCRYPTION_STARTTLS;      //Habilitando criptografia TLS 
        $mail->Port       = 465;
        //Desabilitando o SSL default
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        //Emissor e receptor
        $mail->setFrom('teste@gmail.com', 'Curriculo');
        $mail->addAddress($email);
        $mail->addReplyTo('teste@gmail.com', 'Curriculo');

        //Conteúdo
        $mail->isHTML(true);
        $mail->Subject = 'Alteracao de Curriculo';
        $mail->Body    = "Segue o link para alteracao do seu curriculo: <br>" . $link_de_alteracao . ", guarde-o e nao compartilhe";

        //$mail->send();
        if (!$mail->send()) {
            echo 'Erro: ' . $mail->ErrorInfo;
        } else {
            //echo 'Email enviado com sucesso!';
            echo msg_sucesso($nome, $email);
        }
    } catch (Exception $e) {
        echo "E-mail não pode ser enviado: {$mail->ErrorInfo}";
    }
```
<br>
Altere a função envia_email nos arquivos conexao.php e atualiza-curriculo.php
<br>

## Requisitos
- Servidor Xamp com php versão 7.4 + <br>
- Composer: versão 1.7.2
- Mysql

## Estrutura do banco
Criei scripts para a criação do banco de dados para isso insira os valores para conexao com o mysql nos aquivos config.ini em seguida remova o arquivo .htaccess da pasta scripts banco e com o apache e mysql em execução execute o script versionamento-banco.php exemplo:
http://127.0.0.1/gestao%20de%20curriculos/versionamento-banco.php esse script criará toda a estrutura de tabelas no banco de dados em seguida execute o script query_insert_into_usuario.php da mesma forma esse criará um usuário para acessara aplicação 
## Sugestões de modificações futuras 
- Colocar a aplicação no modelo MVC separando o back-end do front-end
