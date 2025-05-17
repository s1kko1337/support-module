<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Events\StoreEventRequest;
use App\Http\Requests\Api\V1\Events\UpdateEventRequest;
use App\Http\Resources\Api\V1\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return EventResource::collection(Event::with('group')->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();
        $event = Event::create($data);

        //broadcast(new StorePostEvent($post))->toOthers();

        return EventResource::make($event)->resolve();
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $group): EventResource
    {
        return new EventResource($group);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event): EventResource
    {
        $event->update($request->all());
        return new EventResource($event);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json([
            "message" => "Post deleted"
        ], 204);
    }
}





