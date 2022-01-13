<?php
declare(strict_types=1);

namespace App\Command\Terminology;

use App\Entity\Terminology\Annotation;

class DeleteAnnotationCommand
{
    private Annotation $annotation;

    public function __construct(Annotation $annotation)
    {
        $this->annotation = $annotation;
    }

    public function getAnnotation(): Annotation
    {
        return $this->annotation;
    }
}
