<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserBank;
use App\Models\UserKyc;
use App\Models\Category;
use App\Models\Commodity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
	public function languageList(Request $request){
		$languages = DB::table('languages')->get();
		if($languages){
			$responseArr['status'] = true;
			$responseArr['data'] = $languages;
			return response()->json($responseArr, Response::HTTP_OK);
		}
	}

	public function countryList(Request $request){
		$countries = DB::table('countries')->get();
		if($countries){
			$responseArr['status'] = true;
			$responseArr['data'] = $countries;
			return response()->json($responseArr, Response::HTTP_OK);
		}
	}

	public function categoryList(Request $request){
		$categories = Category::all();
		if($categories){
			$responseArr['status'] = true;
			$responseArr['data'] = $categories;
			return response()->json($responseArr, Response::HTTP_OK);
		}
	}

	public function subCategoryList(Request $request){
		$category_id = $request->input('category_id');
		$sub_categories = Category::where('parent', $category_id)->get();
		if($sub_categories){
			$responseArr['status'] = true;
			$responseArr['data'] = $sub_categories;
			return response()->json($responseArr, Response::HTTP_OK);
		}
	}

	public function commodityList(Request $request){
		$subcategory_id = $request->input('subcategory_id');
		$commodities = Commodity::where('subcategory_id', $subcategory_id)->get();
		if($commodities){
			$responseArr['status'] = true;
			$responseArr['data'] = $commodities;
			return response()->json($responseArr, Response::HTTP_OK);
		}
	}

	public function stateList(Request $request){
		$country_id = $request->input('country_id');
		$states = DB::table('states')->where('country_id', $country_id)->get();
		if($states){
			$responseArr['status'] = true;
			$responseArr['data'] = $states;
			return response()->json($responseArr, Response::HTTP_OK);
		}
	}
	/**
	 * select language
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function selectLanguage(Request $request){
		$post = $request->all();
		$validator = Validator::make($request->all(), [
			'language_id' => 'required|integer|digits:10',
		]);

		if ($validator->fails()) {
			$responseArr['status'] = false;
			$responseArr['message'] = $validator->errors();;
			$responseArr['token'] = '';
			return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
		}

		$data['language_id'] = $post['language_id'];
		$data['device_id']   = isset($post['device_id']) ? $post['device_id'] : '';
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		$user_id = DB::table('users')->insertGetId($data);
		if($user_id){
			$responseArr['status'] = true;
			$responseArr['message'] = 'Sucessfully';
			$responseArr['data'] = ['user_otp_id'=>$user_otp_id];
			$responseArr['token'] = '';
			return response()->json($responseArr, Response::HTTP_CREATED);
		}else{
			$responseArr['status'] = false;
			$responseArr['message'] = Response::HTTP_INTERNAL_SERVER_ERROR;
			$responseArr['token'] = '';
			return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	/**
	 * send a otp to the user mobile.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function sendOTP(Request $request)
	{
		$post = $request->all();
		$validator = Validator::make($request->all(), [
			'user_mobile' => 'required|integer|digits:10',
		]);
		$message = json_decode(json_encode($validator->errors()));
		// echo "<pre>";print_r($message->user_mobile[0]);die;
		if ($validator->fails()) {
			$responseArr['status'] = false;
			$responseArr['message'] = $message->user_mobile[0];
			$responseArr['token'] = '';
			return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
		}

		$data['user_mobile'] = $post['user_mobile'];
		$data['otp_code'] = 123456;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		$user_otp_id = DB::table('user_otp')->insertGetId($data);
		if($user_otp_id){
			$responseArr['status'] = true;
			$responseArr['message'] = 'Sucessfully';
			$responseArr['data'] = ['user_otp_id'=>$user_otp_id];
			$responseArr['token'] = '';
			return response()->json($responseArr, Response::HTTP_CREATED);
		}else{
			$responseArr['status'] = false;
			$responseArr['message'] = Response::HTTP_INTERNAL_SERVER_ERROR;
			$responseArr['token'] = '';
			return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	/**
	 * resend a otp to the user mobile.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function resendOTP(Request $request)
	{
		$post = $request->all();
		$validator = Validator::make($request->all(), [
			'user_otp_id' => 'required|integer',
		]);

		if ($validator->fails()) {
			$responseArr['status'] = false;
			$responseArr['message'] = $validator->errors();;
			$responseArr['token'] = '';
			return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
		}

		$user_otp = DB::table('user_otp')->where('user_otp_id', $post['user_otp_id'])->where('is_verify', 0)->first();

		if($user_otp){
			$data['otp_code']   = '123456';
			$data['updated_at'] = date('Y-m-d H:i:s');
			$res = DB::table('user_otp')->where('user_otp_id', $post['user_otp_id'])->where('is_verify', 0)->update($data);
			$responseArr['status'] = true;
			$responseArr['message'] = 'Sucessfully';
			$responseArr['data'] = ['user_otp_id'=>$user_otp->user_otp_id];
			$responseArr['token'] = '';
			return response()->json($responseArr, Response::HTTP_CREATED);
		}else{
			$responseArr['status'] = false;
			$responseArr['message'] = Response::HTTP_INTERNAL_SERVER_ERROR;
			$responseArr['token'] = '';
			return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	/**
	 * Verify auser mobile number otp.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function verifyOTP(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'user_otp_id' => 'required|integer',
			'otp_code' => 'required|integer',
		]);

		if ($validator->fails()) {
			$responseArr['status'] = false;
			$responseArr['message'] = $validator->errors();;
			$responseArr['token'] = '';
			return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
		}

		$post = $request->all();

		$user_otp = DB::table('user_otp')->where('user_otp_id', $post['user_otp_id'])->where('is_verify', 0)->first();
		
		if(!empty($user_otp)){
			if($user_otp->otp_code == $post['otp_code']){
				// $this->common_model->updateData('tbl_user_otp', ['user_otp_id'=>$user_otp_res->user_otp_id], ['is_verify'=>1]);
				$affected = DB::table('user_otp')
					->where('user_otp_id', $user_otp->user_otp_id)
					->update(['is_verify' => 1, 'updated_at'=>date('Y-m-d H:i:s')]);
				
				if($affected)
				{
					$user = User::where('mobile', $user_otp->user_mobile)->first();
					if(empty($user)){
						/*$user_data['mobile'] = $user_otp->user_mobile;
						$user_data['language_id'] = 1;
						$user = User::create($user_data);*/
						$responseArr['status'] = true;
						$responseArr['message'] = 'Sucessfully';
						$responseArr['is_new'] = true;
						$responseArr['data'] = $user;
						// $result = ['status' => true, 'code' => 201, 'message' => 'Sucessfully', 'data' => $user, 'is_new' => true];
						return response()->json($responseArr, Response::HTTP_OK);
						
					}else{
						$responseArr['status'] = true;
						$responseArr['message'] = 'Sucessfully';
						$responseArr['is_new'] = false;
						$responseArr['data'] = $user;
						return response()->json($responseArr, Response::HTTP_OK);
					}
				}else{
					$responseArr['status'] = false;
					$responseArr['message'] = 'Something went Wrong!';
					return response()->json($responseArr, Response::HTTP_UNAUTHORIZED);
				}
			}else{
				$responseArr['status'] = false;
				$responseArr['message'] = 'OTP code Not matched!';
				return response()->json($responseArr, Response::HTTP_UNAUTHORIZED);
			}
		}else{
			$responseArr['status'] = false;
			$responseArr['message'] = 'No record found';
			return response()->json($responseArr, Response::HTTP_NOT_FOUND);
		}
	}
	/**
	 * Rewgister a user of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function registerUser(Request $request)
	{
		$post = $request->all();
		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'mobile' => 'required|integer|digits:10',
			'pincode' => 'required',
			'country_id' => 'required',
			'state_id' => 'required',
			'city' => 'required',
		]);

		if ($validator->fails()) {
			$responseArr['status'] = false;
			$responseArr['message'] = $validator->errors();;
			return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
		}

		$user = User::where('mobile', $post['mobile'])->first();
		
		if(empty($user)){
			$user_data['mobile'] = $post['mobile'];
			$user_data['role_id'] = 1;
			$user_data['assured_id'] = 1;
			$user_data['is_new'] = '0';
			$user = User::create($user_data);
			if(!empty($user)){
				$user_address['user_id'] = $user->id;
				$user_address['address'] = '';
				$user_address['land_area'] = '';
				$user_address['country_id'] = $post['country_id'];
				$user_address['state_id']   = $post['state_id'];
				$user_address['city']       = $post['city'];
				$user_address['district']   = $post['district'];
				$user_address['village_town'] = $post['village_town'];
				$user_address['house_number'] = $post['house_number'];
				$user_address['latitude']   = $post['latitude'];
				$user_address['longitude']  = $post['longitude'];
				$user_address = UserAddress::create($user_address);

				$responseArr['status'] = true;
				$responseArr['message'] = 'Sucessfully';
				$responseArr['data'] = ['user' => $user, 'user_address' => $user_address];
				return response()->json($responseArr, Response::HTTP_OK);
			}else{
				$responseArr['status'] = false;
				$responseArr['message'] = 'registration failed';
				return response()->json($responseArr, Response::HTTP_FOUND);
			}
		}else{
			$responseArr['status'] = false;
			$responseArr['message'] = 'already registered';
			return response()->json($responseArr, Response::HTTP_FOUND);
		}
		// User::where('id', $user->id)->update($user_data);        
	}     
	
	public function getUserProfile(Request $request){
		$user = User::find($request->input('user_id'));
		$responseArr['status'] = true;
		$responseArr['message'] = 'Sucessfully';
		$responseArr['data'] = ["user" => $user, "user_address"=>$user->address->first()];
		return response()->json($responseArr, Response::HTTP_OK);
	}

	public function updateUserProfile(Request $request){
		$post = $request->all();
		$update_part = $request->input('update_part');
		$user_id = $request->input('user_id');

		switch ($update_part) {
			
			case "personal":
				$user = User::find($user_id);
				$user->name 		= $post['name'];
				$user->email 		= $post['email'];
				$user->category_id 	= $post['category_id'];
				$user->commodity_id = $post['commodity_id'];
				$user->language_id 	= $post['language_id'];
				
				$file       = $request->file('user_image');
				if($file){
	                $filename   = $file->getClientOriginalName();
	                $name       = "user_img";
	                $extension  = $file->extension();
	                $filenew    =  date('d-M-Y').'_'.str_replace($filename,$name,$filename).'_'.time().''.rand(). "." .$extension;
	                $file->move(base_path('/public/uploads/user_image'), $filenew);
	                $user->user_image   = asset('/uploads/user_image/'.$filenew);
				}
				// echo "<pre>";print_r($user);die;
				$user->save();
				$responseArr['status'] = true;
				$responseArr['message'] = 'Sucessfully';
				$responseArr['data'] = ["user" => $user];
				return response()->json($responseArr, Response::HTTP_OK);
			
			case "address":
				$user_address = UserAddress::where('user_id', $user_id)->first();
				$user_address->address 		= $post['address'];
				$user_address->land_area 	= $post['land_area'];
				$user_address->country_id 	= $post['country_id'];
				$user_address->state_id 	= $post['state_id'];
				$user_address->city 		= $post['city'];
				$user_address->district 	= $post['district'];
				$user_address->village_town = $post['village_town'];
				$user_address->house_number = $post['house_number'];
				$user_address->latitude 	= $post['latitude'];
				$user_address->longitude 	= $post['longitude'];
				$user_address->save();
				
				$responseArr['status'] = true;
				$responseArr['message'] = 'Sucessfully';
				$responseArr['data'] = ["user_address" => $user_address];
				return response()->json($responseArr, Response::HTTP_OK);
			
			case "kyc":
				$where = ['user_id'=>$user_id];
				$kyc_data['kyc_type'] = $post['kyc_type'];
				$kyc_data['card_number'] = $post['card_number'];
				if($file = $request->file('card_img')){
	                $filename   = $file->getClientOriginalName();
	                $name       = "card_img";
	                $extension  = $file->extension();
	                $filenew    =  date('d-M-Y').'_'.str_replace($filename,$name,$filename).'_'.time().''.rand(). "." .$extension;
	                $file->move(base_path('/public/uploads/user_kyc'), $filenew);
	                $kyc_data['card_img']   = asset('/uploads/user_kyc/'.$filenew);
				}
				$user_kyc = UserKyc::updateOrCreate($where, $kyc_data);
				$responseArr['status'] = true;
				$responseArr['message'] = 'Sucessfully';
				$responseArr['data'] = ["user_kyc" => $user_kyc];
				return response()->json($responseArr, Response::HTTP_OK);
			
			case "bank":
				$where = ['user_id'=>$user_id];
				// bank data 
				$bank_data['bank_name'] 	 = $post['bank_name'];
				$bank_data['bank_address']   = $post['bank_address'];
				$bank_data['account_number'] = $post['account_number'];
				$bank_data['account_owner']  = $post['account_owner'];
				if($file = $request->file('passbook_img')){
	                $filename   = $file->getClientOriginalName();
	                $name       = "passbook_img";
	                $extension  = $file->extension();
	                $filenew    =  date('d-M-Y').'_'.str_replace($filename,$name,$filename).'_'.time().''.rand(). "." .$extension;
	                $file->move(base_path('/public/uploads/user_bank'), $filenew);
	                $bank_data['passbook_img']   = asset('/uploads/user_bank/'.$filenew);
				}
				$user_bank = UserBank::updateOrCreate($where, $bank_data);
				$responseArr['status'] = true;
				$responseArr['message'] = 'Sucessfully';
				$responseArr['data'] = ["user_bank" => $user_bank];
				return response()->json($responseArr, Response::HTTP_OK);
			default:
				echo "default!";
		}
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function login(Request $request)
	{
		return 'login';
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function register(Request $request)
	{
		if(isset($_POST) && count($_POST) > 0){

			if(isset($_POST['contact']) && (!empty($_POST['contact']))){
				$users = new User();
				$uData = $users->where('mobile',$_POST['contact'])->first();
				if(!empty($uData)){
					// $users->otp = '123456';
					$updated = $users->where('mobile',$_POST['contact'])->update(['otp'=>'123456']);
					// dd($users);
					if($updated){
						$data = ['status' => true, 'code' => 200, 'message' => "Please Verify Otp", 'otp' => '1234'];    
					}else{
						$data = ['status' => false, 'code' => 201, 'message' => "Something Goes Wrong Please Try Again"];
					}
				}else{
					$users->mobile = $_POST['contact'];
					$users->otp = '123456';
					$saved = $users->save();
					if($saved){
						$data = ['status' => true, 'code' => 200, 'message' => "You're Sucessfully Registered", 'otp' => '1234'];    
					}else{
						$data = ['status' => false, 'code' => 201, 'message' => "Something Goes Wrong Please Try Again"];
					}
				}
				return $data;
			}else{
				$data = ['status' => false, 'code' => 201, 'message' => 'Please Enter Contact Number'];
				return $data;
			}
		}else{
			$data = ['status' => false, 'code' => 201, 'message' => 'Please Enter Contact Number'];
				return $data;
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function otpVerify(Request $request)
	{
		if(isset($_POST) && count($_POST) > 0){

			if(isset($_POST['otp']) && (!empty($_POST['otp']))){
				if((!isset($_POST['contact'])) && (empty($_POST['contact']))){
					$data = ['status' => false, 'code' => 201, 'message' => 'Please Enter Contact Number'];
				}else{
					$users = new User();
					$uData = $users->where('mobile',$_POST['contact'])->get();
					if(isset($uData) && (count($uData) > 0)){
						// $users->otp = '123456';
						$updated = $users->where('mobile',$_POST['contact'])->update(['otp'=>'123456']);
						if($updated){
							$data = ['status' => true, 'code' => 200, 'message' => "Please Verify Otp", 'otp' => '1234'];    
						}else{
							$data = ['status' => false, 'code' => 201, 'message' => "Something Goes Wrong Please Try Again"];
						}
					}else{
						$users->mobile = $_POST['contact'];
						$saved = $users->save();
						if($saved){
							$data = ['status' => true, 'code' => 200, 'message' => "You're Sucessfully Registered", 'otp' => '1234'];    
						}else{
							$data = ['status' => false, 'code' => 201, 'message' => "Something Goes Wrong Please Try Again"];
						}
					}  
				}
				
				return $data;
			}else{
				$data = ['status' => false, 'code' => 201, 'message' => 'Please Enter Otp'];
				return $data;
			}
		}else{
			$data = ['status' => false, 'code' => 201, 'message' => 'Please Enter Detail'];
				return $data;
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Users  $users
	 * @return \Illuminate\Http\Response
	 */
	public function show(Users $users)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Models\Users  $users
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Users $users)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Models\Users  $users
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Users $users)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Users  $users
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Users $users)
	{
		//
	}
}