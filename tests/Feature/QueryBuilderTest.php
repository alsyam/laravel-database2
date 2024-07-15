<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\CounterSeeder;
use Illuminate\Support\Facades\Log;
use Database\Seeders\CategorySeeder;
use illuminate\Database\Query\Builder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QueryBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete("delete from categories");
        DB::delete("delete from counters");
        DB::delete("delete from products");
    }

    public function testInsert()
    {
        DB::table("categories")->insert([
            "id" => "GADGET",
            "name" => "Gadget",
            "description" => "Description for Gadget",
            "created_at" => "2020-10-10 10:10:10"
        ]);
        DB::table("categories")->insert([
            "id" => "FOOD",
            "name" => "Food",
            "description" => "Description for Food",
            "created_at" => "2020-10-10 10:10:10"
        ]);

        $result = DB::select("SELECT count(id) as total from categories");
        self::assertEquals(2, $result[0]->total);
    }

    public function testSelect()
    {
        $this->testInsert();

        $collection = DB::table("categories")->select(["id", "name"])->get();
        self::assertNotNull($collection);

        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function insertCategories()
    {
        $this->seed(CategorySeeder::class);

        // DB::table("categories")->insert([
        //     "id" => "SMARTPHONE",
        //     "name" => "Smartphone",
        //     "description" => "Description for Smartphone",
        //     "created_at" => "2020-10-10 10:10:10"
        // ]);
        // DB::table("categories")->insert([
        //     "id" => "FOOD",
        //     "name" => "Food",
        //     "description" => "Description for Food",
        //     "created_at" => "2020-10-10 10:10:10"
        // ]);
        // DB::table("categories")->insert([
        //     "id" => "FASHION",
        //     "name" => "Fashion",
        //     "description" => "Description for Fashion",
        //     "created_at" => "2020-10-10 10:10:10"
        // ]);
        // DB::table("categories")->insert([
        //     "id" => "LAPTOP",
        //     "name" => "Laptop",
        //     "description" => "Description for Laptop",
        //     "created_at" => "2020-10-10 10:10:10"
        // ]);
    }
    public function testWhere()
    {

        $this->insertCategories();

        $collection = DB::table("categories")->orWhere(function (Builder $builder) {
            $builder->where('id', '=', 'SMARTPHONE');
            $builder->orWhere('id', '=', 'LAPTOP');
            // select * from categories where (id = smartphone OR id = laptop)
        })->get();

        self::assertCount(2, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
        // for ($i = 0; $i < count($collection); $i++) {
        //     Log::info(json_encode($collection[$i]));
        // }

    }
    public function testWhereBetween()
    {
        $this->insertCategories();

        $collection = DB::table("categories")->whereBetween("created_at", ["2020-9-10 10:10:10", "2020-11-10 10:10:10"])->get();

        self::assertCount(4, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereIn()
    {
        $this->insertCategories();

        $collection = DB::table("categories")->whereIn("id", ["LAPTOP", "FASHION"])->get();

        self::assertCount(2, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereNotNullANdNull()
    {
        $this->insertCategories();

        $collection = DB::table("categories")->whereNotNull("description")->get();

        self::assertCount(4, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });

        $collection = DB::table("categories")->whereNull("description")->get();

        self::assertCount(0, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereDate()
    {
        $this->insertCategories();

        $collection = DB::table("categories")->whereDate("created_at", ["2020-10-10"])->get();

        self::assertCount(4, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereUpdate()
    {
        $this->insertCategories();

        DB::table("categories")->where("id", "=", "SMARTPHONE")->update([
            "id" => "HP"
        ]);


        $collection = DB::table("categories")->where("id", ["HP"])->get();
        self::assertCount(1, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }
    public function testUpsert()
    {
        $this->insertCategories();


        DB::table("categories")->updateOrInsert([
            "id" => "VOUCHER"
        ], [
            "name" => "Voucher",
            "description" => "Description for Voucher",
            "created_at" => "2020-10-10 10:10:10"
        ]);


        $collection = DB::table("categories")
            // ->where("id", "=", "VOUCHER")
            ->get();
        self::assertCount(5, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testIncrement()
    {

        $this->seed(CounterSeeder::class);
        DB::table("counters")->where('id', '=', 'sample')->increment('counter', 1);


        $collection = DB::table("counters")
            ->where("id", "=", "sample")
            ->get();
        self::assertCount(1, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testDelete()
    {
        $this->insertCategories();


        DB::table("categories")->where('id', '=', 'smartphone')->delete();



        $collection = DB::table("categories")
            ->where("id", "=", "smartphone")
            ->get();
        self::assertCount(0, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function insertProducts()
    {
        $this->insertCategories();

        DB::table('products')->insert([
            [
                "id" => "1",
                "name" => "Iphone 15",
                // "description" => "1",
                "price" => 2000000,
                "category_id" => "SMARTPHONE",
                "created_at" => "2020-10-10 10:10:10"

            ], [
                "id" => "2",
                "name" => "VIVO KEREN",
                // "description" => "1",
                "price" => 1000000,
                "category_id" => "SMARTPHONE",
                "created_at" => "2020-10-10 10:10:10"
            ]
        ]);
    }

    public function testJoin()
    {

        // // Hapus semua produk terkait terlebih dahulu
        // DB::table('products')->where('category_id', 1)->delete();

        // // Sekarang hapus kategori
        // DB::table('categories')->where('id', 1)->delete();

        // // Tambahkan asersi yang sesuai di sini
        // $this->assertTrue(true);


        $this->insertProducts();

        $collection = DB::table("products")
            ->join("categories", "products.category_id", '=', "categories.id")
            ->select("products.id", "products.name", "products.price", "categories.name as category_name")
            ->get();

        self::assertCount(2, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testOrdering()
    {

        $this->insertProducts();

        $collection = DB::table("products")
            ->whereNotNull("id")
            ->orderBy("price", "desc")
            ->orderBy("name", "asc")
            ->get();

        self::assertCount(2, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }
    public function testPaging()
    {

        $this->insertCategories();

        $collection = DB::table("categories")
            ->skip(0)
            ->take(2)
            ->get();

        self::assertCount(2, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function insertManyCategories()
    {
        for ($i = 0; $i < 100; $i++) {
            DB::table('categories')->insert(
                [
                    "id" => "CATEGORY - $i",
                    "name" => "Categori $i",
                    "created_at" => "2020-10-10 10:10:10"
                ]
            );
        }
    }

    public function testChunk()
    {
        $this->insertManyCategories();

        DB::table("categories")->orderBy("id")
            ->chunk(10, function ($categories) {
                self::assertNotNull($categories);
                Log::info("Start Chunk");
                $categories->each(function ($category) {
                    Log::info(json_encode($category));
                });
                Log::info("End Chunk");
            });
    }

    public function testLazy()
    {
        $this->insertManyCategories();

        $collection = DB::table("categories")->orderBy("id")
            ->lazy(10)->take(2);
        self::assertNotNull($collection);

        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testCursor()
    {
        $this->insertManyCategories();

        $collection = DB::table("categories")->orderBy("id")
            ->cursor();
        self::assertNotNull($collection);

        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testAggregat()
    {
        $this->insertProducts();

        $result = DB::table("products")->count("id");
        self::assertEquals(2, $result);

        $result = DB::table("products")->min("price");
        self::assertEquals(1000000, $result);

        $result = DB::table("products")->max("price");
        self::assertEquals(2000000, $result);

        $result = DB::table("products")->avg("price");
        self::assertEquals(1500000, $result);

        $result = DB::table("products")->sum("price");
        self::assertEquals(3000000, $result);
    }

    public function testQueryBuilderRaw()
    {
        $this->insertProducts();

        $collection = DB::table('products')
            ->select(
                DB::raw("count(id) as total_product"),
                DB::raw("max(price) as max_price"),
                DB::raw("min(price) as min_price"),
            )->get();

        self::assertEquals(2, $collection[0]->total_product);
        self::assertEquals(2000000, $collection[0]->max_price);
        self::assertEquals(1000000, $collection[0]->min_price);
    }

    public function insertProductsFood()
    {
        DB::table('products')->insert([
            [
                "id" => "3",
                "name" => "BAKSO",
                // "description" => "1",
                "price" => 20000,
                "category_id" => "FOOD",
                "created_at" => "2020-10-10 10:10:10"

            ], [
                "id" => "4",
                "name" => "PECEL",
                // "description" => "1",
                "price" => 10000,
                "category_id" => "FOOD",
                "created_at" => "2020-10-10 10:10:10"
            ]
        ]);
    }

    public function testQueryGroupBy()
    {
        $this->insertProducts();
        $this->insertProductsFood();

        $collection = DB::table("products")
            ->select("category_id", DB::raw("count(*) as total_product"))
            ->groupBy("category_id")
            ->orderBy("category_id", "desc")
            ->get();

        self::assertCount(2, $collection);
        self::assertEquals("SMARTPHONE", $collection[0]->category_id);
        self::assertEquals("FOOD", $collection[1]->category_id);
        self::assertEquals(2, $collection[0]->total_product);
        self::assertEquals(2, $collection[1]->total_product);
    }

    public function testQueryGroupByHaving()
    {
        $this->insertProducts();
        $this->insertProductsFood();

        $collection = DB::table("products")
            ->select("category_id", DB::raw("count(*) as total_product"))
            ->groupBy("category_id")
            ->having(DB::raw("count(*)"), ">", 2)
            ->orderBy("category_id", "desc")
            ->get();

        self::assertCount(0, $collection);
    }

    public function testLocking()
    {
        $this->insertProducts();

        DB::transaction(function () {
            $collection = DB::table("products")
                ->where('id', '=', '1')
                ->lockForUpdate()
                ->get();

            self::assertCount(1, $collection);
        });
    }

    public function testPagination()
    {
        $this->insertCategories();

        $paginate = DB::table("categories")
            ->paginate(perPage: 2, page: 2);

        self::assertEquals(2, $paginate->currentPage());
        self::assertEquals(2, $paginate->perPage());
        self::assertEquals(2, $paginate->lastPage());
        self::assertEquals(4, $paginate->total());

        $collection = $paginate->items();
        self::assertCount(2, $collection);
        foreach ($collection as $item) {
            Log::info(json_encode($item));
        }
    }

    public function testIterateAllPagination()
    {
        $this->insertCategories();

        $page = 1;

        while (true) {
            $paginate = DB::table("categories")
                ->paginate(perPage: 2, page: $page);

            if ($paginate->isEmpty()) {
                break;
            } else {
                $page++;

                $collection = $paginate->items();
                self::assertCount(2, $collection);
                foreach ($collection as $item) {
                    Log::info(json_encode($item));
                }
            }
        }
    }


    public function testCursorPagination()
    {
        $this->insertCategories();

        $cursor = "id";

        while (true) {
            $paginate = DB::table("categories")
                ->orderBy("id")
                ->cursorPaginate(perPage: 2, cursor: $cursor);
            foreach ($paginate->items() as  $item) {
                self::assertNotNull($item);
                Log::info(json_encode($item));
            }

            $cursor = $paginate->nextCursor();
            if ($cursor == null) {
                break;
            }
        }
    }
}
