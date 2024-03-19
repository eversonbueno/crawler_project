<?php


namespace App\Http\Controllers;


use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\CurrencyCaptureService;
use mysql_xdevapi\Exception;

class CurrencyCaptureController extends Controller
{
    protected $currencyCaptureService;

    public function __construct(CurrencyCaptureService $currencyCaptureService)
    {
        $this->currencyCaptureService = $currencyCaptureService;
    }

    public function search(Request $request)
    {
        $urlRequest = 'https://pt.wikipedia.org/wiki/ISO_4217';
        $paramsRequest = ['code', 'code_list', 'number', 'number_lists'];

        try {
            $typeRequest = gettype($request->request->filter($request->request->keys()[0]));

            if ($request->request->keys() and !in_array($request->request->keys()[0], $paramsRequest)) {
                throw new \Exception('O parametro informado não é valido para a rota');
            }

            if ($request->request->keys() and
                (!isset($request->request->keys()[0]) || $request->request->keys()[0] == '')) {
                throw new \Exception('Não foi informado parametros para a pesquisa');
            }

            // Inicia a biblioteca cURL
            $ch = curl_init();

            // Define a URL para fazer a requisição
            curl_setopt($ch, CURLOPT_URL, $urlRequest);

            // Habilita a opção para retornar a resposta
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

            // Executa a requisição e armazena a resposta
            $response = curl_exec($ch);

            // Fecha a conexão
            curl_close($ch);

            if ($response == FALSE) {
                throw new \Exception('Falha na requisição para a URL ');
            }

            return $this->currencyCaptureService->serviceSearch($response, $request, $typeRequest);
        } catch (\Exception $e) {
            return json_encode(['message' => 'Erro na execução do Crawler', 'currencies' => [],
                'error_message' => $e->getMessage(), 'error_tracing' => $e->getTraceAsString()]);
        }

    }
}
