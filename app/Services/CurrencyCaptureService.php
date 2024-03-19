<?php


namespace App\Services;


use DOMDocument;
use DOMXPath;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CurrencyCaptureService
{
    public function serviceSearch($response, $request, $typeRequest)
    {
        $currencies = [];

        try {

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
                $currencyLocations = [];

                foreach ($tables as $table) {
                    $value = $xpath->query(".//../td", $table);
                    $spans = $xpath->query('.//../span[@class="mw-image-border"]', $value->item(4));

                    foreach ($spans as $span) {
                        $locations = $xpath->query('.//../a', $span);
                        foreach ($locations as $location) {
                            $currencyLocations[] = ['location' => trim($location->getAttribute("title"))];
                        }

                        for ($i=0; $i < count($currencyLocations); $i++) {
                            $imagens = $xpath->query('.//../img', $span);
                            foreach ($imagens as $imagem) {
                                if (!array_search(trim($imagem->getAttribute("src")), $currencyLocations)) {
                                    $currencyLocations[$i]['icon'] = 'https:' . trim($imagem->getAttribute("src"));
                                }
                                foreach ($currencyLocations as $val) {
                                    if ($val['icon'] = 'https:' . trim($imagem->getAttribute("src"))) {
                                        break 3;
                                    }
                                }

                            }
                        }

                    }

                    $currencies[] = [
                        'code' => trim($value->item(0)->textContent),
                        'number' => trim($value->item(1)->textContent),
                        'decimal' => trim($value->item(2)->textContent),
                        'currency' => trim($value->item(3)->textContent),
                        'currency_locations' => $currencyLocations
                    ];
                }
            }

            return new JsonResponse(['message' => 'Crawler executado com Sucesso', 'currencies' => $currencies,
                'error_message' => '']);

        }catch (\Exception $e) {
            return $e;
        }
    }
}
