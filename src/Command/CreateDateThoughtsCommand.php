<?php

namespace App\Command;

use App\Exceptions\TheBrainException;
use App\Services\TheBrainDateService;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'date:create',
    description: 'Создаёт мысли типа Дата',
)]
class CreateDateThoughtsCommand extends Command
{
    public function __construct(
        private readonly TheBrainDateService $theBrainDateService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'parent',
                InputArgument::REQUIRED,
                'Идентификатор ноды родителя'
            )
            ->addOption(
                'month',
                'm',
                InputOption::VALUE_REQUIRED,
                'Номер месяца за который надо создать'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $month = filter_var($input->getOption('month') ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $parentNodeId = $input->getArgument('parent');

        $io = new SymfonyStyle($input, $output);

        if (null === $parentNodeId)

        if (0 !== $month && ($month < 1 || $month > 12)) {
            $io->error('Номер месяца должен быть в диапазоне от 1 до 12');

            return Command::FAILURE;
        }

        if ($month === 0) {
            $month = date_create()->format('n');
        }

        $year = date_create()->format('Y');

        try {
            $this->theBrainDateService->createDateThoughts($month, $year, $parentNodeId);
        } catch (TheBrainException | GuzzleException $e) {
            $io->error($e->getMessage());
        }

        return Command::SUCCESS;
    }
}
