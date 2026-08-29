<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemHealthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    public function test_public_pages_render_successfully()
    {
        $routes = [
            '/' => 200,
            '/programmes' => 200,
            '/admission-requirements' => 200,
            '/track-application' => 200,
            '/news' => 200,
            '/events' => 200,
            '/faqs' => 200,
            '/downloads' => 200,
            '/student-guide' => 200,
            '/contact' => 200,
            '/privacy-policy' => 200,
            '/terms-and-conditions' => 200,
            '/login' => 200,
            '/careers' => 200,
            '/up' => 200,
        ];

        foreach ($routes as $uri => $expectedStatus) {
            $response = $this->get($uri);
            $response->assertStatus($expectedStatus);
        }

        // Test register route (redirects to wizard if applicant_login_required is false, or renders 200 if true)
        $registerRes = $this->get('/register');
        $this->assertTrue(in_array($registerRes->getStatusCode(), [200, 302]));
    }

    public function test_auth_and_protected_redirection()
    {
        // Unauthenticated access to dashboard should redirect to login or consent
        $response = $this->get('/applicant/dashboard');
        $response->assertRedirect('/login');

        $adminResponse = $this->get('/admin/dashboard');
        $adminResponse->assertRedirect('/login');
    }
}
