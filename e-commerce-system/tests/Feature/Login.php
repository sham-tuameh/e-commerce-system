<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Login extends TestCase
{
    use RefreshDatabase;

    public function Login_existing_user()
    {
        $user = User::create([
            'name' => 'Sham Tuameh',
            'email' => 'stomeh6@gmail.com',
            'password' => bcrypt('secret')
        ]);
        $response = $this->post('api/login', [
            'email' => $user->email,
            'password' => 'secret',
            'device_name' => 'Huawei'
        ]);
        $response->assertSuccessful();
        $this->assertNotEmpty($response->getContent());
        $this->assertDatabaseHas('personal_access_token', [
            'name' => 'Huawei',
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id
        ]);
    }

    protected function get_user_from_token()
    {
        $user = User::create([
            'name' => 'Sham Tuameh',
            'email' => 'stomeh6@gmail.com',
            'password' => bcrypt('secret')
        ]);
        $token = $user->createToken('Huawei')->plainTextToken;
        $response = $this->get('/api/user', [
            'Authorization' => 'Bearer' . $token
        ]);
        $response->assertSuccessful();

        $response->assertJson(function ($json){
            $json->where('email', 'stomeh6@gmail.com')
                ->missing('password')
                ->etc();
        });
}
}
