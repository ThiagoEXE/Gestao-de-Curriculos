<?php
// Inclui o padrão xml e o arquivo de conexão com o banco de dados

include('seleciona-candidatos.php');
echo '<?xml version="1.0" encoding="ISO-8859-1"?>';

/**
 * Função que verifica a origem da requisição permitindo apenas que ip's com pré-fixo 10. . . .
 * possam ter acesso aos dados do banco de talentos, sendo assim a regra é para que apenas pessoas 
 * que estejam em estações de trabalho dentro da empresa possam ter acesso
 *
 * @return void
 */

if (!valida_acesso()) {
  header("Location: https://google.com");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="pt-br">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta charset="UTF-8">
  <title>Candidatos</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
  <!-- https://datatables.net/ -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.12.1/datatables.min.css" />
  <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.12.1/datatables.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
  <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
  <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
  <style>
    caption {
      caption-side: top;
      padding-bottom: 3px
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="row">
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item active">
              <a class="nav-link" href="#">Banco de Talentos</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="lista-de-funcoes.php">Lista de funções</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../">Formulário</a>
            </li>
          </ul>
        </div>
        <span class="navbar-text">
          <a href="logout.php" class="btn btn-danger" role="button" aria-pressed="true">Sair</a>
        </span>
      </nav>
    </div>
  </div>
  <div class="container">
    <table id="grid-bancotalentos" class="table table-striped" style="width:100%">
      <caption>
        <div><select id="status">
            <option default>Selecione a Ação</option>
            <option value="Aprovado">Aprovado</option>
            <option value="Reprovado">Reprovado</option>
            <option value="Em Seleção">Em Seleção</option>
            <option value="Cadastrado">Cadastrado</option>
          </select></div>
      </caption>
      <thead>
        <tr>
          <td>Currículo</td>
          <td>Nome</td>
          <td>Idade</td>
          <td>E-mail</td>
          <td>Endereço</td>
          <td>Bairro</td>
          <td>Cidade</td>
          <td>Whatsapp</td>
          <td>Telefone</td>
          <td>Funçao</td>
          <td>Qual deficiência?</td>
          <td>Quem indicou?</td>
          <td>Status</td>
          <td>Data do cadastro</td>
        </tr>
      </thead>
      <tbody>
        <?php
        /**
         * Variável que recebe o retorno do método selecao() contendo a conexão com o banco e é associada a um array.
         *
         */
        $conexao = selecao_candidatos();
        while ($dado = $conexao->fetch_array()) {
        ?>

          <tr>
            <td><input type="checkbox" data-email="<?= $dado['email']; ?>" id="status_check" class="check_status">
              <a class="btn" href="downloads.php?hash=<?= $dado['arquivo'] ?>&nome=<?= $dado['nome'] ?>">

                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-down" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M3.5 10a.5.5 0 0 1-.5-.5v-8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 0 0 1h2A1.5 1.5 0 0 0 14 9.5v-8A1.5 1.5 0 0 0 12.5 0h-9A1.5 1.5 0 0 0 2 1.5v8A1.5 1.5 0 0 0 3.5 11h2a.5.5 0 0 0 0-1h-2z" />
                  <path fill-rule="evenodd" d="M7.646 15.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 14.293V5.5a.5.5 0 0 0-1 0v8.793l-2.146-2.147a.5.5 0 0 0-.708.708l3 3z" />
                </svg>
              </a>
            </td>
            <td><?php echo $dado['nome']; ?></td>
            <td><?php echo $dado['idade']; ?></td>
            <td><?php echo $dado['email']; ?></td>
            <td><?php echo $dado['endereco']; ?></td>
            <td><?php echo $dado['bairro']; ?></td>
            <td><?php echo $dado['cidade']; ?></td>
            <td><?php echo $dado['whatsapp']; ?></td>
            <td><?php echo $dado['telefone']; ?></td>
            <td><?php echo $dado['funcao']; ?></td>
            <td><?php echo $dado['qual_deficiencia']; ?></td>
            <td><?php echo $dado['quem_indicou']; ?></td>
            <td><?php echo $dado['status_processo']; ?></td>
            <td><?php echo $dado['data_inclusao'] ?></td>
          </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr>
          <td>Currículo</td>
          <td>Nome</td>
          <td>Idade</td>
          <td>E-mail</td>
          <td>Endereço</td>
          <td>Bairro</td>
          <td>Cidade</td>
          <td>Whatsapp</td>
          <td>Telefone</td>
          <td>Funçao</td>
          <td>Qual deficiência?</td>
          <td>Quem indicou?</td>
          <td>Status</td>
          <td>Data do cadastro</td>
          <br>
        </tr>
      </tfoot>
    </table>
  </div>
  <script>
    /**
     *Uso de um Framework para seleceionar os registros em 10, 25, ou 50 linhas 
     *promovendo a paginação.
     */
    $(document).ready(function() {
      var table = $('#grid-bancotalentos').DataTable({
        language: {
          search: 'Procura:',
          url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json',
        },
        lengthChange: false,
        dom: 'Bfrtip',
        lengthMenu: [
          [10, 25, 50, -1],
          ['10 linhas', '25 linhas', '50 linhas', 'Tudo']
        ],
        buttons: [
          'copyHtml5',
          'excelHtml5',
          'csvHtml5',
          'pageLength'
        ]
      });
      //Inicia a tabela com valor default na barra de pesquisa "esse valor é um cookie da ultima busca"
      $(document).ready(function() {
        var table = $('#grid-bancotalentos').DataTable();
        var valor_padrao = getCookie("texto");
        $('input[type="search"').val(valor_padrao);
        table.search(valor_padrao).draw();
      });
    });
  </script>
  <!--Script que captura a última busca usando um timeout de 1 segundo-->
  <script>
    var typingTimer;
    var doneTypingInterval = 1000;

    $('#grid-bancotalentos').on('search.dt', function() {
      clearTimeout(typingTimer);
      if ($('.dataTables_filter input').val()) {
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
      }
    });

    function doneTyping() {
      var value = $('.dataTables_filter input').val();
      checkCookie();
    }
  </script>

  <!--Script para gera e recupera um cookie com base no valor pesquisado -->
  <script>
    //parte de criacao do cookie
    function setCookie(cname, cvalue, exdays) {
      var d = new Date();
      d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
      var expires = "expires=" + d.toGMTString();
      document.cookie = cname + "=" + cvalue + ";" + ";path=/";
    }

    function getCookie(cname) {
      var name = cname + "=";
      var decodedCookie = decodeURIComponent(document.cookie);
      var ca = decodedCookie.split(';');

      for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
          c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
          return c.substring(name.length, c.length);

        }
      }
      return "";
    }

    function checkCookie() {

      let pesquisa = $('.dataTables_filter input').val();

      if (pesquisa != "" && pesquisa != null) {
        setCookie("texto", pesquisa, 30);

      } else {
        alert("erro");
      }
      nomeatual = getCookie("texto");
    }
  </script>
  <!--Script para mudança de status-->
  <script>
    $(document).ready(function() {
      $("#status").on('change', function() {
        var contador = 0;
        var selecionados = [];
        $.each($("input[class='check_status']:checked"), function() {
          selecionados.push({
            email: $(this).attr("data-email")
          });
          contador = contador + 1;
        });
        if (contador > 0) {
          if (confirm('Confirma Ação : ' + this.value + ' ?')) {
            $.ajax({
              type: "POST",
              url: "../consultas.php",
              cache: false,
              data: {
                action: "status",
                status: this.value,
                lista: selecionados
              },
              dataType: "json",
              success: function(dataresult) {
                console.log(dataresult);
              },
              error: function(erro) {
                console.log(erro);
                alert('Ocorreu algum erro!');
              },
              complete: function(data, textStatus, jqXHR) {
                //location.reload();
              }
            });
          }
        }
        this.value = 'Selecione a Ação';
      });
    });
  </script>
  <div class="container">
    <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
      <div class="col-md-4 d-flex align-items-center">
        <a href="/" class="mb-3 me-2 mb-md-0 text-muted text-decoration-none lh-1">
          <svg class="bi" width="30" height="24">
            <use xlink:href="#bootstrap" />
          </svg>
        </a>
        <span class="mb-3 mb-md-0 text-muted">&copy; Desenvolvido por Thiago Barbosa</span>
      </div>
    </footer>
  </div>
</body>

</html>