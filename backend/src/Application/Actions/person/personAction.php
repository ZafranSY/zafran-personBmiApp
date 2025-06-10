<?php

declare(strict_types=1);

namespace App\Application\Actions\Person;

use App\Application\Actions\Action;
use App\Domain\Person\PersonRepository;
use Psr\Log\LoggerInterface;

abstract class PersonAction extends Action
{
    protected PersonRepository $personRepository;

    public function __construct(LoggerInterface $logger, personRepository $personRepository)
    {
        parent::__construct($logger);
        $this->personRepository = $personRepository;
    }
}