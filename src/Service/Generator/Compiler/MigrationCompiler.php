<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Compiler;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\PsrPrinter;
use Nette\PhpGenerator\Method;

class MigrationCompiler
{

    protected $namespace;
    protected $name = '';


    public function setNamespace(PhpNamespace $namespace): void
    {
        $this->namespace = $namespace;
    }

    public function getNamespace(): PhpNamespace
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }


    public function initializeMigration(string $name, string $customMigrationClass = ''): void
    {
        $className = Migration::class;
        if (!empty($customMigrationClass) && class_exists($customMigrationClass)) {
            $className = $customMigrationClass;
        }

        $namespace = new PhpNamespace('Database\\Migration');
        $namespace->addUse($className);
        $namespace->addUse(Blueprint::class);

        $class = new ClassType($name);
        $class->addExtend($className);
        $upMethod = $class->addMethod('up');
        $upMethod->setPublic();
        $upMethod->setReturnType('void');


        $downMethod = $class->addMethod('down');
        $downMethod->setPublic();
        $downMethod->setReturnType('void');

        $namespace->add($class);

        $this->setNamespace($namespace);
        $this->setName($name);
    }

    public function createByResult(ResultEntity $entity): void
    {
        $namespace = $this->getNamespace();
        $class = $namespace->getClasses()[$this->getName()];

        $this->addCreateTable($class->getMethod('up'), $entity->getResultByKey('table'));
    }


    protected function addCreateTable(Method $method, array $data): void
    {
        $body = '
            Schema::table();
        ';

        $method->setBody($body);
    }


    public function getContent(): string
    {
        $printer = new PsrPrinter();
        $class = $this->getNamespace();

        $content = $printer->printNamespace($class);
        $content = str_replace('namespace Database\Migration;', '<?php', $content);

        return $content;
    }
}