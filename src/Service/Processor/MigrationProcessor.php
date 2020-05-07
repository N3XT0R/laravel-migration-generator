<?php


namespace N3XT0R\MigrationGenerator\Service\Processor;


use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserInterface;
use Illuminate\Database\Migrations\Migrator;

class MigrationProcessor implements MigrationProcessorInterface
{
    protected $messages = [];
    protected $schemaParser;
    protected $migrator;

    public function __construct(SchemaParserInterface $schemaParser, Migrator $migrator)
    {
        $this->setSchemaParser($schemaParser);
        $this->setMigrator($migrator);
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param array $messages
     */
    public function setMessages(array $messages): void
    {
        $this->messages = $messages;
    }

    public function addMessage(string $type, string $message): void
    {
        $this->messages[$type][] = $message;
    }

    /**
     * @return SchemaParserInterface
     */
    public function getSchemaParser(): SchemaParserInterface
    {
        return $this->schemaParser;
    }

    /**
     * @param SchemaParserInterface $schemaParser
     */
    public function setSchemaParser(SchemaParserInterface $schemaParser): void
    {
        $this->schemaParser = $schemaParser;
    }

    public function getMessagesByType(string $type): array
    {
        $result = [];
        if (array_key_exists($type, $this->getMessages())) {
            $result = $this->getMessages()[$type];
        }

        return $result;
    }

    public function hasMessagesForType(string $type): bool
    {
        return 0 !== count($this->getMessagesByType($type));
    }

    public function setMigrator(Migrator $migrator): void
    {
        $this->migrator = $migrator;
    }

    public function getMigrator(): Migrator
    {
        return $this->migrator;
    }

    public function run(array $options): void
    {
    }
}