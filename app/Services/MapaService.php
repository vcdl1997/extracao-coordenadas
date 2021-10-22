<?php

namespace App\Services;

class MapaService{

    public static function obterListaDeEnderecos() :array
    {
        // Retorna uma lista dos arquivos contidos na pasta csv-padrao
        $planilhas = scandir("C:\Users\DELL\Documents\projeto-mapa\storage\csv-padrao");

        // Filtra apenas os arquivos que tiverem o nome iniciando com "enderecos-"
        $planilhas = array_filter($planilhas, function($arquivo){
            if(strpos($arquivo, "enderecos-") !== false) return $arquivo;
        });

        // Realiza a ordenação das pastas de forma alfabética
        natsort($planilhas);

        // Reordena os indices do array
        $planilhas = array_values($planilhas);

        $array = [];

        foreach($planilhas as $planilha){
            $arrLinhas = file("C:\Users\DELL\Documents\projeto-mapa\storage\csv-padrao " . DIRECTORY_SEPARATOR .  $planilha);

            foreach($arrLinhas as $linha){
                $params = explode(",\"", $linha);
                $endereco = str_replace(array("\n", "\r", "\""), '', $params[1]);
                $teste = explode(",", $params[0]);

                $array[] = [
                    'ejfid'     => key_exists(0, $teste) ? $teste[0] : '',
                    'enjdsc'    => key_exists(1, $teste) ? $teste[1] : '',
                    'endid'     => key_exists(2, $teste) ? $teste[2] : '',
                    'endereco'  => $endereco
                ];
            }
        }

        // return $array; // retornar todos
        return array_slice($array, 0, 3);
    }
}
