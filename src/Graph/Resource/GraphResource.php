<?php
declare(strict_types=1);

namespace App\Graph\Resource;

use EasyRdf\Graph;

interface GraphResource
{
    public function toGraph(string $baseUrl): Graph;

    // public function addToGraph(?string $subject, ?string $predicate, Graph $graph): Graph;
}
