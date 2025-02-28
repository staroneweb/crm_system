<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class ContactController extends Controller
{
    public function contactAdd(Request $request){

        try
          {

            $validator=Validator::make($request->all(),[
    
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:tbl_contacts',
                    'phone' => 'required|string|regex:/^\d{10}$/|unique:tbl_contacts',
                    'company'=>'required',
                    'created_by' => 'required',
                    'assigned_to' => 'required'
            ]);

            if($validator->fails()){
                return response()->json(['status'=>422,'message'=>$validator->errors()]);
            }

            $contact = new Contact();
            $contact->first_name=$request->first_name;
            $contact->last_name = $request->last_name;
            $contact->email= $request->email;
            $contact->phone = $request->phone;
            $contact->company = $request->company;
            $contact->created_by = $request->created_by;
            $contact->assigned_to = $request->assigned_to;

            if($contact->save()){

                return response()->json(['status'=>200,'message'=>'Contact created successfully!']);

            }else{

                return response()->json(['status'=>500,'message'=>'Contact created failed!']);

            }

        }catch(\Exception $e){

            return response()->json(['status' => 500, 'message' => 'An error occurred while contact add. Please try again later.']);

        }
    }
    public function contactEdit(Request $request){

       try{

            $contact_data = Contact::where('id',$request->contact_id)->first();

            if(!$contact_data){
                return response()->json(['status'=>500,'message'=>'Contact not found!']);
            }

            $data []=[

                    'first_name'=>$contact_data->first_name,
                    'last_name'=>$contact_data->last_name,
                    'email'=>$contact_data->email,
                    'phone'=>$contact_data->phone,
                    'company'=>$contact_data->company,
                    'status'=>$contact_data->status,
                    'created_by'=>$contact_data->created_by,
                    'assigned_to'=>$contact_data->assigned_to
            ];

            return response()->json(['status'=>200,'data'=>$data]);

       }catch(\Exception $e){

          return response()->json(['status' => 500, 'message' => 'An error occurred while contact edit. Please try again later.']);

       }
    }

    public function contactUpdate(Request $request){
        try{

            $contact_data = Contact::where('id',$request->contact_id)->first();

            if(!$contact_data){
                return response()->json(['status'=>500,'message'=>'Contact not found!']);
            }
            
            $validator=Validator::make($request->all(),[
    
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('tbl_contacts')->ignore($request->contact_id)
                ],
                'phone' => [
                    'required',
                    'string',
                    'regex:/^\d{10}$/',
                    Rule::unique('tbl_contacts')->ignore($request->contact_id),
                ],
                'company'=>'required',
                'created_by' => 'required',
                'assigned_to' => 'required'
            ]);

            if($validator->fails()){
                return response()->json(['status'=>422,'message'=>$validator->errors()]);
            }

            $contact_data->first_name=$request->first_name;
            $contact_data->last_name = $request->last_name;
            $contact_data->email= $request->email;
            $contact_data->phone = $request->phone;
            $contact_data->company = $request->company;
            $contact_data->status= $request->status;
            $contact_data->created_by = $request->created_by;
            $contact_data->assigned_to = $request->assigned_to;

            if($contact_data->save()){

                return response()->json(['status'=>200,'message'=>'Contact update successfully!']);

            }else{

                return response()->json(['status'=>500,'message'=>'Contact update failed!']);

            }
 
        }catch(\Exception $e){

            return response()->json(['status' => 500, 'message' => 'An error occurred while contact update. Please try again later.']);
 
        }
    }

     public function contactDelete(Request $request){
        try{

            $contact_data = Contact::where('id',$request->contact_id)->first();

            if(!$contact_data){
                return response()->json(['status'=>500,'message'=>'Contact not found!']);
            }

            if($contact_data->delete()){
                return response()->json(['status'=>200,'message'=>'Contact delete successfully!']);
            }
 
        }catch(\Exception $e){
 
            return response()->json(['status' => 500, 'message' => 'An error occurred while contact delete. Please try again later.']);

        }
    }

    // public function contactStatus(Request $request){
    //     try{

    //         $user_id = $request->user_id;

    //         $user_data = User::where('id', $user_id)->first();

    //         if (!$user_data) {

    //             return response()->json(['status' => 500, 'message' => 'User Not Found!']);
    //         }
    //         $user_data->status = $request->status;

    //         if ($user_data->save()) {

    //             return response()->json(['status' => 200, 'message' => 'User Status Change Successfully!']);
    //         }
 
    //     }catch(\Exception $e){
 
    //         return response()->json(['status' => 500, 'message' => 'An error occurred while contact status. Please try again later.']);
            
    //     }
    // }

    public function contactList(Request $request){

        try{

            $query=Contact::with(['assigned','createdby']);
            

            if(isset($request->search)){

                $query->where('first_name','like', '%' . $request->search . '%')
                    ->orwhere('last_name','like','%' . $request->search . '%')
                    ->orwhere('email','like','%' . $request->search . '%')
                    ->orwhere('phone','like','%' . $request->search . '%')
                    ->orwhere('company','like','%' . $request->search . '%')
                    ->orwhere('status','like','%' . $request->search . '%')
                    ->orwhereHas('assigned',function ($q) use($request){
                        $q->where('name','like','%'.$request->search.'%');
                     })
                     ->orwhereHas('createdby',function ($q) use($request){
                        $q->where('name','like','%'.$request->search.'%');
                    }
                 );

            }

            $contacts=$query->get();


            if(!$contacts){

                return response()->json(['status'=>200,'message'=>'Data Not Found!']);
            }

            $contact_data = [];

            foreach($contacts as $contact){

                 $contact_data[] = [

                    'id'=>$contact->id,
                    'first_name' => $contact->first_name,
                    'last_name' => $contact->last_name,
                    'email' => $contact->email,
                    'phone'=>$contact->phone,
                    'company'=>$contact->company,
                    'status'=> $contact->status,
                    'Assign User'=>$contact->assigned->name,
                    'Added By'=>$contact->createdby->name
                ];
            }

            return response()->json(['status'=>200,'data'=>$contact_data]);

 
        }catch(\Exception $e){
 
            return response()->json(['status' => 500, 'message' => 'An error occurred while contact list. Please try again later.']);
            
        }
    }
}
