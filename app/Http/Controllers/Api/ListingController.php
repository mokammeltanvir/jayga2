<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\User;
use App\Models\UserNid;
use App\Models\ListerNid;
use App\Models\ListingGuestAmenities;
use App\Models\ListingDescribe;
use App\Models\ListingRestrictions;
use App\Models\ListingImages;
use App\Models\FavListing;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class ListingController extends Controller
{
    public function listings(Request $request){
        $listing = Listing::where('isApproved', true)->where('isActive', true)->with('newAmenities.amenity')->with('newRestrictions.restrictions')->with('images')->get();
        if(count($listing)>0){
            return response()->json([
                'status' => 200,
                'Listings' => $listing
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'messege' => 'No Listing found'
            ], 404);
        }
    }


    public function filter(Request $request){
        $filtered_listing = QueryBuilder::for(Listing::class)->where('isApproved', true)->where('isActive', true)->allowedFilters(['guest_num', 'bed_num', 'district', 'town', 'allow_short_stay', 'listing_type'])->with('images')->with('newAmenities.amenity')->with('newRestrictions.restrictions')->with('reviews')->get();
        if(count($filtered_listing)>0){
            return response()->json([
                'status' => 200,
                'filtered_listing' => $filtered_listing
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'messege' => 'No filter result found'
            ],404);
        }
    }

    

    public function create(Request $request){

        $validated = $request->validate([
            'user_id' => 'required'
        ]);

        $check = Listing::where('listing_title', $request->input('listing_title'))->where('lister_id', $request->input('user_id'))->get();
        $user = User::where('id', $request->input('user_id'))->get();
        if(count($check)>0){
            return response()->json([
                'status' => false,
                'messege' => 'Listing title can not be same'
            ]);
        }else{

            if($validated){
                 Listing::create([
                    'lister_id' => $request->input('user_id'),
                    'lister_name' => $user[0]->name,
                    'guest_num' => $request->input('guest_num'),
                    'bed_num' => $request->input('bed_num'),
                    'bathroom_num' => $request->input('bathroom_num'),
                    'listing_title' => $request->input('listing_title'),
                    'listing_description' => $request->input('listing_description'),
                    'full_day_price_set_by_user' => $request->input('full_day_price_set_by_user'),
                    'listing_address' => $request->input('listing_address'),
                    'zip_code' => $request->input('zip_code'),
                    'district' => $request->input('district'),
                    'town' => $request->input('town'),
                    'allow_short_stay' => $request->input('allow_short_stay'),
                    
                    'lat' => $request->input('lati'),
                    'long' => $request->input('longi'),
                    'listing_type' => $request->input('listing_type'),
                    'video_link' => $request->input('video_link'),
                ]);

                
        
                $listing_id = Listing::where('listing_title', $request->input('listing_title'))->get();
              //  dd($check[0]);

             // $user = User::where('id', $request->input('user_id'))->get();

              if($user[0]->phone == null){
                $receipent = $user[0]->email;
                $subject = 'Listing Creation Under Review';

                 Mail::plain(
                    view: 'mailTemplates.ListingCreation',
                    data: [
                        'username' => $user[0]->name,
                        'listing_title' => $request->input('listing_title')
                    ],
                    callback: function (Message $message) use ($receipent, $subject) {
                        $message->to($receipent)->subject($subject);
                    }
                );

              }elseif($user[0]->email == null){
                $data = [
                    "sender_id" => "8809601010510",
                    "receiver" => $user[0]->phone,
                    "message" => 'Your listing : '. $request->input('listing_title') . ' is created and submitted to review',
                    "remove_duplicate" => true
                ];
                send_sms($data);
              }else{
                $receipent = $user[0]->email;
                $subject = 'Listing Creation Under Review';

                 Mail::plain(
                    view: 'mailTemplates.ListingCreation',
                    data: [
                        'username' => $user[0]->name,
                        'listing_title' => $request->input('listing_title')
                    ],
                    callback: function (Message $message) use ($receipent, $subject) {
                        $message->to($receipent)->subject($subject);
                    }
                );

                $data = [
                    "sender_id" => "8809601010510",
                    "receiver" => $user[0]->phone,
                    "message" => 'Your listing : '. $request->input('listing_title') . ' is created and submitted for review',
                    "remove_duplicate" => true
                ];
                send_sms($data);
              }

                

                return response()->json([
                    'status' => true,
                    'messege' => 'Listing created and submitted for review',
                    'listing_id' => [
                        'id' => $listing_id[0]->listing_id
                    ]
                 ]);
            }else{
                return $validated->errors();
            }
               
        }
                  
        


        
    }

    public function images(Request $request){
        $file = $request->file('listing_pictures');
        $validated = $request->validate([
            'listing_id' => 'required',
            'lister_id' => 'required',
            
        ]);
        if($validated){
            
            if(count($file)>0){
                
                foreach ($file as $f) {
                $path = $f->store('listings');
                ListingImages::create([
                    'listing_id' => $request->input('listing_id'),
                    'lister_id' => $request->input('lister_id'),
                    'listing_filename' => $f->hashName(),
                    'listing_targetlocation' => $path,
                ]);
                }
                return response()->json([
                    'status' => 200,
                    'messege' => 'Listing Pictures uploaded'
                ]);
            }else{
                return response()->json([
                    'status' => 404,
                    'messege' => 'No picture uploaded'
                ], 404);
            } 
        }else{
            return $validated->errors();
        }
    }

    public function listing_nid(Request $request){
        $file = $request->file('listing_nid');
        $validated = $request->validate([
            'listing_id' => 'required',
            'user_id' => 'required',
            
        ]);
        if($validated){
            
            if(count($file)>0){
                
                foreach ($file as $f) {
                $path = $f->store('listings-nid');
                ListerNid::create([
                    'listing_id' => $request->input('listing_id'),
                    'lister_id' => $request->input('user_id'),
                    'nid_filename' => $f->hashName(),
                    'nid_targetlocation' => $path,
                ]);
                }
                return response()->json([
                    'status' => 200,
                    'messege' => 'Listing Nid uploaded'
                ]);
            }else{
                return response()->json([
                    'status' => 404,
                    'messege' => 'No picture uploaded'
                ], 404);
            } 
        }else{
            return $validated->errors();
        }
    }

    public function profile_listings(Request $request, $id){
        $listings = Listing::where('lister_id', $id)->with('newAmenities.amenity')->with('newRestrictions.restrictions')->with('images')->get();
        if(count($listings)>0){
            return response()->json([
                'status' => 200,
                'profile_listings' => $listings
            ]);
        }else{
            return response()->json([
                'status' => 200,
                'messege' => 'No Listings Found'
            ]);
        }
    }

    public function update_listing(Request $request){
        Listing::where('listing_id', $request->input('listing_id'))->update($request->all());

        /* 
        'lister_id' => $request->input('user_id'),
            'lister_name' => $request->input('lister_name'),
            'guest_num' => $request->input('guest_num'),
            'bed_num' => $request->input('bed_num'),
            'bathroom_num' => $request->input('bathroom_num'),
            'listing_title' => $request->input('listing_title'),
            'listing_description' => $request->input('listing_description'),
            'full_day_price_set_by_user' => $request->input('full_day_price_set_by_user'),
            'listing_address' => $request->input('listing_address'),
            'zip_code' => $request->input('zip_code'),
            'district' => $request->input('district'),
            'town' => $request->input('town'),
            'allow_short_stay' => $request->input('allow_short_stay'),
            'describe_peaceful' => $request->input('describe_peaceful'),
            'describe_unique' => $request->input('describe_unique'),
            'describe_familyfriendly' => $request->input('describe_familyfriendly'),
            'describe_stylish' => $request->input('describe_stylish'),
            'describe_central' => $request->input('describe_central'),
            'describe_spacious' => $request->input('describe_spacious'),
            'lat' => $request->input('lati'),
            'long' => $request->input('longi'),
            'listing_type' => $request->input('listing_type'),
            'video_link' => $request->input('video_link'),
            */

        

        return response()->json([
            'status' => 200,
            'messege' => 'Listing updated'
        ]);
    }
    

    public function delete_image_listing(Request $request, $id){
        $img = ListingImages::where('listing_img_id', $id)->get();
        if(count($img)>0){
            Storage::delete($img[0]->listing_targetlocation);
            ListingImages::where('listing_img_id', $id)->delete();
            return response()->json([
                'status' => 200,
                'messege' => 'Listing image deleted'
            ]);
        }else{
            return response()->json([
                'status' => 200,
                'messege' => 'No image found'
            ]);
        }
        
    }

    public function get_listing_images(Request $request, $id){
       $images = ListingImages::where('listing_id', $id)->get();
        if(count($images)>0){
            return response()->json([
                'status' => 200,
                'listing_images' => $images
            ]);
        }else{
            return response()->json([
                'status' => 200,
                'messege' => 'No listing image found'
            ]);
        }
    }

    public function listing_status(Request $request){
        $validated = $request->validate([
            'listing_id' => 'required',
            'isActive' => 'required|boolean'
        ]);
        if($validated){
            Listing::where('listing_id', $request->input('listing_id'))->update([
                'isActive' => $request->input('isActive')
            ]);
            return response()->json([
                'status' => 200,
                'messege' => 'Listing Status Changed'
            ]);
        }else{
           $validated->errors();
        }
       
    }


    public function add_fav(Request $request){
        $validated = $request->validate([
            'user_id' => 'required',
            'listing_id' => 'required'
        ]);
        $check = FavListing::where('listing_id', $request->input('listing_id'))->where('user_id', $request->input('user_id'))->get();

        if(count($check)>0){
            return response()->json([
                'status' => 200,
                'messege' => 'Listing already exists'
            ]);
        }else{
            FavListing::create([
                'user_id' => $request->input('user_id'),
                'listing_id' => $request->input('listing_id'),
                'fav_type' => $request->input('fav_type')
            ]);
            return response()->json([
                'status' => 200,
                'messege' => 'Listing added to favourite'
            ]);
        }
      
    }

    public function get_fav(Request $request, $id){
        $favs = FavListing::where('user_id', $id)->with('listing.images')->with('listing.newAmenities.amenity')->with('listing.newRestrictions.restrictions')->get();
        return response()->json([
            'status' => 200,
            'Favourites' => $favs
        ]);
    }

    public function del_fav(Request $request, $id){
        $listing = FavListing::where('id', $id)->get();
        if(count($listing)>0){
            FavListing::where('id', $id)->delete();
         return response()->json([
            'status' => 200,
            'messege' => 'Favourite listing removed'
         ]);
        }else{
            return response()->json([
                'status' =>200,
                'messege' => 'Listing not found'
            ]);
        }
        
    }
}
