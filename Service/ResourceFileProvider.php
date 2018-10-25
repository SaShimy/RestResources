<?php
namespace Simple\Component\RestResources\Service;

//use SimpleKernel;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

class ResourceFileProvider
{
    private $kernelRootDir;
    private $cacheDir;
    private $logger;

    /**
     * ResourceFileProvider constructor.
     *
     * @param ContainerInterface $container
     * @param LoggerInterface    $logger
     */
    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        $kernel = $container->get('kernel');
        $this->kernelRootDir = $kernel->getRootDir();
        $this->cacheDir = $kernel->getCacheDir();
        $this->logger = $logger;
    }

    /**
     * @param $method
     * @param $args
     *
     * @return null
     * @throws \Exception
     */
    public function __call($method, $args)
    {
        return array_key_exists($method, self::get($args[0])) ? self::get($args[0])[$method] : null;
    }

    /**
     * @param      $resource
     * @param bool $cache
     *
     * @return mixed
     * @throws \Exception
     */
    public function getFromResource($resource, $cache = false)
    {
        return $this->get($resource, 'resource', $cache);
    }

    /**
     * @param        $needle
     * @param string $from
     * @param bool   $cache
     *
     * @return mixed
     * @throws \Exception
     */
    public function get($needle, $from = 'class', $cache = false)
    {
        if ($cache)
        {
            if (is_file($cacheFile = $this->cacheDir . "/RestResources_{$from}_Cache.php"))
            {
                $cacheContent = require($cacheFile);
                if (isset($cacheContent[$from]))
                {
                    return $cacheContent[$from];
                }
                else
                {
                    $this->logger->info('Resource "' . $from . '" couldn\'t be found in cache.');
                }
            }
            else
            {
                $this->logger->info('Resources couldn\'t be loaded from cache.');
            }
        }

        $dir = $this->kernelRootDir . '/*/Resources/*';

        $parser = new Parser();
        $finder = new Finder();
        $finder->ignoreUnreadableDirs()
            ->in($dir)
            ->depth('== 0')
            ->files()
            ->name('*.resource.yml');

        foreach ($finder as $file)
        {
            $content = $parser->parse(file_get_contents($file->getRealPath()));
            if ($needle === $content[$from])
            {
                return $content;
            }
        }

        throw new \Exception(sprintf("No file found for class : %s", $needle));
    }

}