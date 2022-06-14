<?php
$url = 'https://bundlephobia.com/api/size';
$search_url = $url . '?package=';

$query = $argv[1];
$results = array();

function toxml($data = null, $format = 'array') {
    global $results;

    if ($format == 'json'):
        $data = json_decode($data, TRUE);
    endif;

    if (is_null($data) && !empty($results)):
        $data = $results;
    elseif (is_null($data) && empty($results)):
        return false;
    endif;

    $items = new SimpleXMLElement("<items></items>");    // Create new XML element

    foreach ($data as $b):                                // Lop through each object in the array
        $c = $items->addChild('item');                // Add a new 'item' element for each object
        $c_keys = array_keys($b);                        // Grab all the keys for that item
        foreach ($c_keys as $key):                        // For each of those keys
            if ($key == 'uid'):
                $c->addAttribute('uid', $b[$key]);
            elseif ($key == 'arg'):
                $c->addAttribute('arg', $b[$key]);
            elseif ($key == 'valid'):
                if ($b[$key] == 'yes' || $b[$key] == 'no'):
                    $c->addAttribute('valid', $b[$key]);
                endif;
            elseif ($key == 'autocomplete'):
                $c->addAttribute('autocomplete', $b[$key]);
            else:
                $c->$key = $b[$key];
            endif;
        endforeach;
    endforeach;

    return $items->asXML();                                // Return XML string representation of the array

}

function formatBytes($bytes, $precision = 1) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . $units[$pow];
}

$results[] = array(
    'uid' => 'placeholder',
    'title' => 'Go to the website',
    'subtitle' => $url,
    'arg' => $url,
    'icon' => 'icon.png',
    'valid' => 'yes'
);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $search_url . $query . '&record=true',
));
$output = curl_exec($curl);
curl_close($curl);
$data = json_decode($output);

$results = array();

if (!empty($data)) {
    $results[] = array(
        'uid' => $data->name,
        'title' => $data->name . '(GZip:' . formatBytes($data -> gzip) . ', Minify:' . formatBytes($data -> size) . ')',
        'subtitle' => $data->description,
        'arg' => 'https://bundlephobia.com/result?p=' . $data->name,
        'icon' => 'icon.png',
        'valid' => 'yes'
    );
} else {
    $results[] = array(
        'uid' => 'placeholder',
        'title' => 'No documents were found that matched "'.$query.'".',
        'subtitle' => 'Click to see the results for yourself',
        'arg' => 'https://bundlephobia.com/result?p=' . $query,
        'icon' => 'icon.png',
        'valid' => 'yes'
    );
}

echo toxml();
