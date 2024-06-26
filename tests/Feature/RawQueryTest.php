<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RawQueryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from categories');
    }
    public function testCrud()
    {
        DB::insert('INSERT INTO categories(id, name, description, created_at) values (?,?,?,?)', ["GADGET", "Gadget", "Gadget Category", "2020-10-10 10:10:10"]);

        $result =  DB::select(' SELECT * FROM categories WHERE id = ?', ['GADGET']);

        self::assertCount(1, $result);
        self::assertEquals('GADGET', $result[0]->id);
        self::assertEquals('Gadget', $result[0]->name);
        self::assertEquals('Gadget Category', $result[0]->description);
        self::assertEquals('2020-10-10 10:10:10', $result[0]->created_at);
    }

    public function testCrudNamedParameter()
    {
        DB::insert('INSERT INTO categories(id, name, description, created_at) values (:id, :name, :description, :created_at)', [
            "id" => 'GADGET',
            "name" => 'Gadget',
            "description" => 'Gadget Category',
            "created_at" => '2020-10-10 10:10:10',
        ]);

        $result =  DB::select(' SELECT * FROM categories WHERE id = ?', ['GADGET']);

        self::assertCount(1, $result);
        self::assertEquals('GADGET', $result[0]->id);
        self::assertEquals('Gadget', $result[0]->name);
        self::assertEquals('Gadget Category', $result[0]->description);
        self::assertEquals('2020-10-10 10:10:10', $result[0]->created_at);
    }
}
