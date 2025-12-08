<?php

use App\Http\Controllers\RoomController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RoomController::class, 'index'])->name('welcome');

Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
Route::get('/rooms/{code}', [RoomController::class, 'show'])->name('rooms.show');
Route::post('/rooms/{code}/join', [RoomController::class, 'join'])->name('rooms.join');
Route::post('/rooms/{code}/leave', [RoomController::class, 'leave'])->name('rooms.leave');
Route::post('/rooms/{code}/heartbeat', [RoomController::class, 'heartbeat'])->name('rooms.heartbeat');

Route::post('/rooms/{roomCode}/stories/start-new', [StoryController::class, 'startNew'])->name('stories.start-new');
Route::post('/rooms/{roomCode}/stories/{storyId}/reveal', [StoryController::class, 'reveal'])->name('stories.reveal');

Route::post('/rooms/{roomCode}/stories/{storyId}/votes', [VoteController::class, 'store'])->name('votes.store');
Route::get('/rooms/{code}/votes-status', [RoomController::class, 'getVotesStatus'])->name('rooms.votes-status');
Route::post('/rooms/{roomCode}/emoji-throw', [App\Http\Controllers\EmojiThrowController::class, 'store'])->name('emoji-throw.store');

