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
        $result = $content;
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                continue;
            }

            if (is_array($value)) {
                $value = implode('', $value);
            }

            $result = str_replace('{{$'.$key.'}}', (string) $value, $result);
        }

        return $result;
    }

}