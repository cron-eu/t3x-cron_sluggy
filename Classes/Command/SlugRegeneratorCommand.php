<?php
declare(strict_types=1);

namespace Cron\CronSluggy\Command;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Cron\CronSluggy\Service\SlugRegeneratorService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Regenerates the slugs in a specific page tree
 *
 * @package TYPO3\CMS\Lowlevel\Command
 */
class SlugRegeneratorCommand extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected $commandDescription = 'Slug regenerator';
    protected $commandHelp = 'Regenerates slugs on a page tree.';

    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription($this->commandDescription)
            ->setHelp($this->commandHelp)
            ->addOption('dry-run','-d', InputOption::VALUE_NONE, 'do not change anything')
            ->addOption('format','-f', InputOption::VALUE_REQUIRED, 'output format (csv, html, plain). plain is the default')
            ->addOption('redirects','-r', InputOption::VALUE_OPTIONAL, 'create redirects for changed slugs with this TTL in days', false)
            ->addArgument('root-page', InputArgument::REQUIRED)
            ->addArgument('language', InputArgument::OPTIONAL, '', 0)
        ;
    }

    /**
     * Start the CLI command "sluggy:regenerate"
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int 0 if everything went fine, or an exit code
     *
     * @throws SiteNotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = (bool)$input->getOption('dry-run');
        $outputFormat = $input->getOption('format') ?? 'plain';
        $redirectsOption = $input->getOption('redirects');
        if (false === $redirectsOption) {
            // option not passed
            $createRedirects = null;
        } elseif (null === $redirectsOption) {
            // option was passed, but no value was given
            $createRedirects = 30;
        } else {
            // option was passed, and value was given
            $createRedirects = (int)$redirectsOption;
        }
        $rootPage = $input->getArgument('root-page');
        $language = $input->getArgument('language');

        $migration = GeneralUtility::makeInstance(
            SlugRegeneratorService::class,
            $io,
            $this->logger,
            $dryRun,
            $createRedirects,
            $outputFormat
        );
        $migration->execute((int)$rootPage, (int)$language);

        return Command::SUCCESS;
    }


}
