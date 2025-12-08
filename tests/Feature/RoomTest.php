<?php

namespace Tests\Feature;

use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_room(): void
    {
        $response = $this->post('/rooms', [
            'name' => 'Test Room',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('rooms', [
            'name' => 'Test Room',
        ]);
    }

    public function test_can_view_room(): void
    {
        $room = Room::create([
            'code' => Room::generateCode(),
            'name' => 'Test Room',
            'is_active' => true,
        ]);

        $response = $this->get("/rooms/{$room->code}");

        $response->assertStatus(200);
        $response->assertSee($room->name);
    }
}

