<?php namespace Admin\Services;
/**
 * 
**/
class Yaml
{
    /**
     * Parse a YAML file into an array.
     *
     * @param string $file Path to the YAML file.
     * @return array
     */
    public function parse($file)
    {
        if (!file_exists($file)) {
            return [];
        }

        if (function_exists('yaml_parse_file')) {
            return yaml_parse_file($file);
        }

        return $this->simpleParse(file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    }

    /**
     * Parse a YAML string (simple implementation).
     * Only supports basic key: value and simple nested arrays.
     */
    protected function simpleParse($lines)
    {
        $data = [];
        $lastIndent = 0;
        $context = [&$data];
        $path = [0];

        foreach ($lines as $line) {
            $line = rtrim($line);
            if (empty($line) || strpos(ltrim($line), '#') === 0) {
                continue;
            }

            // Calculate indent
            preg_match('/^(\s*)/', $line, $matches);
            $indent = strlen($matches[1]);
            $content = trim($line);

            // Find parent based on indent
            while ($indent < end($path)) {
                array_pop($path);
                array_pop($context);
            }

            // Key-Value pair
            if (strpos($content, ':') !== false) {
                list($key, $value) = explode(':', $content, 2);
                $key = trim($key);
                $value = trim($value);

                // Handle basic types
                if (strtolower($value) === 'true') $value = true;
                elseif (strtolower($value) === 'false') $value = false;
                elseif (is_numeric($value)) $value = $value + 0;
                elseif ($value === '') $value = null; // Could be start of array/object
                elseif ((substr($value, 0, 1) == '"' && substr($value, -1) == '"') || (substr($value, 0, 1) == "'" && substr($value, -1) == "'")) {
                    $value = substr($value, 1, -1);
                }

                $current = &$context[count($context) - 1];
                
                if ($value === null) {
                    $current[$key] = [];
                    $context[] = &$current[$key];
                    $path[] = $indent + 2; // Assume 2 spaces indent for children
                } else {
                    $current[$key] = $value;
                }
            }
            // List item (very basic support)
            elseif (strpos($content, '-') === 0) {
                 $value = trim(substr($content, 1));
                 $current = &$context[count($context) - 1];
                 // Basic array push
                 if (!is_array($current)) $current = [];
                 $current[] = $value;
            }
        }
        return $data;
    }

    /**
     * Dump an array to a YAML file.
     *
     * @param string $file Path to save the file.
     * @param array $data Data to save.
     * @return bool|int
     */
    public function dump($file, $data)
    {
        if (function_exists('yaml_emit_file')) {
            return yaml_emit_file($file, $data);
        }

        $content = $this->simpleDump($data);
        return file_put_contents($file, $content);
    }

    protected function simpleDump($data, $indent = 0)
    {
        $output = '';
        $spacing = str_repeat('  ', $indent);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (array_keys($value) === range(0, count($value) - 1)) {
                    // Indexed array (list)
                    $output .= $spacing . $key . ":\n";
                    foreach ($value as $item) {
                        $output .= $spacing . '  - ' . $item . "\n";
                    }
                } else {
                    // Associative array
                    $output .= $spacing . $key . ":\n";
                    $output .= $this->simpleDump($value, $indent + 1);
                }
            } else {
                $rawVal = $value;
                if (is_bool($value)) $rawVal = $value ? 'true' : 'false';
                elseif (is_string($value) && (strpos($value, ':') !== false || strpos($value, '#') !== false)) {
                     $rawVal = '"' . $value . '"';
                }
                $output .= $spacing . $key . ': ' . $rawVal . "\n";
            }
        }
        return $output;
    }
}
