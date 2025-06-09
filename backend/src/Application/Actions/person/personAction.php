<?php

declare(strict_types=1);

namespace App\Application\Actions\person;

use App\Application\Actions\Action;
use App\Domain\person\PersonRepository;
use Psr\Log\LoggerInterface;

abstract class personAction extends Actions
{
    protected PersonRepository $personRepository;

    public function __construct(LoggerInterface $logger, personRepository $personRepository)
    {
        parent::__construct($logger);
        $this->personRepository = $personRepository;
    }
}