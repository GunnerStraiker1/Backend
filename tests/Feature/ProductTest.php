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
     * @group create
     */
    public function test_client_can_create_a_product()
    {
        // Given
        $productData = [
            'name' => 'Huevito Kinder',
            'price' => '9.30'
        ];

        // When
        $response = $this->json('POST', '/api/products', $productData); 

        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(201);
        
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'id',
            'name',
            'price'
        ]);

        // Assert the product was created
        // with the correct data
        $response->assertJsonFragment([
            'name' => 'Huevito Kinder',
            'price' => '9.30'
        ]);
        
        $body = $response->decodeResponseJson();

        // Assert product is on the database
        $this->assertDatabaseHas(
            'products',
            [
                'id' => $body['id'],
                'name' => 'Huevito Kinder',
                'price' => '9.30'
            ]
        );
    }

    public function test_client_can_get_products()
    {
        //Given
        $productData = [
            'name' => 'Huevito Kinder',
            'price' => '9.30'
        ];

        //When
        $responsePost = $this->json("POST", 'api/products', $productData);

        $responseGet = $this->json("GET", 'api/products');
        
        if ($responseGet->assertStatus(200)) {
            //Then
            // Assert it sends the correct HTTP Status
            $responseGet->assertJsonStructure([
                'data'=>[
                    '*'=>[
                        'id',
                        'updated_at',
                        'created_at',
                        'name',
                        'price'
                    ]
                ]
            ]);
        }
        elseif ($responseGet->assertStatus(404)) {
            $responseGet->assertEquals(null, $responseGet->getContent());
        }
    }

    public function test_client_can_show_products()
    {
        // Given
        $productData = [
            'name' => 'Huevito Kinder',
            'price' => '9.30'
        ];

        // When
        $responsePost = $this->json('POST', '/api/products', $productData);
        $bodyPost = $responsePost->decodeResponseJson();

        $responseOneGet = $this->json('GET', '/api/products/'.$bodyPost['id']);

        if ($responseOneGet->assertStatus((200))) {
            //Then
            $responseOneGet->assertJsonStructure([
                'id',
                'updated_at',
                'created_at',
                'name',
                'price'
            ]);

            $responseOneGet->assertJsonFragment([
                'name'=> 'Huevito Kinder',
                'price'=> '9.30'
            ]);

            $bodyResponse = $responseOneGet->decodeResponseJson();

            $this->assertDatabaseHas(
                'products',
                [
                    'id'=> $bodyResponse['id'],
                    'name'=> 'Huevito Kinder',
                    'price'=> '9.30'
                ]
            );

        } else if ($responseOneGet->assertStatus((404))){
            $responseOneGet->assertEquals(NULL,$responseOneGet->getContent());
        }
        

    }
    
    public function test_client_can_update_products()
    {
        // Given
        $productData = [
            'name' => 'Huevito Kinder',
            'price' => '9.30'
        ];

        // When
        $responsePost = $this->json('POST', '/api/products', $productData);
        $bodyPost = $responsePost->decodeResponseJson();

        $newDataProduct = [
            'id' => $bodyPost['id'],
            'name' => 'Carlos XV',
            'price' => '5.40'
        ];

        $responsePut = $this->json('PUT', 'api/products/'.$bodyPost['id'], $newDataProduct);

        if ($responsePut->assertStatus((200))) {
            //Then
            $responsePut->assertJsonStructure([
                'id',
                'updated_at',
                'created_at',
                'name',
                'price'
            ]);

            $responsePut->assertJsonFragment([
                'id' => $bodyPost['id'],
                'name' => 'Carlos XV',
                'price' => '5.40'
            ]);

            $bodyResponse = $responsePut->decodeResponseJson();

            //Assert in DB
            $this->assertDatabaseHas(
                'products',
                [
                    'id' => $bodyResponse['id'],
                    'name' => 'Carlos XV',
                    'price' => '5.40'
                ]
            );
            
        } elseif ($responsePut->assertStatus(404)) {
            $responsePut->assertEquals(NULL, $responsePut->getContent());
        }
        
        
    }

    public function test_client_can_delete_products()
    {
        // Given
        $productDataA = [
            'name' => 'Huevito Kinder',
            'price' => '9.30'
        ];

        $productDataB = [
            'name' => 'Super Mega Chancho',
            'price' => '49.99'
        ];

        //When
        $reponsePostA = $this->json('POST', '/api/products', $productDataA);
        $reponsePostB = $this->json('POST', '/api/products', $productDataB);
        $bodyPostA= $reponsePostA->decodeResponseJson();

        $responseDelete = $this->json('DELETE', '/api/products/' . $bodyPostA['id']);

        if ($responseDelete->assertStatus(200)) {
            $responseDelete->assertJsonStructure([
                'status'
            ]);
        } elseif ($responseDelete->assertStatus(404)) {
            $responseDelete->assertEquals(NULL, $responseDelete->getContent());
        }
    }
}
 
