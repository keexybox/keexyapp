<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Command;

use Composer\Config;
use Composer\Factory;
use Composer\Installer;
use Composer\Installer\ProjectInstaller;
use Composer\Installer\InstallationManager;
use Composer\Installer\SuggestedPackagesReporter;
use Composer\IO\IOInterface;
use Composer\Package\BasePackage;
use Composer\DependencyResolver\Pool;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\Package\Version\VersionSelector;
use Composer\Package\AliasPackage;
use Composer\Repository\RepositoryFactory;
use Composer\Repository\CompositeRepository;
use Composer\Repository\PlatformRepository;
use Composer\Repository\InstalledFilesystemRepository;
use Composer\Script\ScriptEvents;
use Composer\Util\Silencer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Composer\Json\JsonFile;
use Composer\Config\JsonConfigSource;
use Composer\Util\Filesystem;
use Composer\Package\Version\VersionParser;

/**
 * Install a package as new project into new directory.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Tobias Munk <schmunk@usrbin.de>
 * @author Nils Adermann <naderman@naderman.de>
 */
class CreateProjectCommand extends BaseCommand
{
    /**
     * @var SuggestedPackagesReporter
     */
    protected $suggestedPackagesReporter;

    protected function configure()
    {
        $this
            ->setName('create-project')
            ->setDescription('Creates new project from a package into given directory.')
            ->setDefinition(array(
                new InputArgument('package', InputArgument::OPTIONAL, 'Package name to be installed'),
                new InputArgument('directory', InputArgument::OPTIONAL, 'Directory where the files should be created'),
                new InputArgument('version', InputArgument::OPTIONAL, 'Version, will default to latest'),
                new InputOption('stability', 's', InputOption::VALUE_REQUIRED, 'Minimum-stability allowed (unless a version is specified).'),
                new InputOption('prefer-source', null, InputOption::VALUE_NONE, 'Forces installation from package sources when possible, including VCS information.'),
                new InputOption('prefer-dist', null, InputOption::VALUE_NONE, 'Forces installation from package dist even for dev versions.'),
                new InputOption('repository', null, InputOption::VALUE_REQUIRED, 'Pick a different repository (as url or json config) to look for the package.'),
                new InputOption('repository-url', null, InputOption::VALUE_REQUIRED, 'DEPRECATED: Use --repository instead.'),
                new InputOption('dev', null, InputOption::VALUE_NONE, 'Enables installation of require-dev packages (enabled by default, only present for BC).'),
                new InputOption('no-dev', null, InputOption::VALUE_NONE, 'Disables installation of require-dev packages.'),
                new InputOption('no-custom-installers', null, InputOption::VALUE_NONE, 'DEPRECATED: Use no-plugins instead.'),
                new InputOption('no-scripts', null, InputOption::VALUE_NONE, 'Whether to prevent execution of all defined scripts in the root package.'),
                new InputOption('no-progress', null, InputOption::VALUE_NONE, 'Do not output download progress.'),
                new InputOption('no-secure-http', null, InputOption::VALUE_NONE, 'Disable the secure-http config option temporarily while installing the root package. Use at your own risk. Using this flag is a bad idea.'),
                new InputOption('keep-vcs', null, InputOption::VALUE_NONE, 'Whether to prevent deleting the vcs folder.'),
                new InputOption('remove-vcs', null, InputOption::VALUE_NONE, 'Whether to force deletion of the vcs folder without prompting.'),
                new InputOption('no-install', null, InputOption::VALUE_NONE, 'Whether to skip installation of the package dependencies.'),
                new InputOption('ignore-platform-reqs', null, InputOption::VALUE_NONE, 'Ignore platform requirements (php & ext- packages).'),
            ))
            ->setHelp(
                <<<EOT
The <info>create-project</info> command creates a new project from a given
package into a new directory. If executed without params and in a directory
with a composer.json file it installs the packages for the current project.

You can use this command to bootstrap new projects or setup a clean
version-controlled installation for developers of your project.

<info>php composer.phar create-project vendor/project target-directory [version]</info>

You can also specify the version with the package name using = or : as separator.

<info>php composer.phar create-project vendor/project:version target-directory</info>

To install unstable packages, either specify the version you want, or use the
--stability=dev (where dev can be one of RC, beta, alpha or dev).

To setup a developer workable version you should create the project using the source
controlled code by appending the <info>'--prefer-source'</info> flag.

To install a package from another repository than the default one you
can pass the <info>'--repository=https://myrepository.org'</info> flag.

Read more at https://getcomposer.org/doc/03-cli.md#create-project
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = Factory::createConfig();
        $io = $this->getIO();

        list($preferSource, $preferDist) = $this->getPreferredInstallOptions($config, $input, true);

        if ($input->getOption('dev')) {
            $io->writeError('<warning>You are using the deprecated option "dev". Dev packages are installed by default now.</warning>');
        }
        if ($input->getOption('no-custom-installers')) {
            $io->writeError('<warning>You are using the deprecated option "no-custom-installers". Use "no-plugins" instead.</warning>');
            $input->setOption('no-plugins', true);
        }

        return $this->installProject(
            $io,
            $config,
            $input,
            $input->getArgument('package'),
            $input->getArgument('directory'),
            $input->getArgument('version'),
            $input->getOption('stability'),
            $preferSource,
            $preferDist,
            !$input->getOption('no-dev'),
            $input->getOption('repository') ?: $input->getOption('repository-url'),
            $input->getOption('no-plugins'),
            $input->getOption('no-scripts'),
            $input->getOption('no-progress'),
            $input->getOption('no-install'),
            $input->getOption('ignore-platform-reqs'),
            !$input->getOption('no-secure-http')
        );
    }

    public function installProject(IOInterface $io, Config $config, InputInterface $input, $packageName, $directory = null, $packageVersion = null, $stability = 'stable', $preferSource = false, $preferDist = false, $installDevPackages = false, $repository = null, $disablePlugins = false, $noScripts = false, $noProgress = false, $noInstall = false, $ignorePlatformReqs = false, $secureHttp = true)
    {
        $oldCwd = getcwd();

        // we need to manually load the configuration to pass the auth credentials to the io interface!
        $io->loadConfiguration($config);

        $this->suggestedPackagesReporter = new SuggestedPackagesReporter($io);

        if ($packageName !== null) {
            $installedFromVcs = $this->installRootPackage($io, $config, $packageName, $directory, $packageVersion, $stability, $preferSource, $preferDist, $installDevPackages, $repository, $disablePlugins, $noScripts, $noProgress, $ignorePlatformReqs, $secureHttp);
        } else {
            $installedFromVcs = false;
        }

        $composer = Factory::create($io, null, $disablePlugins);
        $composer->getDownloadManager()->setOutputProgress(!$noProgress);

        $fs = new Filesystem();

        if ($noScripts === false) {
            // dispatch event
            $composer->getEventDispatcher()->dispatchScript(ScriptEvents::POST_ROOT_PACKAGE_INSTALL, $installDevPackages);
        }

        // use the new config including the newly installed project
        $config = $composer->getConfig();
        list($preferSource, $preferDist) = $this->getPreferredInstallOptions($config, $input);

        // install dependencies of the created project
        if ($noInstall === false) {
            $installer = Installer::create($io, $composer);
            $installer->setPreferSource($preferSource)
                ->setPreferDist($preferDist)
                ->setDevMode($installDevPackages)
                ->setRunScripts(!$noScripts)
                ->setIgnorePlatformRequirements($ignorePlatformReqs)
                ->setSuggestedPackagesReporter($this->suggestedPackagesReporter)
                ->setOptimizeAutoloader($config->get('optimize-autoloader'))
                ->setClassMapAuthoritative($config->get('classmap-authoritative'))
                ->setApcuAutoloader($config->get('apcu-autoloader'));

            if ($disablePlugins) {
                $installer->disablePlugins();
            }

            $status = $installer->run();
            if (0 !== $status) {
                return $status;
            }
        }

        $hasVcs = $installedFromVcs;
        if (
            !$input->getOption('keep-vcs')
            && $installedFromVcs
            && (
                $input->getOption('remove-vcs')
                || !$io->isInteractive()
                || $io->askConfirmation('<info>Do you want to remove the existing VCS (.git, .svn..) history?</info> [<comment>Y,n</comment>]? ', true)
            )
        ) {
            $finder = new Finder();
            $finder->depth(0)->directories()->in(getcwd())->ignoreVCS(false)->ignoreDotFiles(false);
            foreach (array('.svn', '_svn', 'CVS', '_darcs', '.arch-params', '.monotone', '.bzr', '.git', '.hg', '.fslckout', '_FOSSIL_') as $vcsName) {
                $finder->name($vcsName);
            }

            try {
                $dirs = iterator_to_array($finder);
                unset($finder);
                foreach ($dirs as $dir) {
                    if (!$fs->removeDirectory($dir)) {
                        throw new \RuntimeException('Could not remove '.$dir);
                    }
                }
            } catch (\Exception $e) {
                $io->writeError('<error>An error occurred while removing the VCS metadata: '.$e->getMessage().'</error>');
            }

            $hasVcs = false;
        }

        // rewriting self.version dependencies with explicit version numbers if the package's vcs metadata is gone
        if (!$hasVcs) {
            $package = $composer->getPackage();
            $configSource = new JsonConfigSource(new JsonFile('composer.json'));
            foreach (BasePackage::$supportedLinkTypes as $type => $meta) {
                foreach ($package->{'get'.$meta['method']}() as $link) {
                    if ($link->getPrettyConstraint() === 'self.version') {
                        $configSource->addLink($type, $link->getTarget(), $package->getPrettyVersion());
                    }
                }
            }
        }

        if ($noScripts === false) {
            // dispatch event
            $composer->getEventDispatcher()->dispatchScript(ScriptEvents::POST_CREATE_PROJECT_CMD, $installDevPackages);
        }

        chdir($oldCwd);
        $vendorComposerDir = $config->get('vendor-dir').'/composer';
        if (is_dir($vendorComposerDir) && $fs->isDirEmpty($vendorComposerDir)) {
            Silencer::call('rmdir', $vendorComposerDir);
            $vendorDir = $config->get('vendor-dir');
            if (is_dir($vendorDir) && $fs->isDirEmpty($vendorDir)) {
                Silencer::call('rmdir', $vendorDir);
            }
        }

        return 0;
    }

    protected function installRootPackage(IOInterface $io, Config $config, $packageName, $directory = null, $packageVersion = null, $stability = 'stable', $preferSource = false, $preferDist = false, $installDevPackages = false, $repository = null, $disablePlugins = false, $noScripts = false, $noProgress = false, $ignorePlatformReqs = false, $secureHttp = true)
    {
        if (!$secureHttp) {
            $config->merge(array('config' => array('secure-http' => false)));
        }

        if (null === $repository) {
            $sourceRepo = new CompositeRepository(RepositoryFactory::defaultRepos($io, $config));
        } else {
            $sourceRepo = RepositoryFactory::fromString($io, $config, $repository, true);
        }

        $parser = new VersionParser();
        $requirements = $parser->parseNameVersionPairs(array($packageName));
        $name = strtolower($requirements[0]['name']);
        if (!$packageVersion && isset($requirements[0]['version'])) {
            $packageVersion = $requirements[0]['version'];
        }

        if (null === $stability) {
            if (preg_match('{^[^,\s]*?@('.implode('|', array_keys(BasePackage::$stabilities)).')$}i', $packageVersion, $match)) {
                $stability = $match[1];
            } else {
                $stability = VersionParser::parseStability($packageVersion);
            }
        }

        $stability = VersionParser::normalizeStability($stability);

        if (!isset(BasePackage::$stabilities[$stability])) {
            throw new \InvalidArgumentException('Invalid stability provided ('.$stability.'), must be one of: '.implode(', ', array_keys(BasePackage::$stabilities)));
        }

        $pool = new Pool($stability);
        $pool->addRepository($sourceRepo);

        $phpVersion = null;
        $prettyPhpVersion = null;
        if (!$ignorePlatformReqs) {
            $platformOverrides = $config->get('platform') ?: array();
            // initialize $this->repos as it is used by the parent InitCommand
            $platform = new PlatformRepository(array(), $platformOverrides);
            $phpPackage = $platform->findPackage('php', '*');
            $phpVersion = $phpPackage->getVersion();
            $prettyPhpVersion = $phpPackage->getPrettyVersion();
        }

        // find the latest version if there are multiple
        $versionSelector = new VersionSelector($pool);
        $package = $versionSelector->findBestCandidate($name, $packageVersion, $phpVersion, $stability);

        if (!$package) {
            $errorMessage = "Could not find package $name with " . ($packageVersion ? "version $packageVersion" : "stability $stability");
            if ($phpVersion && $versionSelector->findBestCandidate($name, $packageVersion, null, $stability)) {
                throw new \InvalidArgumentException($errorMessage .' in a version installable using your PHP version '.$prettyPhpVersion.'.');
            }

            throw new \InvalidArgumentException($errorMessage .'.');
        }

        if (null === $directory) {
            $parts = explode("/", $name, 2);
            $directory = getcwd() . DIRECTORY_SEPARATOR . array_pop($parts);
        }

        // handler Ctrl+C for unix-like systems
        if (function_exists('pcntl_async_signals')) {
            @mkdir($directory, 0777, true);
            if ($realDir = realpath($directory)) {
                pcntl_async_signals(true);
                pcntl_signal(SIGINT, function () use ($realDir) {
                    $fs = new Filesystem();
                    $fs->removeDirectory($realDir);
                    exit(130);
                });
            }
        }

        $io->writeError('<info>Installing ' . $package->getName() . ' (' . $package->getFullPrettyVersion(false) . ')</info>');

        if ($disablePlugins) {
            $io->writeError('<info>Plugins have been disabled.</info>');
        }

        if ($package instanceof AliasPackage) {
            $package = $package->getAliasOf();
        }

        $dm = $this->createDownloadManager($io, $config);
        $dm->setPreferSource($preferSource)
            ->setPreferDist($preferDist)
            ->setOutputProgress(!$noProgress);

        $projectInstaller = new ProjectInstaller($directory, $dm);
        $im = $this->createInstallationManager();
        $im->addInstaller($projectInstaller);
        $im->install(new InstalledFilesystemRepository(new JsonFile('php://memory')), new InstallOperation($package));
        $im->notifyInstalls($io);

        // collect suggestions
        $this->suggestedPackagesReporter->addSuggestionsFromPackage($package);

        $installedFromVcs = 'source' === $package->getInstallationSource();

        $io->writeError('<info>Created project in ' . $directory . '</info>');
        chdir($directory);

        $_SERVER['COMPOSER_ROOT_VERSION'] = $package->getPrettyVersion();
        putenv('COMPOSER_ROOT_VERSION='.$_SERVER['COMPOSER_ROOT_VERSION']);

        return $installedFromVcs;
    }

    protected function createDownloadManager(IOInterface $io, Config $config)
    {
        $factory = new Factory();

        return $factory->createDownloadManager($io, $config);
    }

    protected function createInstallationManager()
    {
        return new InstallationManager();
    }
}
