<?php

namespace Acme;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowCommands extends Command {
	
	public function configure()
	{
		$this->setName('show')
			 ->setDescription('Show all tasks.');
	}
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$this->showTasks($output); 
	}
}