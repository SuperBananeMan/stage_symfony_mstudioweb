<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\VinylMixRepository;

#[AsCommand(
    name: 'app:talk-to-me',
    description: 'Scout',
)]
class TalkToMeCommand extends Command
{
	public function __construct(
        private VinylMixRepository $mixRepository
    )
    {
        parent::__construct();
    }
	
    protected function configure(): void
    {
        $this
            ->addArgument('man', InputArgument::OPTIONAL, 'I just wanna talk to him.')
            ->addOption('bonk', null, InputOption::VALUE_NONE, 'You thought fast enough.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $man = $input->getArgument('man');
		$lebonk = $input->getOption('bonk');
		
		$message = sprintf('Think fast chucklenuts !');
		
        if ($man) {
            $message = sprintf('What ? You wanna talk ? Wait until I hit your head BONK !');
        }

        if ($lebonk) {
            $message = sprintf('Hey ! What are you- AAAAAAA !');
        }
		
		$io->success($message);

        return Command::SUCCESS;
    }
}
