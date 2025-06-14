<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Compiler\Engine;


use Illuminate\Contracts\View\Engine;

class ReplaceEngine implements Engine
{
    public function get($path, array $data = []): string
    {
        $content = file_get_contents($path);
        $content = $this->populateData($content, $data);


        return $content;
    }


    protected function populateData(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            if (!is_object($value)) {
                if (is_array($value)) {
                    $string = '';
                    foreach ($value as $val) {
                        $string .= $val;
                    }
                    
                    $value = $string;
                }
                $content = str_replace('{{$' . $key . '}}', (string)$value, $content);
            }
        }

        return $content;
    }

}