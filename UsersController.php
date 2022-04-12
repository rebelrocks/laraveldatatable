<?php

namespace App\Http\Controllers\Admin\User;

use Helper;
use App\User;
use Carbon\Carbon;
use App\Models\ReportUser;
use App\Models\UserImage;
use App\Traits\UploadTrait;
use Illuminate\Http\Request;
use App\Models\Emailtemplate;
use Illuminate\Support\Facades\DB;
use App\Traits\GeneralsettingTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Traits\checkermissionsTrait;
use App\Http\Controllers\Controller;
use App\Models\Sociallink;
use App\Models\UserData;
use App\Traits\Sociallink\SociallinkTrait;
use Aws\Ses\SesClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    use UploadTrait, checkermissionsTrait,GeneralsettingTrait,SociallinkTrait;
    private $userprofiledir = "uploads/users";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     *
     * 
     * @return \Illuminate\Http\Response
     */

    
    public function index($type = null)
    {
        #DB::connection()->enableQueryLog();
        #$sql = "select a.* from users a where CONCAT(',','" . $user->pincode . "', ',') REGEXP CONCAT(',(', REPLACE(a.pincode, ',', '|'), '),') ";
        #$users = DB::select($sql);	
        #dd($users);
        #$queries = DB::getQueryLog();
        #dd($queries);
        if(!$this->checkPermission(Auth::user()->role_id, 'users', 'is_read'))
        {
            return redirect(route('admin_dashboard'))->with('warning', trans('messages.You are not authorised to access that location'));
            exit;
        }
        $user = Auth::user();
        if($type == 1)
        {
            $users = User::select('*')
                ->where('role','<',4)
                ->where('updated_at','!=','NULL')
                ->where('is_request',1)
                ->where(function($query) use($user){
                    if($user->role_id > 4){
                        
                        if(!empty($user->pincode)){
                            $query->orWhereRaw("CONCAT(',','" . $user->pincode . "', ',') REGEXP CONCAT(',(', REPLACE(pincode, ',', '|'), '),')");
                        }
                        if(!empty($user->block_id)){
                            $query->orWhereRaw("CONCAT(',','" . $user->block_id . "', ',') REGEXP CONCAT(',(', REPLACE(block, ',', '|'), '),')");
                        }
                        if(!empty($user->booth_id)){
                            $query->orWhereRaw("CONCAT(',','" . $user->booth_id . "', ',') REGEXP CONCAT(',(', REPLACE(booth_name, ',', '|'), '),')");
                        }
                        if(!empty($user->subblock_id)){
                            $query->orWhereRaw("CONCAT(',','" . $user->subblock_id . "', ',') REGEXP CONCAT(',(', REPLACE(subblock_id, ',', '|'), '),')");
                        }
                        if(!empty($user->block_member_ids)){
                            $query->orWhereRaw("CONCAT(',','" . $user->block_member_ids . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                        }
                        if(!empty($user->subblock_member_ids)){
                            $query->orWhereRaw("CONCAT(',','" . $user->subblock_member_ids . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                        }
                        if(!empty($user->booth_member_ids)){
                            $query->orWhereRaw("CONCAT(',','" . $user->booth_member_ids . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                        }
                        if(!empty($user->id)){
                            $query->orWhereRaw("CONCAT(',','" . $user->id . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                        }
                    }
                })
                ->latest()
                ->paginate(20);
        }
        elseif($type == 2)
        {
            $users = User::select('*')
                ->where('role','<',4)
                ->where('is_approved',0)
                ->where('updated_at','=',NULL)
                ->where(function($query) use($user){
                    if($user->role_id > 4){
                        
                        if(!empty($user->pincode)){
                            $query->orWhereRaw("CONCAT(',','" . $user->pincode . "', ',') REGEXP CONCAT(',(', REPLACE(pincode, ',', '|'), '),')");
                        }
                        if(!empty($user->block_id)){
                            $query->orWhereRaw("CONCAT(',','" . $user->block_id . "', ',') REGEXP CONCAT(',(', REPLACE(block, ',', '|'), '),')");
                        }
                        if(!empty($user->booth_id)){
                            $query->orWhereRaw("CONCAT(',','" . $user->booth_id . "', ',') REGEXP CONCAT(',(', REPLACE(booth_name, ',', '|'), '),')");
                        }
                        if(!empty($user->subblock_id)){
                            $query->orWhereRaw("CONCAT(',','" . $user->subblock_id . "', ',') REGEXP CONCAT(',(', REPLACE(subblock_id, ',', '|'), '),')");
                        }
                        if(!empty($user->block_member_ids)){
                            $query->orWhereRaw("CONCAT(',','" . $user->block_member_ids . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                        }
                        if(!empty($user->subblock_member_ids)){
                            $query->orWhereRaw("CONCAT(',','" . $user->subblock_member_ids . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                        }
                        if(!empty($user->booth_member_ids)){
                            $query->orWhereRaw("CONCAT(',','" . $user->booth_member_ids . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                        }
                        if(!empty($user->id)){
                            $query->orWhereRaw("CONCAT(',','" . $user->id . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                        }
                    }
                })
                ->latest()
                ->paginate(20);
        }
        elseif($type == 3)
        {
            $users = UserData::select('*')
                #->where('role','<',4)
                ->where('updated_at','!=','NULL')
                ->where(function($query) use($user){
                    if($user->role_id > 4){
                        
                        if(!empty($user->pincode)){
                            $query->orWhereRaw("CONCAT(',','" . $user->pincode . "', ',') REGEXP CONCAT(',(', REPLACE(pincode, ',', '|'), '),')");
                        }
                        if(!empty($user->block_id)){
                            $query->orWhereRaw("CONCAT(',','" . $user->block_id . "', ',') REGEXP CONCAT(',(', REPLACE(block, ',', '|'), '),')");
                        }
                        if(!empty($user->booth_id)){
                            $query->orWhereRaw("CONCAT(',','" . $user->booth_id . "', ',') REGEXP CONCAT(',(', REPLACE(booth_name, ',', '|'), '),')");
                        }
                        if(!empty($user->subblock_id)){
                            $query->orWhereRaw("CONCAT(',','" . $user->subblock_id . "', ',') REGEXP CONCAT(',(', REPLACE(subblock_id, ',', '|'), '),')");
                        }
                       
                    }
                })
                ->latest()
                ->paginate(20);
        }
        else
        { 
                
                $users = UserData::select('*')
				#->where('role','<',4)
                ->where('updated_at','=',NULL)
                ->where(function($query) use($user){
                    if($user->role_id > 4){
                        
                        if(!empty($user->pincode)){
                            $query->orWhereRaw("CONCAT(',','" . $user->pincode . "', ',') REGEXP CONCAT(',(', REPLACE(pincode, ',', '|'), '),')");
                        }
                        if(!empty($user->block_id)){
                            $query->orWhereRaw("CONCAT(',','" . $user->block_id . "', ',') REGEXP CONCAT(',(', REPLACE(block, ',', '|'), '),')");
                        }
                        if(!empty($user->booth_id)){
                            $query->orWhereRaw("CONCAT(',','" . $user->booth_id . "', ',') REGEXP CONCAT(',(', REPLACE(booth_name, ',', '|'), '),')");
                        }
                        if(!empty($user->subblock_id)){
                            $query->orWhereRaw("CONCAT(',','" . $user->subblock_id . "', ',') REGEXP CONCAT(',(', REPLACE(subblock_id, ',', '|'), '),')");
                        }
                        
                    }
                })
                ->latest()
                ->paginate(20);
                #dd($users);
                             
        }
        
        return view('admin.users.users', compact('users','type'));
    }
    
    public function users(Request $request){

        #Check permission access or not
        if(!$this->checkPermission(Auth::user()->role_id, 'users', 'is_read'))
        {
            return redirect(route('admin_dashboard'))->with('warning', trans('messages.You are not authorised to access that location'));
            exit;
        }
        $user= Auth::user();
        $type = $request->type;
        if(request()->ajax()) 
        {
            $draw = request('draw');
            $row = request('start');
            $rowperpage = request('length'); // Rows display per page
            $columnIndex = request('order')['0']['column']; // Column index
            $columnName = request('columns')[$columnIndex]['data']; // Column name
            $columnSortOrder = request('order')[0]['dir']; // asc or desc
            $searchValue = request('search')['value']; // Search value            
            
            
            if($type== 1 || $type == 3){
                $search_id = $request->columns[0]['search']['value'];
                $search_first_name = $request->columns[1]['search']['value'];
                $search_email = $request->columns[2]['search']['value'];
                $mobile = $request->columns[3]['search']['value'];
                $rate = $request->columns[4]['search']['value'];
                $block = $request->columns[5]['search']['value'];
                $booth = $request->columns[6]['search']['value'];
                $sub_area = $request->columns[7]['search']['value'];
                $active = $request->columns[8]['search']['value'];
              
            }else{
                $search_id = $request->columns[0]['search']['value'];
                $search_first_name = $request->columns[1]['search']['value'];
                $search_email = $request->columns[2]['search']['value'];
                $mobile = $request->columns[3]['search']['value'];
                $block = $request->columns[4]['search']['value'];
                $booth = $request->columns[5]['search']['value'];
                $sub_area = $request->columns[6]['search']['value'];
                $active = $request->columns[7]['search']['value'];
                $rate = '';
            }
          
            $conditions=array();
            if(!empty($search_id))
            {
                $conditions[] = ['voter_id_number', '=', "$search_id"];
            }
            if(!empty($search_first_name))
            {
                $conditions[] = ['first_name', 'like', "%$search_first_name%"];
            }
            if(!empty($search_last_name))
            {
                $conditions[] = ['last_name', 'like', "%$search_last_name%"];
            }
            if(!empty($search_email))
            {
                $conditions[] = ['house_number', 'like', "$search_email%"];
               
            }
            if(!empty($mobile))
            {
                $conditions[] = ['mobile', 'like', "%$mobile%"];
            }
		
            if(!empty($rate))
            {
                $conditions[] = ['rating', 'like', "%$rate%"];
            }
		
            if(!empty($block))
            {
                $conditions[] = ['block', 'like', "%$block%"];
            }
		
            if(!empty($booth))
            {
                $conditions[] = ['booth_name', 'like', "%$booth%"];
            }
		
            if(!empty($sub_area))
            {
                $conditions[] = ['part_number', 'like', "%$sub_area%"];
            }
            
            if(!empty($active))
            {
                if($active > 0){
                   $conditions[] = ['status', '=',$active]; 
                }else{
                   $conditions[] = ['status', '=', 0]; 
                }
                
            }
            if(!empty($search_email))
            {
                $columnName='house_number'; $columnSortOrder= 'ASC';
            }
		
            #dd($columnName,$columnSortOrder);
            switch($type){
                case 1:
                    $empQuery = User::select('*')
                    ->whereIn('role_id',[1,2])
                    ->where($conditions)
                    ->offset($row)
                    ->limit($rowperpage)
                    ->where('is_request',1)
                    ->where('updated_at','!=','NULL')
                    ->where(function($query) use($user){
                        if($user->role_id > 4){
                            
                            if(!empty($user->pincode)){
                                $query->orWhereRaw("CONCAT(',','" . $user->pincode . "', ',') REGEXP CONCAT(',(', REPLACE(pincode, ',', '|'), '),')");
                            }
                            if(!empty($user->block_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->block_id . "', ',') REGEXP CONCAT(',(', REPLACE(block, ',', '|'), '),')");
                            }
                            if(!empty($user->booth_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->booth_id . "', ',') REGEXP CONCAT(',(', REPLACE(booth_name, ',', '|'), '),')");
                            }
                            if(!empty($user->subblock_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->subblock_id . "', ',') REGEXP CONCAT(',(', REPLACE(subblock_id, ',', '|'), '),')");
                            }
                            if(!empty($user->block_member_ids)){
                                $query->orWhereRaw("CONCAT(',','" . $user->block_member_ids . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                            }
                            if(!empty($user->subblock_member_ids)){
                                $query->orWhereRaw("CONCAT(',','" . $user->subblock_member_ids . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                            }
                            if(!empty($user->booth_member_ids)){
                                $query->orWhereRaw("CONCAT(',','" . $user->booth_member_ids . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                            }
                            if(!empty($user->id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->id . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                            }
                        }
                    })
                    ->orderebycoloumn($columnName, $columnSortOrder);    
                    $totalRecords = DB::table('users')
                    ->whereIn('role_id',[1])
                    ->where('is_request',1)
                    ->where($conditions)
                    ->where('updated_at','!=','NULL')
                    ->where(function($query) use($user){
                        if($user->role_id > 4){
                            
                            if(!empty($user->pincode)){
                                $query->orWhereRaw("CONCAT(',','" . $user->pincode . "', ',') REGEXP CONCAT(',(', REPLACE(pincode, ',', '|'), '),')");
                            }
                            if(!empty($user->block_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->block_id . "', ',') REGEXP CONCAT(',(', REPLACE(block, ',', '|'), '),')");
                            }
                            if(!empty($user->booth_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->booth_id . "', ',') REGEXP CONCAT(',(', REPLACE(booth_name, ',', '|'), '),')");
                            }
                            if(!empty($user->subblock_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->subblock_id . "', ',') REGEXP CONCAT(',(', REPLACE(subblock_id, ',', '|'), '),')");
                            }
                            if(!empty($user->block_member_ids)){
                                $query->orWhereRaw("CONCAT(',','" . $user->block_member_ids . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                            }
                            if(!empty($user->subblock_member_ids)){
                                $query->orWhereRaw("CONCAT(',','" . $user->subblock_member_ids . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                            }
                            if(!empty($user->booth_member_ids)){
                                $query->orWhereRaw("CONCAT(',','" . $user->booth_member_ids . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                            }
                            if(!empty($user->id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->id . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                            }
                        }
                    })->count();
                break;
                case 2:
                    $empQuery = User::select('*')
                    ->whereIn('role_id',[1,2])
                    ->where($conditions)
                    ->offset($row)
                    ->limit($rowperpage)
                    ->where('is_request',1)
                    ->where('updated_at','=',NULL)
                    ->where('is_approved',0)
                    ->where(function($query) use($user){
                        if($user->role_id > 4){
                            
                            if(!empty($user->pincode)){
                                $query->orWhereRaw("CONCAT(',','" . $user->pincode . "', ',') REGEXP CONCAT(',(', REPLACE(pincode, ',', '|'), '),')");
                            }
                            if(!empty($user->block_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->block_id . "', ',') REGEXP CONCAT(',(', REPLACE(block, ',', '|'), '),')");
                            }
                            if(!empty($user->booth_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->booth_id . "', ',') REGEXP CONCAT(',(', REPLACE(booth_name, ',', '|'), '),')");
                            }
                            if(!empty($user->subblock_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->subblock_id . "', ',') REGEXP CONCAT(',(', REPLACE(subblock_id, ',', '|'), '),')");
                            }
                            if(!empty($user->block_member_ids)){
                                $query->orWhereRaw("CONCAT(',','" . $user->block_member_ids . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                            }
                            if(!empty($user->subblock_member_ids)){
                                $query->orWhereRaw("CONCAT(',','" . $user->subblock_member_ids . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                            }
                            if(!empty($user->booth_member_ids)){
                                $query->orWhereRaw("CONCAT(',','" . $user->booth_member_ids . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                            }
                            if(!empty($user->id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->id . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                            }
                        }
                    })
                    ->orderebycoloumn($columnName, $columnSortOrder);    
                    $totalRecords = DB::table('users')
                    ->where('is_request',1)
                    ->where('updated_at','=',NULL)
                    ->where('is_approved',0)
                    ->whereIn('role_id',[1,2])
                    ->where($conditions)
                    ->where(function($query) use($user){
                        if($user->role_id > 4){
                            
                            if(!empty($user->pincode)){
                                $query->orWhereRaw("CONCAT(',','" . $user->pincode . "', ',') REGEXP CONCAT(',(', REPLACE(pincode, ',', '|'), '),')");
                            }
                            if(!empty($user->block_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->block_id . "', ',') REGEXP CONCAT(',(', REPLACE(block, ',', '|'), '),')");
                            }
                            if(!empty($user->booth_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->booth_id . "', ',') REGEXP CONCAT(',(', REPLACE(booth_name, ',', '|'), '),')");
                            }
                            if(!empty($user->subblock_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->subblock_id . "', ',') REGEXP CONCAT(',(', REPLACE(subblock_id, ',', '|'), '),')");
                            }
                            if(!empty($user->block_member_ids)){
                                $query->orWhereRaw("CONCAT(',','" . $user->block_member_ids . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                            }
                            if(!empty($user->subblock_member_ids)){
                                $query->orWhereRaw("CONCAT(',','" . $user->subblock_member_ids . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                            }
                            if(!empty($user->booth_member_ids)){
                                $query->orWhereRaw("CONCAT(',','" . $user->booth_member_ids . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                            }
                            if(!empty($user->id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->id . "', ',') REGEXP CONCAT(',(', REPLACE(created_by, ',', '|'), '),')");
                            }
                        }
                    })
                    ->count();
                break;
                case 3:
                    $empQuery = UserData::select('*')
                    #->whereIn('role_id',[1,2])
                    ->where($conditions)
                    ->offset($row)
                    ->limit($rowperpage)
                    #->where('is_approved',0)
                    ->where('updated_at','!=','NULL')
                    ->where(function($query) use($user){
                        if($user->role_id > 4){
                            
                            if(!empty($user->pincode)){
                                $query->orWhereRaw("CONCAT(',','" . $user->pincode . "', ',') REGEXP CONCAT(',(', REPLACE(pincode, ',', '|'), '),')");
                            }
                            if(!empty($user->block_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->block_id . "', ',') REGEXP CONCAT(',(', REPLACE(block, ',', '|'), '),')");
                            }
                            if(!empty($user->booth_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->booth_id . "', ',') REGEXP CONCAT(',(', REPLACE(booth_name, ',', '|'), '),')");
                            }
                            if(!empty($user->subblock_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->subblock_id . "', ',') REGEXP CONCAT(',(', REPLACE(subblock_id, ',', '|'), '),')");
                            }
                            
                        }
                    })
                    ->orderBy($columnName, $columnSortOrder)->get();    
                    $totalRecords = DB::table('users_data')
                    #->where('is_approved',0)
                    #->whereIn('role_id',[1,2])
                    ->where('updated_at','!=','NULL')
                    ->where($conditions)
                    ->where(function($query) use($user){
                        if($user->role_id > 4){
                            
                            if(!empty($user->pincode)){
                                $query->orWhereRaw("CONCAT(',','" . $user->pincode . "', ',') REGEXP CONCAT(',(', REPLACE(pincode, ',', '|'), '),')");
                            }
                            if(!empty($user->block_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->block_id . "', ',') REGEXP CONCAT(',(', REPLACE(block, ',', '|'), '),')");
                            }
                            if(!empty($user->booth_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->booth_id . "', ',') REGEXP CONCAT(',(', REPLACE(booth_name, ',', '|'), '),')");
                            }
                            if(!empty($user->subblock_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->subblock_id . "', ',') REGEXP CONCAT(',(', REPLACE(subblock_id, ',', '|'), '),')");
                            }
                            
                        }
                    })
                    ->count();
                break;
                default:
                   
                    $empQuery = UserData::select('*')
                    #->whereIn('role_id',[1,2])
                    ->where($conditions)
                    ->offset($row)
                    ->limit($rowperpage)
                    ->where('updated_at','=',NULL)
                    ->where(function($query) use($user){
                        if($user->role_id > 4){
                            
                            if(!empty($user->pincode)){
                                $query->orWhereRaw("CONCAT(',','" . $user->pincode . "', ',') REGEXP CONCAT(',(', REPLACE(pincode, ',', '|'), '),')");
                            }
                            if(!empty($user->block_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->block_id . "', ',') REGEXP CONCAT(',(', REPLACE(block, ',', '|'), '),')");
                            }
                            if(!empty($user->booth_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->booth_id . "', ',') REGEXP CONCAT(',(', REPLACE(booth_name, ',', '|'), '),')");
                            }
                            if(!empty($user->subblock_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->subblock_id . "', ',') REGEXP CONCAT(',(', REPLACE(subblock_id, ',', '|'), '),')");
                            }
                            
                        }
                    })
                    ->orderBy($columnName, $columnSortOrder)->get();   
                    
                    
                    $totalRecords = DB::table('users_data')
                    ->where('updated_at','=',NULL)
                    #->whereIn('role_id',[1,2])
                    ->where($conditions)
                    ->where(function($query) use($user){
                        if($user->role_id > 4){
                            
                            if(!empty($user->pincode)){
                                $query->orWhereRaw("CONCAT(',','" . $user->pincode . "', ',') REGEXP CONCAT(',(', REPLACE(pincode, ',', '|'), '),')");
                            }
                            if(!empty($user->block_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->block_id . "', ',') REGEXP CONCAT(',(', REPLACE(block, ',', '|'), '),')");
                            }
                            if(!empty($user->booth_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->booth_id . "', ',') REGEXP CONCAT(',(', REPLACE(booth_name, ',', '|'), '),')");
                            }
                            if(!empty($user->subblock_id)){
                                $query->orWhereRaw("CONCAT(',','" . $user->subblock_id . "', ',') REGEXP CONCAT(',(', REPLACE(subblock_id, ',', '|'), '),')");
                            }
                           
                        }
                    })
                    ->count();
                break;
            }
                             
            #dd($empQuery);
            $data = array();
            $are_you_sure_want_to_delete = "'".trans('messages.are_you_sure_want_to_delete')."'";
            $change_status = "'".trans('messages.change_status')."'";
            
            if(count($empQuery))
            {
                foreach($empQuery as $rk1 => $rv1)
                {
                   
                    /*if(empty($type) || $type == 3){
                        $data[$rk1]['id'] = $rv1->user_id;
                    }else{
                        $data[$rk1]['id'] = $rv1->id;
                    }*/
                    $data[$rk1]['voter_id_number'] = $rv1->voter_id_number;
                     
                    $data[$rk1]['first_name'] = $this->blankIfNull($rv1->first_name);
                    $data[$rk1]['last_name'] = $this->blankIfNull($rv1->last_name);
                    $data[$rk1]['house_number'] = $this->blankIfNull($rv1->house_number);
                    $data[$rk1]['mobile'] = $this->blankIfNull($rv1->mobile);
                    if($type == 3 || $type == 1){
                        $data[$rk1]['rating'] = $rv1->rating ?? 0;
                    }
                    $data[$rk1]['block'] = $this->blankIfNull($rv1->block);
                    $data[$rk1]['booth_name'] = $this->blankIfNull($rv1->booth_name);
                    $data[$rk1]['part_number'] = $this->blankIfNull($rv1->part_number);
                    
                    $status = trans('messages.statusinactive');
                    //$change_status = trans('messages.change_status');
                    $status_class = 'danger';
                    if($rv1->status){
                        $status = trans('messages.statusactive');
                        $status_class = 'success';
                    }

                    
                    if(empty($type) || $type == 3){
                        $data[$rk1]['status'] = '<a style="color:white;" id="atag'.$rv1->user_id.'" class="btn btn-'.$status_class.'" data-id="'.$rv1->user_id.'" data-status="'.$rv1->status.'" onclick="updateuserStatus(this)" >'.$status.'</a>';
                    }else{
                       $data[$rk1]['status'] = '<a style="color:white;" id="atag'.$rv1->id.'" class="btn btn-'.$status_class.'" data-id="'.$rv1->id.'" data-status="'.$rv1->status.'" onclick="updateuserStatus(this)" >'.$status.'</a>';
                    }
                    
                    $actions = '';
                    
                    if(empty($type) || $type == 3){
                        $actions .= '<a href="'.route('viewuser', $rv1->user_id).'" class="btn btn-success"><i class="material-icons">visibility</i>Details </a>&nbsp;';
                    
                        $actions .= '<a href="'.route('edituser', $rv1->user_id).'" class="btn btn-warning"><i class="material-icons">border_color</i>Edit </a>&nbsp;';
                        
                        $actions .= '<a href="'.route('delete_user', $rv1->user_id).'" class="btn btn-danger" onclick="return confirm('.$are_you_sure_want_to_delete.')"><i class="material-icons">clear</i>Delete</a>'; 
                    }else{
                        $actions .= '<a href="'.route('viewuser', $rv1->id).'" class="btn btn-success"><i class="material-icons">visibility</i>Details </a>&nbsp;';
                    
                        $actions .= '<a href="'.route('edituser', $rv1->id).'" class="btn btn-warning"><i class="material-icons">border_color</i>Edit </a>&nbsp;';
                        
                        $actions .= '<a href="'.route('delete_user', $rv1->id).'" class="btn btn-danger" onclick="return confirm('.$are_you_sure_want_to_delete.')"><i class="material-icons">clear</i>Delete</a>'; 
                    }
                                        
                    $data[$rk1]['action'] = $actions;
                }
            }

            ## Total number of record with filtering
            if(empty($type) && empty($searchValue))
            {
                $totalRecordwithFilter = User::whereIn('role_id',[1,2])->where($conditions)->count();
            }
            elseif(empty($type) && !empty($searchValue))
            {
                $totalRecordwithFilter = User::whereIn('role_id',[1,2])->where('first_name', 'like', '%'.$searchValue.'%')->count();
            }
            else
            {
                $totalRecordwithFilter = User::where('role_id',$type)->where($conditions)->count();
            }

            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecordwithFilter,
                "iTotalDisplayRecords" => $totalRecords,
                "aaData" => $data
            );            
            echo json_encode($response);
            exit(); 
        }
    }
	
	public function getTotalReportUsers(){
		$user_id=request('user_id');
		
        $builder = ReportUser::query();
        $builder->select('report_users.*','users.first_name','users.last_name');
        $builder->join('users','users.id','=','report_users.reported_by');
        $builder->where('report_users.user_id', $user_id); 
        $builder->orderBy('report_users.created_at','DESC');      
        $data=$builder->get()->toArray();
        echo json_encode(array('status'=>true,'data'=>$data));
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!$this->checkPermission(Auth::user()->role_id, 'users', 'is_edit'))
        {
            return redirect(route('admin_dashboard'))->with('warning', trans('messages.You are not authorised to access that location'));
            exit;
        }        
        
        $user = $this->getuserDetail($id);
        
        if(!$user)
        {
            return back()->with('warning', trans('messages.user_detail_not'));
        }
        // dd($user);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        //Check permission access or not
        if(!$this->checkPermission(Auth::user()->role_id, 'users', 'is_edit'))
        {
            return redirect(route('admin_dashboard'))->with('warning', trans('messages.You are not authorised to access that location'));
            exit;
        }
        
        $user = $this->getuserDetail($id);

        if(!$user)
        {
            return back()->with('warning', trans('messages.user_detail_not'));
        }
        
        $request->validate([
            'mobile' => 'required',
            'email'=>'nullable|email|unique:users,email,'.$id,
            'first_name' => 'required|string|max:191',
            'rating' => 'required|numeric|min:1|max:10',            
        ]);

        $email = request('email') ? request('email') : '';
        $mobile = request('mobile') ? request('mobile') : '';
        $last_name = request('last_name') ? request('last_name'): '';
        $first_name = request('first_name') ? request('first_name'): '';
        $dob = ($request->dob) ? date('Y-m-d',strtotime($request->dob)) : '0000-00-00';
        $update = User::where('id', $id)->update([
            'email'                      => $email,
            'role'                       =>1,
            'role_id'                    =>1,
            'status'                     =>$request->status ? $request->status : 0,
            'is_approved'                =>$request->is_approved ? $request->is_approved : 0,
            'mobile'                     => $mobile,
            'dob'                        =>$dob,
            'last_name'                  => $last_name,
            'first_name'                 => $first_name,
            'address'                    => request('address') ?? '',
            'house_number'               => request('house_number') ?? '',
            'lane'                       => request('lane') ?? '',
            'city'                       => request('city') ?? '',
            'state'                      => request('state') ?? '',
            'block'                      => request('block') ?? '', 
            'booth_name'                 => request('booth_name') ?? '', 
            'sub_area'                   => request('sub_area') ?? '', 
            'assembly_constituency'      => request('assembly_constituency') ?? '', 
            'voter_id_number'            => request('voter_id_number') ?? '',
            'parliamentary_constituency' => request('parliamentary_constituency') ?? '',
            'part_number'                => request('part_number') ?? '',
            'part_name'                  => request('part_name') ?? '',
            'serial_number'              => request('serial_number') ?? '',
            'polling_station'            => request('polling_station') ?? '',
            'facebook_url'               => request('facebook_url') ?? '',
            'twitter_url'                => request('twitter_url') ?? '', 
            'remarks'                    => request('remark') ?? '', 
            'rating'                     => request('rating') ?? 0,
            'updated_at'                 => date('Y-m-d H:i:s')
        ]);

        if($update)
        {
            UserData::where('user_id', $id)->update([
            'updated_at' => date('Y-m-d H:i:s'),
            'rating'     => request('rating') ?? 0,
            ]);

            $this->UserActivity('Update activity performed on  user id :  '.$id,$id); 
            
            $this->SaveUserActivity('voters updated activity performed on user id '.$id); 
            $gallery_picture = $request->file('gallery_picture');
            
            if($request->hasFile('gallery_picture'))
            {
                $uplode_image_path = public_path().'/uploads/users/';
                foreach ($request->file('gallery_picture') as $fileKey => $fileObject )
                {
                    
                        if ($fileObject->isValid()) 
                        {
                            $photo  = $gallery_picture[$fileKey];
                            @$get_profile_image =  $this->uploadimageCompress($photo, $uplode_image_path);
                            $prev_image=$request->prev_image;
                            if(!empty($prev_image) && File::exists($uplode_image_path.'/'.$prev_image))
                            {
                                @unlink($uplode_image_path.'/'.$prev_image);
                            }
                            User::where('id', $id)->update([
                                'profile_pic' => $get_profile_image
                            ]);
                        }
                                       
                }
            }else{
                User::where('id', $id)->update([
                    'profile_pic' => $request->prev_image
                ]);  
            }

            if(!empty(request('password')) || request('email') != $user->email){
                
                #Send email if user email or Password Changed
                $template = Emailtemplate::where('id',3)->first();
                $sitesetting = $this->siteSettings();
                $social_links = $this->sitesocialLinks();
        
                $appname = $sitesetting['site.name'] ?? 'Voter Management Portal';
                $image_path = url('application/public/uploads/emailtemplates');
                $header_image = $image_path.'/'.$template->header_image;
                $white_logo = $image_path.'/'.$template->white_logo;
        
                $first_name  = $user->first_name ?? '';            
                
                #replace template var with value
                
                $emailFindReplace = array(
                    '##MAIN_COLOR##'  => '#'.$template->main_color,
                    '##WHITE_LOGO##' => $white_logo,
                    '##HEADER_IMAGE##' => $header_image,
                    '##FIRST_NAME##' => ucwords($first_name),
                    '##SITE_NAME##' => $appname,
                    '##EMAIL##' => $email,
                    '##NEWPASSWORD##' => request('password'),
                    '##SECONDARY_COLOR##' => '#'.$template->secondary_color,
                    '##FB_LINK##' => isset($social_links['EmailTemplate.fb_url']) ? '<a href="'.$social_links['EmailTemplate.fb_url'].'" target="_blank"><img alt="" height="54" src="'.url('content/socialicons/fb.png').'" width="54" /></a>' : '',
                    '##TWITTER_LINK##' => isset($social_links['EmailTemplate.twitter_url']) ? '<a href="'.$social_links['EmailTemplate.twitter_url'].'" target="_blank"><img alt="" height="54" src="'.url('content/socialicons/tw.png').'" width="54" /></a>' : '',
                    '##INSTA_LINK##' => isset($social_links['EmailTemplate.insta_url']) ? '<a href="'.$social_links['EmailTemplate.insta_url'].'" target="_blank"><img alt="" height="54" src="'.url('content/socialicons/it.png').'" width="54" /></a>' : '',
                    '##WEBSITE##' => isset($social_links['EmailTemplate.web_url']) ? '<a href="'.$social_links['EmailTemplate.web_url'].'" target="_blank"><img alt="" height="54" src="'.url('content/socialicons/wd.png').'" width="54" /></a>' : '',
                    '##LINKEDIN_LINK##' => isset($social_links['EmailTemplate.linked_in_url']) ? '<a href="'.$social_links['EmailTemplate.linked_in_url'].'" target="_blank"><img alt="" height="54" src="'.url('content/socialicons/linkedin.png').'" width="54" /></a>' : '',
                    '##CONTACT_EMAIL##' => $sitesetting['site.contact_email_address'] ?? '',
                    '##SITE_LINK##' => $sitesetting['site.url'] ?? '',
                    '##YEAR##' => date('Y'),	            
                );
        
                $from_mail = $sitesetting['site.sending_email_address'];
                $toEmail = $user->email;
                $content = strtr($template['description'], $emailFindReplace);
                $subject = str_replace('##SITE_NAME##', strtolower($appname), $template['subject']);
                if(!empty($toEmail))
                {
                    $data =array('templates'=>$content);
                    $status = Mail::send("emails.send_mail", $data, function ($message) use ($from_mail, $first_name, $toEmail, $subject) {
                        $message->to($toEmail, ucwords($first_name))
                            ->subject($subject);
                        $message->from($from_mail, $subject);
                    });
                }
                #End code for send mail 
              
            }


            return redirect(route('users'))->with('success', trans('messages.user_has_been_updated'));
        }

        return back()->with('warning', trans('messages.user_detail_not'));
    }


        /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($user_id)
    {
        #Check permission access or not
        if(!$this->checkPermission(Auth::user()->role_id, 'users', 'is_delete'))
        {
            return redirect(route('admin_dashboard'))->with('warning', trans('messages.You are not authorised to access that location'));
            exit;
        }
        $user = User::find($user_id);
        if(!$user)
        {
            return abort(404); 
        }
        
        $this->SaveUserActivity('voters delete activity performed on user id '.$user_id); 
        DB::table('users')->where('id', $user_id)->delete();
        DB::table('users_data')->where('user_id', $user_id)->delete();
        
        
        #sending mail to user for account deleted successfully.
        $template = Emailtemplate::where('id', 4)->first();
        $sitesetting = $this->siteSettings();
        $social_links = $this->sitesocialLinks();

        $appname = $sitesetting['site.name'] ?? 'Voters Management';
        $image_path = url('application/public/uploads/emailtemplates');
        $header_image = $image_path.'/'.$template->header_image;
        $white_logo = $image_path.'/'.$template->white_logo;

        $first_name  = $user->first_name ?? '';            
        
        #replace template var with value
        
        $emailFindReplace = array(
            '##MAIN_COLOR##'  => '#'.$template->main_color,
            '##WHITE_LOGO##' => $white_logo,
            '##HEADER_IMAGE##' => $header_image,
            '##FIRST_NAME##' => ucwords($first_name),
            '##SITE_NAME##' => $appname,
            '##SECONDARY_COLOR##' => '#'.$template->secondary_color,
            '##FB_LINK##' => isset($social_links['EmailTemplate.fb_url']) ? '<a href="'.$social_links['EmailTemplate.fb_url'].'" target="_blank"><img alt="" height="54" src="'.url('content/socialicons/fb.png').'" width="54" /></a>' : '',
            '##TWITTER_LINK##' => isset($social_links['EmailTemplate.twitter_url']) ? '<a href="'.$social_links['EmailTemplate.twitter_url'].'" target="_blank"><img alt="" height="54" src="'.url('content/socialicons/tw.png').'" width="54" /></a>' : '',
            '##INSTA_LINK##' => isset($social_links['EmailTemplate.insta_url']) ? '<a href="'.$social_links['EmailTemplate.insta_url'].'" target="_blank"><img alt="" height="54" src="'.url('content/socialicons/it.png').'" width="54" /></a>' : '',
            '##WEBSITE##' => isset($social_links['EmailTemplate.web_url']) ? '<a href="'.$social_links['EmailTemplate.web_url'].'" target="_blank"><img alt="" height="54" src="'.url('content/socialicons/wd.png').'" width="54" /></a>' : '',
            '##LINKEDIN_LINK##' => isset($social_links['EmailTemplate.linked_in_url']) ? '<a href="'.$social_links['EmailTemplate.linked_in_url'].'" target="_blank"><img alt="" height="54" src="'.url('content/socialicons/linkedin.png').'" width="54" /></a>' : '',
            '##CONTACT_EMAIL##' => $sitesetting['site.contact_email_address'] ?? '',
            '##SITE_LINK##' => $sitesetting['site.url'] ?? '',
            '##YEAR##' => date('Y'),	            
        );

        $from_mail = $sitesetting['site.sending_email_address'];
        $toEmail = $user->email;
        $content = strtr($template['description'], $emailFindReplace);
        $subject = str_replace('##SITE_NAME##', strtolower($appname), $template['subject']);
        if(!empty($toEmail))
        {
            $data =array('templates'=>$content);
            $status = Mail::send("emails.send_mail", $data, function ($message) use ($from_mail, $first_name, $toEmail, $subject) {
                $message->to($toEmail, ucwords($first_name))
                    ->subject($subject);
                $message->from($from_mail, $subject);
            });
        }
        
        return redirect()->back()->with('success', trans('messages.user_has_been_successfully_deleted'));
    }

    public function updatestatus(Request $request)
    {

        if($request->ajax())
        {
            //Check permission access or not
            if(!$this->checkPermission(Auth::user()->role_id, 'users', 'is_edit'))
            {
                return response()->json(['error' => trans('messages.You are not authorised to access that location')]);
                    exit();
            }

            $output = array('success' => '', 'error' => '');
            $status = request('status') ? false : true;
            $id = request('id');
            $update = User::where('id', $id)->update(['status' => $status]);
            UserData::where('user_id', $id)->update(['status' => $status]);
            if($update)
            {
                $output['success'] = trans('messages.status_updated_successfully');
            }
            else
            {
                $output['error'] = trans('messages.something_worng');  
            }
            $this->SaveUserActivity('voters status update activity performed on user id '.$id); 
            return response()->json($output);
        }
        
    }


    /**
     * Define user profile image upload path 
     */
    public function userprofiledirpath()
    {
        return $this->userprofiledir;
    }



    /**
     * upload user images
     * @param $user_id, $image_id, $image
     */
    private function uploaduserimages($user_id, $image, $image_id)
    {
        UserImage::create([
            'user_id' => $user_id,
            'image' => $image,
            'image_id' => $image_id,
            'status' => 1,
        ]);
    }

    /**
     * delete user images
     * @param $id, $image
     */
    private function deleteuserimages($id, $image)
    {
        $uplode_image_path = public_path(self::userprofiledirpath());

        if(!empty($image) && File::exists($uplode_image_path.'/'.$image))
        {
            unlink($uplode_image_path.'/'.$image);
        }

        UserImage::where('id', $id)->delete();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteuserimage($user_id, $id)
    {

        //Check permission access or not
        if(!$this->checkPermission(Auth::user()->role_id, 'users', 'is_delete'))
        {
            return redirect(route('admin_dashboard'))->with('warning', trans('messages.You are not authorised to access that location'));
            exit;
        }

        $user = User::find($user_id);
        if(!$user)
        {
            return abort(404);
        }

        $get_image_detail = UserImage::find($id);
        $uplode_image_path = public_path(self::userprofiledirpath());

        if(!empty($get_image_detail->image) && File::exists($uplode_image_path.'/'.$get_image_detail->image))
        {
            unlink($uplode_image_path.'/'.$get_image_detail->image);
        }

        UserImage::where('id', $id)->delete();

        return redirect()->back()->with('success', trans('messages.User image has been successfully deleted'));
    }


    public function reported_users()
    {
        $users = User::select('*')
        ->join('report_users', 'users.id', '=', 'report_users.user_id')
        ->where('users.role_id', 1)
        ->orWhere('users.role_id', 2)
        ->paginate(20);

        return view('admin.users.reported', compact('users'));

    }


    public function create(){
        if(!$this->checkPermission(Auth::user()->role_id, 'users', 'is_edit'))
        {
            return redirect(route('admin_dashboard'))->with('warning', trans('messages.You are not authorised to access that location'));
            exit;
        }        

        return view('admin.users.add');
    }

    public function store(Request $request)
    {
        
        //Check permission access or not
        if(!$this->checkPermission(Auth::user()->role_id, 'users', 'is_edit'))
        {
            return redirect(route('admin_dashboard'))->with('warning', trans('messages.You are not authorised to access that location'));
            exit;
        }
        
        $request->validate([
            'mobile' => 'required',
            'email' => 'nullable|unique:users,email',
            'first_name' => 'required|string|max:191',
            'rating' => 'required|numeric|min:1|max:10',
        ]);

        $email = request('email') ? request('email') : '';
        $mobile = request('mobile') ? request('mobile') : '';
        $last_name = request('last_name') ? request('last_name') : '';
        $first_name = request('first_name') ? request('first_name') : '';
        $dob = ($request->dob) ? date('Y-m-d',strtotime($request->dob)) : '0000-00-00';
        $create = User::create([
            'email'                      => $email,
            'role'                       =>1,
            'role_id'                    =>1,
            'is_request'                 =>1,
            'status'                     =>$request->status ? $request->status : 0,
            'is_approved'                =>$request->is_approved ? $request->is_approved : 0,
            'mobile'                     => $mobile,
            'dob'                        =>$dob,
            'last_name'                  => $last_name,
            'first_name'                 => $first_name,
            'address'                    => request('address') ?? '',
            'house_number'               => request('house_number') ?? '',
            'lane'                       => request('lane') ?? '',
            'city'                       => request('city') ?? '',
            'state'                      => request('state') ?? '',
            'block'                      => request('block') ?? '', 
            'booth_name'                 => request('booth_name') ?? '', 
            'sub_area'                   => request('sub_area') ?? '', 
            'assembly_constituency'      => request('assembly_constituency') ?? '', 
            'voter_id_number'            => request('voter_id_number') ?? '',
            'parliamentary_constituency' => request('parliamentary_constituency') ?? '',
            'part_number'                => request('part_number') ?? '',
            'part_name'                  => request('part_name') ?? '',
            'serial_number'              => request('serial_number') ?? '',
            'polling_station'            => request('polling_station') ?? '',
            'facebook_url'               => request('facebook_url') ?? '',
            'twitter_url'                => request('twitter_url') ?? '', 
            'remarks'                    => request('remark') ?? '',
            'created_by'                 => Auth::id(),
            'updated_at'                 => NULL,
            'rating'                     => request('rating') ?? 0,
            
        ]);

       
        if($create)
        {
            
            $this->UserActivity('Voter Create activity performed on  user id :  '.$create->id,$create->id); 
            
            $this->SaveUserActivity('voters request activity performed new user id is '.$create->id); 
            $gallery_picture = $request->file('gallery_picture');
            $gallery_id = request('gallery_id');

            if($request->hasFile('gallery_picture'))
            {
                $uplode_image_path = public_path().'/uploads/users/';
                foreach ($request->file('gallery_picture') as $fileKey => $fileObject )
                {
                    if ($fileObject->isValid()) 
                    {
                        $photo  = $gallery_picture[$fileKey];
                        @$get_profile_image =  $this->uploadimageCompress($photo, $uplode_image_path);

                        User::where('id', $create->id)->update([
                            'profile_pic' => $get_profile_image
                        ]);
                    }
                                   
                }
            }
            $user = User::find($create->id);
            #sending mail to user for account deleted successfully.
            $template = Emailtemplate::where('id', 1)->first();
            $sitesetting = $this->siteSettings();
            $social_links = $this->sitesocialLinks();

            $appname = $sitesetting['site.name'] ?? 'Voters Management';
            $image_path = url('application/public/uploads/emailtemplates');
            $header_image = $image_path.'/'.$template->header_image;
            $white_logo = $image_path.'/'.$template->white_logo;

            $first_name  = $user->first_name ?? '';            
            
            #replace template var with value
            $member = Auth::user();
            $emailFindReplace = array(
                '##MAIN_COLOR##'  => '#'.$template->main_color,
                '##WHITE_LOGO##' => $white_logo,
                '##HEADER_IMAGE##' => $header_image,
                '##FIRST_NAME##' => ucwords($first_name),
                '##MOBILE##' => $mobile,
                '##ADDRESS##' => $request->address,
                '##SITE_NAME##' => $appname,
                '##MEMBERNAME##' =>ucwords($member->first_name),
                '##MEMBEREMAIL##' =>$member->email,
                '##EMAIL##' => $email,
                '##SECONDARY_COLOR##' => '#'.$template->secondary_color,
                '##FB_LINK##' => isset($social_links['EmailTemplate.fb_url']) ? '<a href="'.$social_links['EmailTemplate.fb_url'].'" target="_blank"><img alt="" height="54" src="'.url('content/socialicons/fb.png').'" width="54" /></a>' : '',
                '##TWITTER_LINK##' => isset($social_links['EmailTemplate.twitter_url']) ? '<a href="'.$social_links['EmailTemplate.twitter_url'].'" target="_blank"><img alt="" height="54" src="'.url('content/socialicons/tw.png').'" width="54" /></a>' : '',
                '##INSTA_LINK##' => isset($social_links['EmailTemplate.insta_url']) ? '<a href="'.$social_links['EmailTemplate.insta_url'].'" target="_blank"><img alt="" height="54" src="'.url('content/socialicons/it.png').'" width="54" /></a>' : '',
                '##WEBSITE##' => isset($social_links['EmailTemplate.web_url']) ? '<a href="'.$social_links['EmailTemplate.web_url'].'" target="_blank"><img alt="" height="54" src="'.url('content/socialicons/wd.png').'" width="54" /></a>' : '',
                '##LINKEDIN_LINK##' => isset($social_links['EmailTemplate.linked_in_url']) ? '<a href="'.$social_links['EmailTemplate.linked_in_url'].'" target="_blank"><img alt="" height="54" src="'.url('content/socialicons/linkedin.png').'" width="54" /></a>' : '',
                '##CONTACT_EMAIL##' => $sitesetting['site.contact_email_address'] ?? '',
                '##SITE_LINK##' => $sitesetting['site.url'] ?? '',
                '##YEAR##' => date('Y'),	            
            );

            $from_mail = $sitesetting['site.sending_email_address'];
            $toEmail = $user->email;
            $content = strtr($template['description'], $emailFindReplace);
            $subject = str_replace('##SITE_NAME##', strtolower($appname), $template['subject']);
            if(!empty($toEmail))
            {
                $data =array('templates'=>$content);
                $status = Mail::send("emails.send_mail", $data, function ($message) use ($from_mail, $first_name, $toEmail, $subject) {
                    $message->to($toEmail, ucwords($first_name))
                        ->subject($subject);
                    $message->from($from_mail, $subject);
                });

                $status = Mail::send("emails.send_mail", $data, function ($message) use ($from_mail, $subject, $member) {
                    $message->to($member->email, ucwords($member->first_name))
                        ->subject($subject);
                    $message->from($from_mail, $subject);
                });

                $admin = User::find(1);
                $status = Mail::send("emails.send_mail", $data, function ($message) use ($from_mail, $subject, $admin) {
                    $message->to($admin->email, ucwords($admin->first_name))
                        ->subject($subject);
                    $message->from($from_mail, $subject);
                });
            }
                   

            return redirect(route('users'))->with('success', trans('messages.user_has_been_successfully_added'));
        }

        return back()->with('warning', trans('messages.user_detail_not'));
    }

    function generate_password($length = 8){
        $chars =  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.
                  '0123456789`-=~!@#$%^&*()_+,./<>?;:[]{}\|';
      
        $str = '';
        $max = strlen($chars) - 1;
      
        for ($i=0; $i < $length; $i++)
          $str .= $chars[random_int(0, $max)];
      
        return $str;
    }

    public function details($user_id){
        if(!$this->checkPermission(Auth::user()->role_id, 'users', 'is_read'))
        {
            return redirect(route('admin_dashboard'))->with('warning', trans('messages.You are not authorised to access that location'));
            exit;
        }        
        
        $user = $this->getuserDetail($user_id);
        $original_user = UserData::where('user_id',$user_id)->first();
        $activities=DB::table('user_data_activities');
        $activities->select('user_data_activities.*','users.first_name','users.last_name','roles.name');
        $activities->join('users','users.id','=','user_data_activities.user_id');	
        $activities->join('roles','roles.id','=','user_data_activities.role_id');
        $activities->latest();
        $activities->where('user_data_id',$user_id);
        if(Auth::user()->role_id > 4){
            $AuthUser=Auth::user()->role_id;
            $userRoles=[];
            if($AuthUser == 4){
                $userRoles=[4,13,14,15,16];
            }
            if($AuthUser == 13){
                $userRoles=[13,14,15,16]; 
            }
            if($AuthUser == 14){
                $userRoles=[14,15,16]; 
            }
            if($AuthUser == 15){
                $userRoles=[15,16]; 
            }
            $activities->whereIn('user_activities.role_id',$userRoles);
        }
        $activities = $activities->get();
        if(!$user)
        {
            return redirect(route('users'))->with('warning', trans('messages.user_detail_not'));
        }
        
        # get previous user id
        $previous = User::where('id', '<', $user_id)->where('role_id','<', 4)->max('id');

        # get next user id
        $next = User::where('id', '>', $user_id)->where('role_id','<', 4)->min('id');
        
        return view('admin.users.details', compact('user','original_user','activities','previous','next'));

    }

   
}
