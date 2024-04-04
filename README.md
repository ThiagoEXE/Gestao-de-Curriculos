
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
## Sugestões de modificações futuras 
- Colocar a aplicação no modelo MVC separando o back-end do front-end
