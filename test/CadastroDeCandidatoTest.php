<?php
include("./conexao.php");
use PHPUnit\Framework\TestCase;

class CadastroDeCandidatoTest extends TestCase
{
    public function testCadastrarCandidatoSucesso()
    {
        // Simula uma conexão com o banco de dados (pode usar um mock para isso)
        $connect = $this->createMock(mysqli::class);
        $result_data_nasc_formatada = '25-12-1996';
        $caminho_arquivo = '/caminho/do/arquivo.txt';

        // Simula os dados do formulário
        $_POST = [
            'action' => 'cadastrar',
            'nome' => 'Teste02',
            'data_nasc' => '15-06-1997',
            'email' => 'cadastrar@123',
            'endereco' => 'Rua das Araras',
            'bairro' => 'Pituba',
            'cidade' => 'Salvador',
            'estado' => 'Bahia',
            'cep' => '41245874',
            'whatsapp' => '71985698569',
            'telefone' => '',
            'funcao' => 'Almoxorifado',
            'especial' => 'sim',
            'qual_necessidade' => 'a',
            'tem_indicacao' => 'sim',
            'quem_indicou' => 'b',
            'aceite_termo' => 'sim',
            'cadastrar' => 'submit',
            'arquivo' => 'banco/documentos/teste.pdf'
        ];

        $resultado = cadastrar($connect, $result_data_nasc_formatada, $caminho_arquivo);
        // Testa se a função cadastrarCandidato não lança exceções
        var_dump($resultado);
        /*$this->assertDoesNotThrow(function () use ($connect, $result_data_nasc_formatada, $caminho_arquivo) {
        });*/
    }
}
