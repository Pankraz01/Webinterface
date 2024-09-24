<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Animation;

class AnimationUploadTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_view_the_upload_form()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/upload');
        $response->assertStatus(200);
        $response->assertSee('Animation hochladen');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_upload_an_animation()
    {
        $user = User::factory()->create();
        $file = \Illuminate\Http\UploadedFile::fake()->image('animation.png');
        
        // Führe den Upload durch
        $response = $this->actingAs($user)->post('/upload', [
            'file' => $file,
            'tags' => 'transition, ink, monochrom',
        ]);

        $response->assertSessionHas('success');

        // Hole die hochgeladene Animation aus der Datenbank
        $animation = Animation::latest()->first();

        // Überprüfe, ob der Dateiname korrekt generiert wurde
        $this->assertMatchesRegularExpression('/^[0-9]+_animation\.png$/', $animation->file_name);

        // Überprüfe, ob die Tags korrekt gespeichert wurden
        $this->assertEquals('transition, ink, monochrom', $animation->tags);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_view_dashboard_with_uploaded_animations()
    {
        $user = User::factory()->create();
        $animation = Animation::factory()->create([
            'file_name' => 'animation.png',
            'tags' => 'transition, ink, monochrom',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('animation.png');
        $response->assertSee('transition, ink, monochrom');
    }
}
