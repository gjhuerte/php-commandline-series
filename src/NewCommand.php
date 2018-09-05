<?php

namespace Acme;

use ZipArchive;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends Command {

	private $client;

	public function __construct(ClientInterface $client)
	{
		$this->client = $client;

		parent::__construct();
	}
	
	/**
	 * Configure the command options
	 * 
	 * @return void
	 */
	public function configure()
	{
		$this->setName('new')
			 ->setDescription('Create a new Laravel application')
			 ->addArgument('name', InputArgument::REQUIRED);
	}

	/**
	 * Execute the command
	 * 
	 * @param  InputInterface  $input 
	 * @param  OutputInterface $output
	 * @return void
	 */
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$directory = getcwd() . '/' . $input->getArgument('name');
		$this->assertApplicationDoesNotExist($directory, $output);
		
		$this->download($zipFile = $this->makeFileName())
			->extract($zipFile, $directory)
			->cleanUp($zipFile);

		$output->writeln('<comment>Application ready!!</comment>');       
	}

	private function assertApplicationDoesNotExist($directory, OutputInterface $output)
	{
		if (is_dir($directory))
		{
			$output->writeln('<error>Application already exists!</error>');

			exit(1);
		}
	}

	private function makeFileName()
	{
		return getcwd() . '/laravel_' .md5(time().uniqid()) . '.zip' ;
	}

	private function download($zipfile)
	{
		$response = $this->client->get('http://cabinet.laravel.com/latest.zip')->getBody();
		file_put_contents($zipfile, $response);

		return $this;
	}

	public function extract($zipFile, $directory)
	{
		$archive = new ZipArchive;
		$archive->open($zipFile);
		$archive->extractTo($directory);
		$archive->close();

		return $this;
	}

	private function cleanUp($zipFile)
	{
		@chmod($zipFile, 0777);
		@unlink($zipFile);

		return $this;
	}
}