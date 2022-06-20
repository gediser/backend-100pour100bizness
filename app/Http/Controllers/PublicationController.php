<?php

namespace App\Http\Controllers;

use Error;
use App\Models\Publication;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Http\Resources\PublicationResource;
use App\Http\Requests\StorePublicationRequest;
use App\Http\Requests\UpdatePublicationRequest;

class PublicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $user = $request->user();

        return PublicationResource::collection(Publication::where('user_id', $user->id)->orderBy("created_at", "desc")->limit(300)->paginate(5));
        
    }

    public function publicationsGet(Request $request){
        $publications_ids = explode(',', $request->query('pubs'));
        for($i=0; $i < count($publications_ids); $i++){
            $publications_ids[$i] = intval(trim($publications_ids[$i]));
        }
        return PublicationResource::collection(Publication::whereIn('id', $publications_ids)->get());
    }

    public function publicationsJusteSave(Request $request){
        DB::table('justepourvous')->delete();

        $publications = $request->input('publications');
        DB::table('justepourvous')->insert($publications);

        return response(["success"=>true]);
    }

    public function publicationsJusteGetAll(){
        $publications = DB::table('justepourvous')->get();
        $publications_ids = [];
        for($i=0; $i<count($publications); $i++){
            $publications_ids[] = $publications[$i]->publication_id;
        }
        return PublicationResource::collection(Publication::whereIn('id', $publications_ids)->orderBy("id", "asc")->get());
    }

    public function publicationsMeilleurSave(Request $request){
        DB::table('meilleurclassement')->delete();

        $publications = $request->input('publications');
        DB::table('meilleurclassement')->insert($publications);

        return response(["success"=>true]);
    }

    public function publicationsMeilleurGetAll(){
        $publications = DB::table('meilleurclassement')->get();
        $publications_ids = [];
        for($i=0; $i<count($publications); $i++){
            $publications_ids[] = $publications[$i]->publication_id;
        }
        return PublicationResource::collection(Publication::whereIn('id', $publications_ids)->orderBy("id", "asc")->get());
    }

    public function viewPublicPublications(Request $request){
        return PublicationResource::collection((new Publication())->orderBy("created_at", "desc")->limit(300)->paginate(5));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePublicationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePublicationRequest $request)
    {
        //
        $data = $request->validated();
        // Check if image was given and save on local file system

        if (isset($data['image'])){
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;
        }

        $publication = Publication::create($data);

        return new PublicationResource($publication);
    }

   

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Publication  $publication
     * @return \Illuminate\Http\Response
     */
    public function show(Publication $publication, Request $request)
    {
        //
        $user = $request->user();
        if ($user->id !== $publication->user_id){
            return abort(403, 'Unauthorized action.');
        }
        return new PublicationResource($publication);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePublicationRequest  $request
     * @param  \App\Models\Publication  $publication
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePublicationRequest $request, Publication $publication)
    {
        //
        $data = $request->validated();

        // Check if image was given and save on local file system
        if (isset($data['image'])){
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;

            // If there is an old image, delete it
            if ($publication->image){
                $absolutePath = public_path($publication->image);
                File::delete($absolutePath);
            }
        }

        // Update survey in the database
        $publication->update($data);

        return new PublicationResource($publication);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Publication  $publication
     * @return \Illuminate\Http\Response
     */
    public function destroy(Publication $publication, Request $request)
    {
        //
        $user = $request->user();
        if ($user->id !== $publication->user_id){
            return abort(403, 'Unauthorized action.');
        }
        
        $publication->delete();

        // If there is an old image, delete it
        if ($publication->image){
            $absolutePath = public_path($publication->image);
            File::delete($absolutePath);
        }

        return response('', 204);
    }

    private function saveImage($image){
        // Check if image is valid base64 string
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)){
            // Take out the base64 encoded text without mime type
            $image = substr($image, strpos($image, ',') + 1);

            // Get the file extension
            $type = strtolower($type[1]); // jpg, png, gif

            // check if file is an image
            if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])){
                throw new \Exception('invalid image type');
            }
            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);

            if ($image === false){
                throw new \Exception('base64_decode failed');
            }
        }else{
            throw new \Exception('did not match data URI with image data.');
        }

        $dir = 'images/';
        $file = Str::random() . '.' . $type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;

        if (!File::exists($absolutePath)){
            File::makeDirectory($absolutePath, 0755, true);
        }
        file_put_contents($relativePath, $image);

        return $relativePath;
    }

}
