<?php
declare(strict_types=1);

namespace App\Graph\Resource;

use EasyRdf_Graph;

interface GraphResource
{
    public function toGraph(): EasyRdf_Graph;

    // public function addToGraph(?string $subject, ?string $predicate, EasyRdf_Graph $graph): EasyRdf_Graph;
}
