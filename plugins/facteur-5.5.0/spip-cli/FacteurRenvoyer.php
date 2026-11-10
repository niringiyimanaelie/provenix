<?php

use Spip\Cli\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;

class FacteurRenvoyer extends Command {
	protected function configure() {
		$this
			->setName('facteur:renvoyer')
			->setDescription('Renvoyer un mail en attente ou en echec')
			->addOption(
				'id',
				null,
				InputOption::VALUE_REQUIRED,
				'id du mail en attente ou en echec',
				null
			)
			->addUsage("--id=retry/2-1dddfe44513e17d66f30c4eb15f6ce01")
			->addUsage("--id=failed/1dddfe44513e17d66f30c4eb15f6ce01")
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		include_spip('inc/filtres');


		$mailid = $input->getOption('id');
		if (!$mailid) {
			$this->io->error("Indiquez un id mail a renvoyer");
			return self::FAILURE;
		}

		if (substr($mailid, -5) === '.json') {
			$mailid = substr($mailid, 0, -5);
		}

		$dir_tmp = _DIR_TMP . "facteur/";
		if (!file_exists($f = $dir_tmp . $mailid . ".json")) {
			$this->io->error("Aucun fichier $f");
			return self::FAILURE;
		}

		include_spip('inc/facteur');
		facteur_retry_envoyer_mail($mailid);

		passthru("tail -15 " . _DIR_TMP . "log/facteur.log");

		return self::SUCCESS;
	}
}
