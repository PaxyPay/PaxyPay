<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Faker $faker, Payment $payment)
    {
        for ($i = 0; $i < 200; $i++) {
            $randomString = uniqid('',true);
      
        $md5Hash = md5($randomString);
            $new_payment = new Payment();
            $new_payment->client_name = $faker->name();
            $new_payment->description = $faker->paragraph();
            $new_payment->total_price = $faker->randomFloat(2, 0, 999) + $faker->randomFloat(3, 0, 0.99);
            $new_payment->due_date = $faker->dateTimeBetween('2022-01-01', '2023-12-31')->format('Y-m-d');
            $new_payment->active = $faker->boolean();
            $new_payment->status = 'not_paid';
            $new_payment->token = $md5Hash;
            $new_payment->user_id = 1;
            $new_payment->save();
        
            // Aggiungi prodotti casuali
            $num_products = rand(1, 5);
            for ($j = 0; $j < $num_products; $j++) {
                $product = new Product();
                $product->payment_id = $new_payment->id; // Associare il prodotto al pagamento corrente
                $product->product_name = $faker->word();
                $product->product_price = $faker->randomFloat(2, 0, 999); // Assicurati che il nome dell'attributo sia corretto
                $product->quantity = $faker->randomNumber(2);
                $product->save(); 
            }
        }
        
    }
}
