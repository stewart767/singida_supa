<?php
libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML(file_get_contents('http://localhost/supa/public/careers'));
$xpath = new DOMXPath($dom);
foreach($xpath->query('//h3') as $h3) {
    echo trim($h3->textContent) . PHP_EOL;
}
