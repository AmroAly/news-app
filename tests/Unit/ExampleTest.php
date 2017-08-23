<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Mail\ConfirmationEmail;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use Mail;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_throws_an_error_if_the_fields_are_empty()
    {
        $response = $this->json('POST', 'api/register', [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => ''
        ]);


        $response
           ->assertStatus(422)
           ->assertJson([
               "name" => ["The name field is required."],
               "email" => ["The email field is required."],
               "password" => ["The password field is required."]
           ]);
    }

    public function test_register_function_can_create_user()
    {
        // user with email `test@test.com`
        $response = $this->createUser();

        $response
           ->assertStatus(200)
           ->assertJson([
               'registered' => true,
           ]);

        $this->assertDatabaseHas('users', [
           'email' => 'test@test.com'
        ]);
    }

    public function test_register_triggers_Cinfirmation_email()
    {
        Mail::fake();

        $userEmail = 'test@test.com';
        // user with email `test@test.com`
        $response = $this->createUser();

        Mail::assertSent(ConfirmationEmail::class, function ($e) use ($userEmail) {
            return $e->hasTo($userEmail);
        });
    }

    public function test_if_the_email_already_exists_it_will_throw_error()
    {
        // user with email `test@test.com`
        $response = $this->createUser();

        // user with the same email `test@test.com`
        $response2 = $this->createUser();

        $response
           ->assertStatus(200)
           ->assertJson([
               'registered' => true,
           ]);

           $response2
              ->assertStatus(422)
              ->assertJson([
                  "email" => ["The email has already been taken."]
              ]);
    }

    public function test_register_creates_a_user_in_the_database_with_unverified_state()
    {
        // user with email `test@test.com`
        $this->createUser();

        $this->assertDatabaseHas('users', [
            'email' => 'test@test.com',
            'verified' => false
        ]);
    }

    public function test_user_can_verify_his_account()
    {
        $this->createUser();

        $user = User::where('email', 'test@test.com')->first();

        $response = $this->get('/api/register/confirm/' . $user->verfication_token);

        $response
            ->assertStatus(200);
    }

    public function test_once_user_verified_his_account_column_verified_becomes_true()
    {
        $this->createUser();

        $user = User::where('email', 'test@test.com')->first();

        $this
            ->assertEquals(0, $user->verified);

        $response = $this->get('/api/register/confirm/' . $user->verfication_token);

        $response
            ->assertStatus(200)
            ->assertJson([
                 "verified" => true
             ]);

        $user = User::where('email', 'test@test.com')->first();

        $this
            ->assertEquals(1, $user->verified);
    }

    public function test_user_cant_login_without_activate_the_account()
    {
        $this->createUser();

        $response = $this->json('POST', 'api/login', [
            'email' => 'test@test.com',
            'password' => '123123'
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'verified' => false
            ]);
    }

    public function test_user_cant_login_with_empty_fields()
    {
        $response = $this->json('POST', 'api/login', [
            'email' => '',
            'password' => '',
        ]);


        $response
           ->assertStatus(422)
           ->assertJson([
               "email" => ["The email field is required."],
               "password" => ["The password field is required."]
           ]);
    }

    public function test_user_can_login_with_valid_fields()
    {
        // create the user
        $user = factory(User::class)->create([
            'email' => 'test@test.com',
            'password' => bcrypt('123123'),
            'verified' => true
        ]);

        $response = $this->json('POST', 'api/login', [
            'email' => 'test@test.com',
            'password' => '123123',
        ]);

        $user = User::where('email', 'test@test.com')->first();
        $response
           ->assertStatus(200)
           ->assertJson([
               'authenticated' => true,
               'api_token'     => $user->api_token,
               'user_id'       => $user->id
           ]);
    }

    public function test_user_can_logout()
    {
        // create the user
        $this->createUser();

        $user = User::where('email', 'test@test.com')->first();
        // verify the user
        $user->verified = true;
        $user->save();

        $response = $this->json('POST', '/api/login', [
            'email' => 'test@test.com',
            'password' => '123123',
        ]);

        $user = User::where('email', 'test@test.com')->first();

        $response = $this->call('POST', '/api/logout',[], [], [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $user->api_token
            ]);

        $response
           ->assertStatus(200)
           ->assertJson(['logged_out' => true]);
    }

    public function test_a_visitor_can_see_only_ten_records()
    {
        $response = $this->json('GET', '/api/news');

        $content = $response
            ->assertStatus(200)
            ->decodeResponseJson();

        $this->assertEquals(10, count($content));
    }

    protected function createUser()
    {
        return $this->json('POST', 'api/register', [
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => '123123',
            'password_confirmation' => '123123'
        ]);
    }
}
