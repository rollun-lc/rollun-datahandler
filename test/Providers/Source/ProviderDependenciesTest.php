<?php

namespace rollun\test\datahandler\Provider\Source;

use PHPUnit\Framework\TestCase;
use rollun\datahandler\Providers\Source\ProviderDependencies;

class ProviderDependenciesTest extends TestCase
{
    private $providerDep;

    public function setUp()
    {
        $this->providerDep = new ProviderDependencies();
    }

    public function testProvidersMultiplie()
    {
        $this->providerDep->start('test', '1');
        $this->providerDep->start('test2', '2');
        $this->providerDep->finish('a');
        $this->providerDep->start('test3', '3');
        $this->providerDep->finish('b');
        $this->providerDep->finish('c');

        $this->assertEquals([
            'test2' => [
                '#2' => [
                    ProviderDependencies::spanHash(['provider' => 'test', 'id' => '1']) =>
                        ['provider' => 'test', 'id' => '1']
                ]
            ],
            'test3' => [
                '#3' => [
                    ProviderDependencies::spanHash(['provider' => 'test', 'id' => '1']) =>
                        ['provider' => 'test', 'id' => '1']
                ]
            ]
        ], $this->providerDep->depth());
    }

    public function testProvidersRemoveDepends()
    {
        $this->providerDep->start('test', '1');
        $this->providerDep->start('test2', '2');
        $this->providerDep->finish('a');
        $this->providerDep->start('test3', '3');
        $this->providerDep->finish('b');
        $this->providerDep->finish('c');

        $this->assertEquals([
            'test2' => [
                '#2' => [
                    ProviderDependencies::spanHash(['provider' => 'test', 'id' => '1']) =>
                        ['provider' => 'test', 'id' => '1']
                ]
            ],
            'test3' => [
                '#3' => [
                    ProviderDependencies::spanHash(['provider' => 'test', 'id' => '1']) =>
                        ['provider' => 'test', 'id' => '1']
                ]
            ]
        ], $this->providerDep->depth());

        $this->providerDep->start('test', '1');
        $this->providerDep->start('test3', '3');
        $this->providerDep->finish('b');
        $this->providerDep->finish('c');

        $this->assertEquals([
            'test2' => [
                '#2' => [

                ]
            ],
            'test3' => [
                '#3' => [
                    ProviderDependencies::spanHash(['provider' => 'test', 'id' => '1']) =>
                        ['provider' => 'test', 'id' => '1']
                ]
            ]
        ], $this->providerDep->depth());
    }
}
