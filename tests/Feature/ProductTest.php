<?php

namespace Tests\Feature;

use App\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    /**
     * CREATE-1
     */
    public function test_client_can_create_a_product()
    {
        // Given
        $productData = factory(Product::class, 'reformatted')->make();

        // When
        $response = $this->json('POST', '/api/products', $productData->toArray()); 

        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(201);
        
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'name',
                    'price'
                ],
                'links'
            ]
        ]);

        // Assert the product was created
        // with the correct data
        $response->assertJsonFragment([
            'name' => $productData->data['attributes']['name'],
            'price' => $productData->data['attributes']['price']
        ]);
        
        $body = $response->decodeResponseJson();

        // Assert product is on the database
        $this->assertDatabaseHas(
            'products',
            [
                'id' => $body['data']['id'],
                'name' => $productData->data['attributes']['name'],
                'price' => $productData->data['attributes']['price']
            ]
        );
    }

    /**
     * CREATE-2
     */
    public function test_client_create_a_product_without_name()
    {
        // Given
        $productData = factory(Product::class,'withoutName')->make();
        // When
        $response = $this->json('POST', '/api/products', $productData->toArray());
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(422);
        // Assert the response has the correct structure
        $response->assertJsonStructure(['*' => ['*'=>[
            'code',
            'title',
        ]]]);
        $response->assertJsonFragment([
            'code' => 'Error-1',
            'title' => 'A name is required'
        ]);
        // Assert product is on the database
        $this->assertDatabaseMissing(
            'products',
            [
                'name' => $productData->data['attributes']['name'],
                'price' => $productData->data['attributes']['price']
            ]
        );
    }

    /**
     * CREATE-3
     */
    public function test_client_create_a_product_without_price()
    {
        // Given
        $productData = factory(Product::class, 'withoutPrice')->make([
            'price' => null
        ]);
        // When
        $response = $this->json('POST', '/api/products', $productData->toArray());
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(422);
        // Assert the response has the correct structure
        $response->assertJsonStructure(['*' => ['*'=>[
            'code',
            'title',
        ]]]);
        $response->assertJsonFragment([
            'code' => 'Error-1',
            'title' => 'A price is required'
        ]);
        // Assert product is on the database
        $this->assertDatabaseMissing(
            'products',
            [
                'name' => $productData->data['attributes']['name'],
                'price' => $productData->data['attributes']['price']
            ]
        );
    }

    /**
     * CREATE-4
     */
    public function test_client_create_a_product_with_price_string()
    {
        // Given
        $productData = factory(Product::class, 'withoutNumPrice')->make([
            'price' => 'Dolar'
        ]);
        // When
        $response = $this->json('POST', '/api/products', $productData->toArray());
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(422);
        // Assert the response has the correct structure
        $response->assertJsonStructure(['*' => ['*'=>[
            'code',
            'title',
        ]]]);
        $response->assertJsonFragment([
            'code' => 'Error-1',
            'title' => 'The price has to be numeric'
        ]);
    }

    /**
     * CREATE-5
     */
    public function test_client_create_a_product_with_price_less_or_equal_to_zero()
    {
        // Given
        $productData = factory(Product::class, 'subzero')->make([
            'price' => -2
        ]);
        // When
        $response = $this->json('POST', '/api/products', $productData->toArray());
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(422);
        // Assert the response has the correct structure
        $response->assertJsonStructure(['*' => ['*'=>[
            'code',
            'title',
        ]]]);
        $response->assertJsonFragment([
            'code' => 'Error-1',
            'title' => 'The price has to be more than 0 (zero)'
        ]);
        // Assert product is on the database
        $this->assertDatabaseMissing(
            'products',
            [
                'name' => $productData->data['attributes']['name'],
                'price' => $productData->data['attributes']['price']
            ]
        );
    }

    /**
     * LIST-1
     */
    public function test_client_can_get_products()
    {
        $newProduct = factory(Product::class,2)->create();

        $responseGet = $this->json("GET", 'api/products');
        
        $responseGet->assertStatus(200);
        //Then
        // Assert it sends the correct HTTP Status
        $responseGet->assertJsonStructure([
            'data'=>[
                '*'=>[
                    'type',
                    'id',
                    'attributes' => [
                        'name',
                        'price'
                    ],
                    'links'
                ]
            ]
        ]);
        $responseGet->assertJsonFragment([
            'type' => 'products',
            'id' => $newProduct[0]->id,
            'name' => $newProduct[0]->name,
            'price' => strval($newProduct[0]->price),
            'self' => route('api-product', ['id' => $newProduct[0]->id])
        ]);
    }

    /**
     * LIST-2
     */
    public function test_client_get_empty_products()
    {
        $responseGet = $this->json("GET", 'api/products');
        
        $responseGet->assertStatus(200);
        //Then
        // Assert it sends the correct HTTP Status
        $responseGet->assertJsonStructure([
            '*'=>[]
        ]);
    }

    /**
     * SHOW-1
     */
    public function test_client_can_show_products()
    {
        $newProduct = factory(Product::class)->create();

        $responseOneGet = $this->json('GET', '/api/products/'.$newProduct->id);

        $responseOneGet->assertStatus((200));
            //Then
            $responseOneGet->assertJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'name',
                        'price'
                    ],
                    'links'
                ]
            ]);

            $responseOneGet->assertJsonFragment([
                'type' => 'products',
                'id'=> $newProduct->id,
                'name'=> $newProduct->name,
                'price'=> strval($newProduct->price),
                'self' => route('api-product', ['id' => $newProduct->id])
            ]);

            $this->assertDatabaseHas(
                'products',
                [
                    'id'=> $newProduct->id,
                    'name'=> $newProduct->name,
                    'price'=> $newProduct->price
                ]
            );       
    }

    /**
     * SHOW-2
     */
    public function test_client_cant_show_from_unknown_id()
    {
        $responseOneGet = $this->json('GET', '/api/products/24');

        $responseOneGet->assertStatus((404));
            //Then
            $responseOneGet->assertJsonStructure(
                ['*' => ['*'=>
                    'code',
                    'title',
                ]]
            );

            $responseOneGet->assertJsonFragment([
                'code' => 'Error-2',
                'title' => 'ID does not exist'
            ]);

            $this->assertDatabaseMissing(
                'products',
                [
                    'id'=> 24
                ]
            );       
    }
    
    /**
     * UPDATE-1
     */
    public function test_client_can_update_products()
    {
        // Given
        $newProduct = factory(Product::class)->create();

        $newProductUpdate = factory(Product::class,'reformatted')->make();

        $responsePut = $this->json('PUT', 'api/products/'.$newProduct->id, $newProductUpdate->toArray());

        $responsePut->assertStatus((200));
        //Then
        $responsePut->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'name',
                    'price'
                ],
                'links'
            ]
        ]);

        $responsePut->assertJsonFragment([
            'id' => $newProduct->id,
            'name' => $newProductUpdate->data['attributes']['name'],
            'price' => $newProductUpdate->data['attributes']['price'],
            'self' => route('api-product', ['id' => $newProduct->id])
        ]);

        //Assert in DB
        $this->assertDatabaseHas(
            'products',
            [
                'id' => $newProduct->id,
                'name' => $newProductUpdate->data['attributes']['name'],
                'price' => $newProductUpdate->data['attributes']['price']
            ]
        );       
        
    }

    /**
     * UPDATE-2
     */
    public function test_client_update_price_string()
    {
        // Given
        $newProduct = factory(Product::class)->create();

        $newProductUpdate = factory(Product::class,'withoutNumPrice')->make();

        $responsePut = $this->json('PUT', 'api/products/'.$newProduct->id, $newProductUpdate->toArray());

        $responsePut->assertStatus((422));
        //Then
        $responsePut->assertJsonStructure(
            ['*' => ['*'=>[
                'code',
                'title'
            ]
        ]]);

        $responsePut->assertJsonFragment([
            'code' => 'Error-1',
            'title' => 'The price has to be numeric'
        ]);   
    }

    /**
     * UPDATE-3
     */
    public function test_client_update_price_less_to_zero()
    {
        // Given
        $newProduct = factory(Product::class)->create();

        $newProductUpdate = factory(Product::class, 'subzero')->make();

        $responsePut = $this->json('PUT', 'api/products/'.$newProduct->id, $newProductUpdate->toArray());

        $responsePut->assertStatus((422));
        //Then
        $responsePut->assertJsonStructure([
            '*'=>[ '*'=>[
                'code',
                'title'
            ]]
        ]);

        $responsePut->assertJsonFragment([
            'code' => 'Error-1',
            'title' => 'The price has to be more than 0 (zero)'
        ]);

        //Assert in DB
        $this->assertDatabaseMissing(
            'products',
            [
                'id' => $newProductUpdate->id,
                'name' => $newProductUpdate->data['attributes']['name'],
                'price' => $newProductUpdate->data['attributes']['price']
            ]
        );       
        
    }

    /**
     * UPDATE-4
     */
    public function test_client_update_id_unknowed()
    {
        // Given
        $newProductUpdate = factory(Product::class, 'reformatted')->make();

        $responsePut = $this->json('PUT', 'api/products/3', $newProductUpdate->toArray());

        $responsePut->assertStatus((404));
        //Then
        $responsePut->assertJsonStructure(
            ['*' => ['*'=>
            'code',
            'title',
            ]]
        );

        $responsePut->assertJsonFragment([
            'code' => 'Error-2',
            'title' => 'ID does not exist'
        ]);

        //Assert in DB
        $this->assertDatabaseMissing(
            'products',
            [
                'id' => $newProductUpdate->id,
                'name' => $newProductUpdate->data['attributes']['name'],
                'price' => $newProductUpdate->data['attributes']['price']
            ]
        );       
        
    }

    /**
     * DELETE-1
     */
    public function test_client_can_delete_products()
    {
        // Given
        $newProduct = factory(Product::class)->create();

        $responseDelete = $this->json('DELETE', '/api/products/' . $newProduct->id);

        $responseDelete->assertStatus(204);
        $responseDelete->assertSee(NULL);
    }

    /**
     * DELETE-2
     */
    public function test_client_cant_delete_inexistent_product()
    {
        // Given
        $responseDelete = $this->json('DELETE', '/api/products/3');

        $responseDelete->assertStatus(404);

        // Assert the response has the correct structure
        $responseDelete->assertJsonStructure(['*' => ['*'=>
            'code',
           'title',
        ]]);

        $responseDelete->assertJsonFragment([
            'code' => 'Error-2',
            'title' => 'ID does not exist'
        ]);
        $this->assertDatabaseMissing(
            'products',
            [
                'id' => 1
            ]
        );
    }
}
 
