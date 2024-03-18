<?php


namespace App\Http\Controllers;


use DOMDocument;
use DOMXPath;

class CurrencyCaptureController extends Controller
{
    public function search()
    {
        // Inicia a biblioteca cURL
        $ch = curl_init();

        // Define a URL para fazer a requisição
        curl_setopt($ch, CURLOPT_URL, "https://pt.wikipedia.org/wiki/ISO_4217");

        // Habilita a opção para retornar a resposta
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        // Executa a requisição e armazena a resposta
        $response = curl_exec($ch);

        // Fecha a conexão
        curl_close($ch);

        // Verifica se a requisição foi bem-sucedida
        if ($response !== FALSE) {
            // Cria um DOMDocument para manipular o HTML
            $dom = new DOMDocument();

            // Carrega o HTML na instância de DOMDocument. O '@' é usado para suprimir erros de parsing
            @$dom->loadHTML($response);

            $xpath = new DOMXpath($dom);

            $tables = $xpath->query("//table[@class=\"wikitable sortable\"]");
            $values = $xpath->query(".//tbody/tr", $tables->item(0));
            dump($values);
            die('final');
            $currencies = [];

            foreach ($values as $value){
                dump(typeOf($value));
//                dump($value->items(0));
//                $table = $xpath->query(".//td", $value);
//                dump($table->item(2));
//                dump($table2->item(1));
//                dump(trim($table2->item(2)->textContent));
//                dump(trim($table->item(3)->textContent));
//                dump(trim($table->item(4)->textContent));
//                dump(trim($table->item(5)->textContent));

                /* Acessa o conteúdo em texto do primeiro elemento TD */
//                $code = trim($meta->item(2)->textContent);
//                dump($code);
//                $number = trim($meta->item(3)->textContent);
//                dump($number);
//                $currency = trim($meta->item(1)->textContent);
//                dump($currency);
            }


                // Obtém o elemento title
//            $title = $dom->getElementsByTagName('title')->item(0)->nodeValue;

            // Obtém os meta tags
//            $bodys = $dom->getElementsByTagName('body');

            $description = "";
            $keywords = "";

//            for ($i = 0; $i < $codes->length; $i++) {
//                $code = $codes->item($i);
//                echo 'Passei por aqui';
//            }

            // Loop para obter a description e keywords
//            for ($i = 0; $i < $bodys->length; $i++) {
//                $meta = $bodys->item($i);
//                var_dump($meta);
//                var_dump($meta->documentElement->getAttributeNames());

//                dump($meta->getElementsByTagName('body'));
//                print_r($meta->getElementsByTagName('html'));

//                print_r($meta->getAttribute('parentNode'));

//                if (strtolower($meta->getAttribute('name')) == 'description') {
//                    $description = $meta->getAttribute('content');
//                }
//                if (strtolower($meta->getAttribute('name')) == 'keywords') {
//                    $keywords = $meta->getAttribute('content');
//                }
//            }

//            // Exibe os valores obtidos
//            echo "Title: $title<br>";
//            echo "Description: $description<br>";
//            echo "Keywords: $keywords<br>";

            return 'Finalizado';
        }
    }
}
