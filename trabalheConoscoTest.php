<?php

use GuzzleHttp\Client;
use PhpParser\Node\Expr\Cast\Array_;
use PHPUnit\Framework\TestCase;
use  GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Utils;
//use File;

class trabalheConoscoTest extends TestCase
{
    private $httpClient;
    private $baseUri;
    /**
     * Testa o select da lista de funções 
     *
     * @return void
     */
    /**
     * @test
     */
    public function test_ajax_lista_funcoes_dropdown()
    {

        $this->baseUri = 'http://localhost/curriculo/consultas.php';
        $this->httpClient = new \GuzzleHttp\Client();
        $uri = $this->baseUri;
        $response = $this->httpClient->request('GET', $this->baseUri);
        // echo $response->getStatusCode(); // 200
        // echo $response->getHeaderLine('content-type'); 
        $res = $response->getBody();
        $items = json_decode($res);
        $r = $items[1]->nome_vaga;
        $result = array('Analista TI IV', 'Analista de T.I.');
        $this->assertContains($r, $result);
    }
    /**
     * Testa o retorno da lista de funções
     *
     * @return void
     */
    public function test_ajax_lista_funcoes_retorno()
    {
        $this->baseUri = 'http://localhost/curriculo/consultas.php';
        $this->httpClient = new \GuzzleHttp\Client();
        $uri = $this->baseUri;
        $response = $this->httpClient->request('GET', $this->baseUri . '?action=getfuncoes');
        // echo $response->getStatusCode(); // 200
        // echo $response->getHeaderLine('content-type'); 
        $res = $response->getBody();
        $items = json_decode($res);
        $r = $items->status;
        $result = array(200, 204);
        $this->assertContains($r, $result);
    }
    /**
     * Testa o insert de função/vaga
     *
     * @return void
     */
    public function test_ajax_insert()
    {
        $this->baseUri = 'http://localhost/curriculo/consultas.php';
        $this->httpClient = new \GuzzleHttp\Client();
        $uri = $this->baseUri;
        $response = $this->httpClient->request('POST', $this->baseUri, ['form_params' => [
            'action' => 'addfuncoes',
            'nome_vaga' => 'AAAA TESTE02347'
        ]]);
        // echo $response->getStatusCode(); // 200
        // echo $response->getHeaderLine('content-type'); 
        $res = $response->getBody();
        $items = json_decode($res);
        $r = $items->status;
        $result = array(200, 204);
        $this->assertContains($r, $result);
    }
    /**
     * Testa o delete de função/vaga
     *
     * @return void
     */
    public function test_ajax_delete()
    {
        $this->baseUri = 'http://localhost/curriculo/consultas.php';
        $this->httpClient = new \GuzzleHttp\Client();
        $uri = $this->baseUri;
        $response = $this->httpClient->request('POST', $this->baseUri, ['form_params' => [
            'action' => 'delfuncoes',
            'nome_vaga' => 'AAAA TESTE02347'
        ]]);
        // echo $response->getStatusCode(); // 200
        // echo $response->getHeaderLine('content-type'); 
        $res = $response->getBody();
        $items = json_decode($res);
        $r = $items->status;
        $result = array(200, 204);
        $this->assertContains($r, $result);
    }
    /**
     * Testa a inserção de um novo cadastro
     *
     * @return void
     */
    public function test_insercao_form()
    {

        $this->baseUri = 'http://localhost/curriculo/conexao.php';
        $this->httpClient = new \GuzzleHttp\Client();
        $uri = $this->baseUri;
        global $media;


        $response = $this->httpClient->post($this->baseUri, [
            'multipart' => [
                [

                    'name' => 'arquivo',
                    'contents' => fopen('C:\xampp\htdocs\curriculo\teste.pdf', 'r'),

                ],
                [
                    'name' => 'action',
                    'contents' => 'cadastrar',
                ],
                [
                    'name' => 'nome',
                    'contents' => 'Teste02',
                ],
                [
                    'name' => 'data_nasc',
                    'contents' => '1997-08-29',
                ],
                [
                    'name' => 'email',
                    'contents' => 'teste@gmail.com.br',
                ],
                [
                    'name' => 'endereco',
                    'contents' => 'Rua das Flores',
                ],
                [
                    'name' => 'bairro',
                    'contents' => 'Bairro x',
                ],
                [
                    'name' => 'cidade',
                    'contents' => 'Salvador',
                ],
                [
                    'name' => 'estado',
                    'contents' => 'Bahia',
                ],
                [
                    'name' => 'cep',
                    'contents' => '41245784',
                ],
                [
                    'name' => 'whatsapp',
                    'contents' => '71874547854',
                ],
                [
                    'name' => 'telefone',
                    'contents' => '',
                ],
                [
                    'name' => 'funcao',
                    'contents' => 'Almoxorifado',
                ],
                [
                    'name' => 'especial',
                    'contents' => 'sim',
                ],
                [
                    'name' => 'qual_necessidade',
                    'contents' => 'a',
                ],
                [
                    'name' => 'tem_indicacao',
                    'contents' => 'sim',
                ],
                [
                    'name' => 'quem_indicou',
                    'contents' => 'b',
                ],
                [
                    'name' => 'aceite_termo',
                    'contents' => 'sim',
                ],
                [
                    'name' => 'cadastrar',
                    'contents' => 'submit',
                ],
                [
                    'name' => 'arquivo',
                    'contents' => 'banco/documentos/teste.pdf'
                ],
            ],
        ]);
        //  $res = $response->getBody();
        // $items = json_decode($res);
        echo "***********************************************\n";
        $body = json_encode($response->getBody()->getContents());
        var_dump($body);

        echo "***********************************************\n";
        $search = 'Seu curriculo foi cadastrado com Sucesso!';
        global $resultado;
        $resultado = false;
        if (strpos($body, $search) !== false) {
            echo $resultado = true;
        } else {
            echo "ERRO => Não cadastrado";
        }

        $this->assertEquals(true, $resultado);
     
    }
}
