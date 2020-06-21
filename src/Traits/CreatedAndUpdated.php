<?php
declare(strict_types=1);

namespace App\Traits;

trait CreatedAndUpdated
{
    use CreatedAt;
    use CreatedBy;
    use UpdatedAt;
    use UpdatedBy;
}
