<?php
namespace bookin\composer\web;

use Composer\Console\Application;
use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Repository\PlatformRepository;
use Composer\Repository\RepositoryInterface;
use Composer\Repository\RepositoryManager;
use Symfony\Component\Console\Input\ArrayInput;
use yii\base\Component;
use yii\base\InvalidParamException;
use Yii;

class ComposerComponent extends Component
{
    public static $composerConfigFile = '@app/composer.json';
    public static $composerConfigFilePath = '@app';

    public static $composer;

    /**
     * @return \Composer\Composer
     */
    public static function getComposer(){
        if(!self::$composer){
            $factory = new Factory();
            self::$composer = $factory->createComposer(new NullIO(), Yii::getAlias(self::$composerConfigFile), false, Yii::getAlias(self::$composerConfigFilePath));
        }
        return self::$composer;
    }

    /**
     * @return \Composer\Package\PackageInterface[]
     */
    public static function getLocalPackages(){
        return self::getComposer()->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();
    }

    /**
     * @param $name
     * @param array $options
     * @return string
     */
    public static function updatePackage($name, $options=[]){
        return self::runCommand('update', [$name]+$options);
    }

    /**
     * @param array $options
     * @return string
     */
    public static function updateAllPackages($options=[]){
        return self::runCommand('update', $options);
    }

    /**
     * @param array $options
     * @return string
     */
    public static function deleteAllPackages($options=[]){
        return self::runCommand('remove', $options);
    }

    /**
     * @param $name
     * @param array $options
     * @return string
     */
    public static function deletePackage($name, $options=[]){
        return self::runCommand('remove', [$name]+$options);
    }



    public static function searchPackage($search){
        /* @var $app Application */
        $app = self::getApplication();
        $composer = $app->getComposer(true, false);
        $platformRepo = new PlatformRepository();
        $localRepo = $composer->getRepositoryManager()->getLocalRepository();
        $installedRepo = new CompositeRepository(array($localRepo, $platformRepo));
        $repos = new CompositeRepository(array_merge(array($installedRepo), $composer->getRepositoryManager()->getRepositories()));
        $flags = RepositoryInterface::SEARCH_FULLTEXT;
        $results = $repos->search($search, $flags);

        return $results;
    }

    public static function findPackage($name, $version=null)
    {
        if (strpos($name, '/') === false) {
            throw new InvalidParamException('You need use full package name: vendor/vendor1');
        }
        /** @var RepositoryManager $repositoriManager */
        $repositoryManager = self::getComposer()->getRepositoryManager();
        $package = $repositoryManager->findPackage($name, $version);
        return $package;
    }


    public static function getApplication(){
        $app = new WebApplication();
        $app->setComposer(self::getComposer());
        $app->setAutoExit(false);
        return $app;
    }

    /**
     * @param string $command
     * @param array $params
     * @return string
     */
    public static function runCommand($command='', $params=[]){

        if(empty($command)){
            $command='list';
        }

        $parameters = ['command'=>$command]+$params;

        $input = new ArrayInput($parameters);
        $output = new ComposerOutput();

        $output->setFormatter(new BootstrapOutputFormatter());

        try {
            $app = self::getApplication();
            $app->run($input, $output);
        }catch (\Exception $c){
            $output->write($c->getMessage());
        }

        return $output->getMessage();
    }
}