<?php

namespace App\Entity\Enum;

/**
 *      @ method static static interventional()
 *      @ method static static observational()
 *      @ method static static registry()
 *      @ method bool isInterventional()
 *      @ method bool isObservational()
 *      @ method bool isRegistry()
 */
class StudyType extends Enum
{
    private const INTERVENTIONAL = 'interventional';
    private const OBSERVATIONAL = 'observational';
    private const REGISTRY = 'registry';
}