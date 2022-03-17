<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Restaurant;

class RestaurantController extends Controller
{


    protected $unwanted_array = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
        'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
        'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
        'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', '\'' => '');
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $restaurant = Restaurant::where('user_id', Auth::id())->first();
        return view('admin.restaurants.index',compact('restaurant'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // aggiungo restaurant = false per permettere al layout di nascondere i tasti di modifica ristorante
        $restaurant = false;
        $categories=Category::all();
        return view('admin.restaurants.create', compact('restaurant','categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            "name" => "required|string|max:110",
            "description" => "nullable|string",
            "email" => 'required|string|max:100|unique:restaurants,email',
            "address" => "required|string|max:255|unique:restaurants,address",
            "city" => "required|string|max:100",
            "country" => "required|string|max:255",
            "post_code" => "required|string|max:5",
            "phone" => "required|string|max:15|unique:restaurants,phone",
            "image_cover" => "nullable|mimes:jpg,jpeg,png|max:2048",
            "categories" => "required|exists:categories,id",
        ]);

        $data = $request->all();
        $newRestaurant = new Restaurant();
        $newRestaurant->fill($data);

        $newRestaurant->slug = Str::of(strtr( $data['name'].' '.$data['address'], $this->unwanted_array ))->slug('-');

        $newRestaurant->user_id = Auth::id();

        if( isset($data['image_cover']) ) {
            $path_image = Storage::put("uploads/restaurant_images",$data['image_cover']);
            $newRestaurant->image_cover = $path_image;
        }

        $newRestaurant->save();
        if (isset($data["categories"])) {
            $newRestaurant->categories()->attach($data["categories"]);
        }

        return redirect()->route('restaurants.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Restaurant $restaurant)
    {
        $check_restaurant = Restaurant::select('id')->where('user_id', Auth::id())->first();
        $check_id = $check_restaurant->id;

        if($restaurant->id === $check_id){
            $categories=Category::all();
            return view('admin.restaurants.edit', compact('restaurant', 'categories'));
        }else{
            return abort(404);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        $request->validate([
        "name" => "required|string|max:110",
        "description" => "nullable|string",
        "email" =>'required|string|max:100|unique:restaurants,email,'.$restaurant->id,
        "address" => "required|string|max:255|unique:restaurants,address,".$restaurant->id,
        "city" => "required|string|max:100",
        "country" => "required|string|max:255",
        "post_code" =>"required|string|max:5",
        "phone" =>"required|string|max:15|unique:restaurants,phone,".$restaurant->id,
        "image_cover" =>  "nullable|mimes:jpg,jpeg,png|max:2048",
        "categories" => "required|exists:categories,id",
        ]);

        $data = $request->all();



        if($data['name'] != $restaurant->name || $data['address'] != $restaurant->address ){
            $restaurant->slug = Str::of(strtr( $data['name'].' '.$data['address'], $this->unwanted_array ))->slug('-');
        }

        if( isset($data['image_cover']) ) {
            Storage::delete($restaurant->image_cover);
            $path_image = Storage::put("uploads/restaurant_images",$data['image_cover']);
            $restaurant->image_cover = $path_image;
        }

        $restaurant->fill($data);

        $restaurant->save();

        if (isset($data["categories"])) {
            $restaurant->categories()->sync($data["categories"]);
        } else {
            $restaurant->categories()->detach();
        }

        return redirect()->route('restaurants.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Restaurant $restaurant)
    {
        foreach ($restaurant->dishes as $dish) {
            Storage::delete($dish->image);
        }

        if($restaurant->image_cover){
            Storage::delete($restaurant->image_cover);
        }

        $restaurant->delete();

        return redirect()->route('restaurants.index');
    }
}
