<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Participant;
use App\Models\EmojiThrow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EmojiThrowController extends Controller
{
    public function store(Request $request, $roomCode)
    {
        $request->validate([
            'to_participant_id' => 'required|exists:participants,id',
            'emoji' => 'required|string|max:10',
        ]);

        $room = Room::where('code', $roomCode)->firstOrFail();
        $sessionId = Session::getId();

        $fromParticipant = $room->participants()
            ->where('session_id', $sessionId)
            ->firstOrFail();

        $toParticipant = Participant::findOrFail($request->to_participant_id);

        // Verificar se o participante destino pertence à mesma sala
        if ($toParticipant->room_id !== $room->id) {
            return response()->json([
                'success' => false,
                'message' => 'Participante inválido.',
            ], 400);
        }

        // Não pode arremessar em si mesmo
        if ($fromParticipant->id === $toParticipant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Você não pode arremessar em si mesmo!',
            ], 400);
        }

        $emojiThrow = EmojiThrow::create([
            'room_id' => $room->id,
            'from_participant_id' => $fromParticipant->id,
            'to_participant_id' => $toParticipant->id,
            'emoji' => $request->emoji,
        ]);

        return response()->json([
            'success' => true,
            'emoji_throw' => [
                'id' => $emojiThrow->id,
                'emoji' => $emojiThrow->emoji,
                'from_name' => $fromParticipant->name,
                'to_participant_id' => $toParticipant->id,
            ],
        ]);
    }
}



