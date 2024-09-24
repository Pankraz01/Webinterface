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
        
        $response = $this->actingAs($user)->post('/upload', [
            'file' => $file,
            'tags' => 'transition, ink, monochrom',
        ]);

        $response->assertSessionHas('success');

        $animation = Animation::latest()->first();
        $this->assertMatchesRegularExpression('/^[0-9]+_animation\.png$/', $animation->file_name);
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_view_any_uploaded_animation_file()
    {
        $user = User::factory()->create();
    
        // Stelle sicher, dass das Upload-Verzeichnis existiert
        $uploadPath = public_path('uploads');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
    
        // Füge eine Dummy-Datei im Upload-Verzeichnis hinzu, falls noch keine existiert
        $fileName = 'dummy_file.png';
        $filePath = $uploadPath . '/' . $fileName;
        if (!file_exists($filePath)) {
            \Illuminate\Support\Facades\File::put($filePath, 'dummy content');
        }
    
        // Erstelle einen Eintrag in der Datenbank
        $animation = Animation::factory()->create([
            'file_name' => $fileName,
            'tags' => 'test, dummy, file',
        ]);
    
        // Rufe die URL auf, um die Datei anzusehen
        $response = $this->actingAs($user)->get(asset('uploads/' . $fileName));
        $response->assertStatus(200);
    }
    
    

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_download_animation_file()
    {
        $user = User::factory()->create();

        // Füge eine Datei in das Upload-Verzeichnis ein
        $filePath = public_path('uploads/animation.png');
        \Illuminate\Support\Facades\File::put($filePath, 'dummy content');

        $animation = Animation::factory()->create([
            'file_name' => 'animation.png',
            'tags' => 'transition, ink, monochrom',
        ]);

        // Teste den Datei-Download
        $response = $this->actingAs($user)->get(route('animations.download', $animation->file_name));
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=animation.png');
    }
}
