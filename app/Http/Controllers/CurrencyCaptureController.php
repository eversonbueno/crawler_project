<?php


namespace App\Http\Controllers;


use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurrencyCaptureController extends Controller
{
    public function __invoke(string $code, string $codeList, int $number, int $numberList) {
        // Use the parameters…
    }

    public function search(Request $request)
    {
        $urlRequest = 'https://pt.wikipedia.org/wiki/ISO_4217';
        $paramsRequest = ['code', 'code_list', 'number', 'number_lists'];
        $currencies = [];

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

            // Cria um DOMDocument para manipular o HTML
            $dom = new DOMDocument();

            // Carrega o HTML na instância de DOMDocument. O '@' é usado para suprimir erros de parsing
            @$dom->loadHTML($response);

            $xpath = new DOMXpath($dom);

            $requests = $request->request->filter($request->request->keys()[0]);
            if ($typeRequest !== 'array') {
                $requests = [
                    $request->request->filter($request->request->keys()[0])
                ];
            }

            foreach ($requests as $request) {
                $tables = $xpath->query(
                    '//table[@class="wikitable sortable"]/tbody/tr/td[normalize-space(text()) = "' .
                    $request . '"]');
                $currency_locations = [];

                foreach ($tables as $table) {
                    $value = $xpath->query(".//../td", $table);
                    $spans = $xpath->query('.//../span[@class="mw-image-border"]', $value->item(4));

                    foreach ($spans as $span) {
                        $imagens = $xpath->query('.//../img', $span);
                        foreach ($imagens as $imagem) {
                            if (!in_array(trim($imagem->getAttribute("src")), $currency_locations)) {
                                $currency_locations[] = ['icon' => trim($imagem->getAttribute("src"))];
                            }
                        }

                        $locations = $xpath->query('.//../a', $span);
                        foreach ($locations as $location) {
                            $currency_locations[] = ['location' => trim($location->getAttribute("title"))];
                        }

                    }

                    $currencies[] = [
                        'code' => trim($value->item(0)->textContent),
                        'number' => trim($value->item(1)->textContent),
                        'decimal' => trim($value->item(2)->textContent),
                        'currency' => trim($value->item(3)->textContent),
                        'currency_locations' => $currency_locations
                    ];
                }
            }

            return json_encode(['message' => 'Crawler executado com Sucesso', 'currencies' => json_encode($currencies),
                'error_message' => '']);

        }catch (\Exception $e) {
            return json_encode(['message' => 'Erro na execução do Crawler', 'currencies' => [],
                'error_message' => $e->getMessage(), 'error_tracing' => $e->getTraceAsString()]);
        }
    }
}
