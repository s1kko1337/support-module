<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Certifications\StoreCertificationRequest;
use App\Http\Requests\Api\V1\Certifications\UpdateCertificationRequest;
use App\Http\Requests\Api\V1\Events\StoreEventRequest;
use App\Http\Requests\Api\V1\Events\UpdateEventRequest;
use App\Http\Resources\Api\V1\CertificationResource;
use App\Http\Resources\Api\V1\EventResource;
use App\Models\Certification;
use App\Models\Event;
use Illuminate\Http\Request;

class CertificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return CertificationResource::collection(Certification::with('subject')->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCertificationRequest $request)
    {
        $event = Certification::create($request);

        //broadcast(new StorePostEvent($post))->toOthers();

        return CertificationResource::make($event)->resolve();
    }

    /**
     * Display the specified resource.
     */
    public function show(Certification $certification): CertificationResource
    {
        return new CertificationResource($certification);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCertificationRequest $request, Certification $certification): CertificationResource
    {
        $certification->update($request->all());
        return new CertificationResource($certification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Certification $certification)
    {
        $certification->delete();
        return response()->json([
            "message" => "Post deleted"
        ], 204);
    }
}





