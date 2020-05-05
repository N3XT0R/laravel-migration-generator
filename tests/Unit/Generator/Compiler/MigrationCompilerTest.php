<?php


namespace Tests\Unit\Generator\Compiler;


use Illuminate\Contracts\View\Factory as ViewFactory;
use N3XT0R\MigrationGenerator\Service\Generator\Compiler\MigrationCompiler;
use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

class MigrationCompilerTest extends TestCase
{
    protected $compiler;

    public function setUp(): void
    {
        parent::setUp();
        $this->compiler = new MigrationCompiler($this->app->make(ViewFactory::class), $this->app->make('files'));
    }

    public function testSetAndGetViewAreSame(): void
    {
        $actualView = $this->compiler->getView();
        $view = clone $actualView;
        $view->addLocation('/dev/null');

        $this->compiler->setView($view);
        $gotView = $this->compiler->getView();
        $this->assertSame($view, $gotView);
        $this->assertNotSame($gotView, $actualView);
    }

    public function testSetAndGetMapperAreSame(): void
    {
        $mapper = [uniqid('Test', true) => time()];
        $this->compiler->setMapper($mapper);
        $gotMapper = $this->compiler->getMapper();
        $this->assertSame($mapper, $gotMapper);
    }

    public function testSetAndGetRenderedTemplateAreSame(): void
    {
        $rendered = uniqid('template', true);
        $this->compiler->setRenderedTemplate($rendered);
        $gotRendered = $this->compiler->getRenderedTemplate();
        $this->assertSame($rendered, $gotRendered);
    }

    public function testSetAndGetFileSystemAreSame(): void
    {
        $filesystem = new Filesystem();
        $this->compiler->setFilesystem($filesystem);
        $got = $this->compiler->getFilesystem();
        $this->assertSame($filesystem, $got);
    }
}