<?php

namespace PHPlexus\Tests\Core\Entity;

use PHPlexus\Core\Entity\Model;
use PHPlexus\Core\Entity\Record;
use PHPlexus\Core\Entity\EntityBuilder;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    public function testModelConstructor()
    {
        $model = new class (1, 'Alice') extends Model {
            private int $id;
            private string $name;

            public function getId(): int
            {
                return $this->id;
            }

            public function getName(): string
            {
                return $this->name;
            }

            public function setName(string $name): void
            {
                $this->name = $name;
            }
        };

        $this->assertEquals(1, $model->getId());
        $this->assertEquals('Alice', $model->getName());
    }

    public function testModelMethods()
    {
        $model = new class (1, 'Alice') extends Model {
            private int $id;
            private string $name;

            public function getId(): int
            {
                return $this->id;
            }

            public function getName(): string
            {
                return $this->name;
            }

            public function setName(string $name): void
            {
                $this->name = $name;
            }
        };

        $model->setName('Bob');
        $this->assertEquals('Bob', $model->getName());
    }

    public function testRecordConstructor()
    {
        $record = new class (1, 'Alice') extends Record {
            private int $id;
            private string $name;

            public function getId(): int
            {
                return $this->id;
            }

            public function getName(): string
            {
                return $this->name;
            }
        };

        $this->assertEquals(1, $record->getId());
        $this->assertEquals('Alice', $record->getName());
    }

    public function testEntityBuilder()
    {
        $builder = new class extends EntityBuilder {
            public function build(): Model
            {
                return new class ($this->attributes) extends Model {
                    private int $id;
                    private string $name;

                    public function getId(): int
                    {
                        return $this->id;
                    }

                    public function getName(): string
                    {
                        return $this->name;
                    }

                    public function setName(string $name): void
                    {
                        $this->name = $name;
                    }
                };
            }
        };

        $entity = $builder->set('id', 1)->set('name', 'Alice')->build();

        $this->assertEquals(1, $entity->getId());
        $this->assertEquals('Alice', $entity->getName());
    }

    public function testRecordWithMethod()
    {
        $record = new class (1, 'Alice') extends Record {
            private int $id;
            private string $name;

            public function getId(): int
            {
                return $this->id;
            }

            public function getName(): string
            {
                return $this->name;
            }
        };

        $modifiedRecord = $record->with(['name' => 'Bob']);

        $this->assertEquals('Alice', $record->getName());
        $this->assertEquals('Bob', $modifiedRecord->getName());
    }

}