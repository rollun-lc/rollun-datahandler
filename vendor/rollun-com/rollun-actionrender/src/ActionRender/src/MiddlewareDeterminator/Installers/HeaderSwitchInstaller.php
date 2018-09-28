<?php
/**
 * Created by PhpStorm.
 * User: victorsecuring
 * Date: 22.03.18
 * Time: 4:08 PM
 */

namespace rollun\actionrender\MiddlewareDeterminator\Installers;

use rollun\installer\Install\InstallerAbstract;
use rollun\actionrender\MiddlewareDeterminator\Factory\HeaderSwitchAbstractFactory;

class HeaderSwitchInstaller extends InstallerAbstract
{
    /**
     * install
     * @return array
     */
    public function install()
    {
        return [
            'dependencies' => [
                'abstract_factories' => [
                    HeaderSwitchAbstractFactory::class,
                ],
            ],
        ];
    }

    /**
     * Clean all installation
     * @return void
     */
    public function uninstall()
    {

    }

    /**
     * Return string with description of installable functional.
     * @param string $lang ; set select language for description getted.
     * @return string
     */
    public function getDescription($lang = "en")
    {
        switch ($lang) {
            case "ru":
                $description = "Позволяет создавать SwitchMiddlewareDeterminator на основани заголовка запроса";
                break;
            default:
                $description = "Does not exist.";
        }
        return $description;
    }

    public function isInstall()
    {
        $config = $this->container->get('config');
        return (
            isset($config['dependencies']['abstract_factories']) &&
            in_array(HeaderSwitchAbstractFactory::class, $config['dependencies']['abstract_factories'])
        );
    }
}