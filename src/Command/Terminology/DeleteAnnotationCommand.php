<?php
declare(strict_types=1);

namespace App\Command\Terminology;

use App\Entity\Terminology\Annotation;

class DeleteAnnotationCommand
{
    public function __construct(private Annotation $annotation)
    {
    }

    public function getAnnotation(): Annotation
    {
        return $this->annotation;
    }
}
