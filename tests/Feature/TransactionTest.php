<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from categories');
    }

    public function testTransactionSuccess()
    {
        DB::transaction(function () {

            DB::insert('INSERT INTO categories(id, name, description, created_at) values (?,?,?,?)', [
                "GADGET", "Gadget", "Gadget Category", "2020-10-10 10:10:10"
            ]);

            DB::insert('INSERT INTO categories(id, name, description, created_at) values (?,?,?,?)', [
                "FOOD", "Food", "Food Category", "2020-10-10 10:10:10"
            ]);
        });

        $result = DB::select("select * FROM categories");
        self::assertCount(2, $result);
    }
    public function testTransactionFailed()
    {

        try {
            DB::transaction(function () {
                DB::insert('INSERT INTO categories(id, name, description, created_at) values (?,?,?,?)', [
                    "GADGET", "Gadget", "Gadget Category", "2020-10-10 10:10:10"
                ]);

                DB::insert('INSERT INTO categories(id, name, description, created_at) values (?,?,?,?)', [
                    "GADGET", "Food", "Food Category", "2020-10-10 10:10:10"
                ]);
            });
        } catch (QueryException $error) {
            // expected
        }

        $results = DB::select("select * FROM categories");
        self::assertCount(0, $results);
    }

    public function testmanualTransactionSuccess()
    {

        try {
            DB::beginTransaction();

            DB::insert('INSERT INTO categories(id, name, description, created_at) values (?,?,?,?)', [
                "GADGET", "Gadget", "Gadget Category", "2020-10-10 10:10:10"
            ]);

            DB::insert('INSERT INTO categories(id, name, description, created_at) values (?,?,?,?)', [
                "FOOD", "Food", "Food Category", "2020-10-10 10:10:10"
            ]);
            DB::commit();
        } catch (QueryException $error) {
            DB::rollBack();
        }

        $results = DB::select("select * FROM categories");
        self::assertCount(2, $results);
    }

    public function testManualTransactionFailed()
    {

        try {
            DB::beginTransaction();
            DB::insert('INSERT INTO categories(id, name, description, created_at) values (?,?,?,?)', [
                "GADGET", "Gadget", "Gadget Category", "2020-10-10 10:10:10"
            ]);
            DB::insert('INSERT INTO categories(id, name, description, created_at) values (?,?,?,?)', [
                "GADGET", "Food", "Food Category", "2020-10-10 10:10:10"
            ]);
            DB::commit();
        } catch (QueryException $error) {
            DB::rollBack();
        }

        $results = DB::select("select * FROM categories");
        self::assertCount(0, $results);
    }
}
