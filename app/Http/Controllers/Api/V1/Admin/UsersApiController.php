<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Client;
use App\Models\PersonalTrainer;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UsersApiController extends Controller
{

    use InteractsWithMedia;

    public function index()
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new UserResource(User::with(['roles'])->get());
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->all());
        $user->roles()->sync($request->input('roles', []));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new UserResource($user->load(['roles']));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->all());
        $user->roles()->sync($request->input('roles', []));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function user(Request $request)
    {
        return $request->user()->load('client.client_data', 'personal_trainer.spots');
    }

    public function updateUser(Request $request)
    {
        if ($request->password) {
            $request->validate([
                'name' => 'required',
                'password' => 'required|min:6',
                'password_confirmation' => 'required|same:password',

            ], [], [
                'name' => 'Nome',
                'password' => 'Password',
                'password_confirmation' => 'Confirmação da password',
            ]);
        } else {
            $request->validate([
                'name' => 'required',
            ], [], [
                'name' => 'Nome',
            ]);
        }

        $user = User::find($request->id)->load('client');
        $user->name = $request->name;
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        return $user;
    }

    public function updateClient(Request $request)
    {
        $user_id = $request->user()->id;
        $client = Client::where('user_id', $user_id)->first();

        if ($client) {
            $client->name = $request->name ? $request->name : $request->user()->name;
            $client->zip = $request->zip;
            $client->location = $request->location;
            $client->country_id = $request->country_id;
            $client->phone = $request->phone;
            $client->vat = $request->vat;
            $client->save();
        } else {
            $client = new Client;
            $client->name = $request->name ? $request->name : $request->user()->name;
            $client->zip = $request->zip;
            $client->location = $request->location;
            $client->country_id = $request->country_id;
            $client->phone = $request->phone;
            $client->vat = $request->vat;
            $client->user_id = $user_id;
            $client->save();
        }

        return $client;
    }

    public function updateProfessionalData(Request $request)
    {

        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|max:255',
            'phone' => 'required|max:255',
            'description' => 'required|max:1000',
            'price' => 'required|max:255',
            'professional_certificate' => 'required|max:255',
            'expiration' => 'required|max:255',
            'certificate_type' => 'required',
            'spots' => 'required',
        ], [], [
            'name' => 'Nome profissional',
            'email' => 'Email profissional',
            'phone' => 'Contacto telefónico',
            'description' => 'Descrição profissional',
            'price' => 'Preço',
            'professional_certificate' => 'Certificado profissional',
            'expiration' => 'Expira em',
            'certificate_type' => 'Tipo de certificado',
            'spots' => 'Spots',
        ]);

        $user_id = $request->user()->id;
        $personal_trainer = PersonalTrainer::where('user_id', $user_id)->first();
        if ($personal_trainer) {
            $personal_trainer->name = $request->name;
            $personal_trainer->email = $request->email;
            $personal_trainer->phone = $request->phone;
            $personal_trainer->facebook = $request->facebook;
            $personal_trainer->instagram = $request->instagram;
            $personal_trainer->linkedin = $request->linkedin;
            $personal_trainer->tiktok = $request->tiktok;
            $personal_trainer->description = $request->description;
            $personal_trainer->price = $request->price;
            $personal_trainer->professional_certificate = $request->professional_certificate;
            $personal_trainer->expiration = $request->expiration;
            $personal_trainer->certificate_type = $request->certificate_type;
            $personal_trainer->save();
            $personal_trainer->spots()->sync($request->input('spots', []));
        } else {
            $personal_trainer = new PersonalTrainer;
            $personal_trainer->user_id = $user_id;
            $personal_trainer->name = $request->name;
            $personal_trainer->email = $request->email;
            $personal_trainer->phone = $request->phone;
            $personal_trainer->facebook = $request->facebook;
            $personal_trainer->instagram = $request->instagram;
            $personal_trainer->linkedin = $request->linkedin;
            $personal_trainer->tiktok = $request->tiktok;
            $personal_trainer->description = $request->description;
            $personal_trainer->price = $request->price;
            $personal_trainer->professional_certificate = $request->professional_certificate;
            $personal_trainer->expiration = $request->expiration;
            $personal_trainer->certificate_type = $request->certificate_type;
            $personal_trainer->save();
            $personal_trainer->spots()->sync($request->input('spots', []));
        }
        return $personal_trainer;
    }

    public function saveProfilePhoto(Request $request)
    {

        $personal_trainer = PersonalTrainer::find($request->personal_trainer_id);

        if ($personal_trainer && $request->has('profile_photo')) {
            // Remove existing profile photo if it exists
            $mediaItems = $personal_trainer->getMedia('photos');
            if ($mediaItems->isNotEmpty()) {
                $mediaItems[0]->delete();
            }

            $profilePhoto = $request->input('profile_photo');

            // Remove the 'data:image/jpeg;base64,' part if it exists
            $profilePhoto = str_replace('data:image/jpeg;base64,', '', $profilePhoto);
            $profilePhoto = str_replace(' ', '+', $profilePhoto);
            $photoName = 'profile_' . time() . '.jpg';
            $filePath = 'public/profile_photos/' . $photoName;

            // Save the decoded base64 image to the storage
            Storage::disk('local')->put($filePath, base64_decode($profilePhoto));

            // Add the photo to the media library
            $personal_trainer->addMedia(storage_path('app/' . $filePath))->toMediaCollection('photos');
        }

        return response()->json(['status' => 'success']);

    }

    public function saveOtherPhoto(Request $request)
    {
        $personal_trainer = PersonalTrainer::find($request->personal_trainer_id);

        if ($personal_trainer && $request->has('photo')) {
            $photo = $request->input('photo');

            // Remove the 'data:image/jpeg;base64,' part if it exists
            $photo = str_replace('data:image/jpeg;base64,', '', $photo);
            $photo = str_replace(' ', '+', $photo);
            $photoName = 'profile_' . time() . '.jpg';
            $filePath = 'public/profile_photos/' . $photoName;

            // Save the decoded base64 image to the storage
            Storage::disk('local')->put($filePath, base64_decode($photo));

            // Add the photo to the media library
            $personal_trainer->addMedia(storage_path('app/' . $filePath))->toMediaCollection('photos');
        }

        return response()->json(['status' => 'success']);
    }

    public function deletePhoto($photo_id)
    {
        // Find the media item by ID
        $mediaItem = Media::find($photo_id);

        if ($mediaItem) {
            // Delete the media item
            $mediaItem->delete();
            return response()->json(['status' => 'success', 'message' => 'Photo deleted successfully']);
        }

        return response()->json(['status' => 'error', 'message' => 'Photo not found'], 404);
    }
}
