<?php

namespace AppBundle\Twig;

use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Environment;

/**
 * Class GruntAssetExtension
 */
class GruntAssetExtension extends \Twig_Extension
{
    protected $assets;
    protected $twig;
    protected $container;
    protected $path;



    /**
     * @param ContainerInterface $container
     * @param Twig_Environment   $twig
     */
    public function __construct(ContainerInterface $container, Twig_Environment $twig)
    {
        $this->assets = [];
        $this->twig = $twig;
        $this->path = $container->getParameter('grunt_assets_path');


        if (file_exists($this->path)) {
            $data = file_get_contents($this->path);
            $assets = @json_decode($data, true);
            if (is_array($assets)) {
                foreach ($assets as $asset) {
                    if (file_exists($asset['versionedPath'])) {
                        $this->assets[$asset['originalPath']] = $asset;
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('grunt_asset', array($this, 'gruntAsset')),
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'grunt_asset_extension';
    }

    /**
     * @param string $string
     *
     * @return string
     *
     * @throws \Twig_Error_Runtime
     */
    public function gruntAsset($string)
    {
        $string = ltrim($string, '/');
        if (array_key_exists($string, $this->assets)) {
            $string = $this->assets[$string]['versionedPath'];
        }

        /** @var AssetExtension $assetExtension */
        $assetExtension = $this->twig->getExtension(AssetExtension::class);

        return $assetExtension->getAssetUrl($string);
    }
}
