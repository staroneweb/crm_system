<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;


class LeadController extends Controller
{
    // List all leads with search and pagination
    public function index(Request $request)
    {
        try {
            $query = Lead::with(['contact:id,first_name,last_name', 'assignedUser:id,name']); // Eager load contact & assigned user

            // Apply search filters
            if ($request->has('source')) {
                $query->where('source', 'LIKE', "%{$request->source}%");
            }

            if ($request->has('stage')) {
                $query->where('stage', $request->stage);
            }

            if ($request->has('assigned_to')) {
                $query->where('assigned_to', $request->assigned_to);
            }

            $leads = $query->paginate(10);


            return response()->json([
                'status' => 200,
                'message' => 'Leads fetched successfully',
                'leads' => collect($leads->items())->map(function ($lead) {
                    return [
                        'id' => $lead->id,
                        'contact_name' => $lead->contact->first_name . " " . $lead->contact->last_name ?? null,
                        'source' => $lead->source,
                        'stage' => $lead->stage,
                        'value' => $lead->value,
                        //'assigned_to' => $lead->assigned_to,
                        'assigned_user_name' => $lead->assignedUser->name ?? null,
                        'created_at' => $lead->created_at ? $lead->created_at->format('d-M-Y H:i:s') : null, // Format DateTime
                        'updated_at' => $lead->updated_at ? $lead->updated_at->format('d-M-Y H:i:s') : null, // Format DateTime
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Error fetching leads', 'error' => $e->getMessage()]);
        }
    }


    // Store a new lead
    public function store(Request $request)
    {

       // dd($request);

        try {
            $validator=Validator::make($request->all(),[
                'contact_id' => 'required|exists:tbl_contacts,id',
                'source' => 'required|string|max:100',
                'stage' => 'required|in:new,qualified,proposal,won,lost',
                'value' => 'required|numeric',
                'assigned_to' => 'required|exists:tbl_users,id',
            ],
            [
                'contact_id.required' => 'The contact ID is required.', 
                'assigned_to.required' => 'The Assign User is required.',
            ]
            
        );

            if($validator->fails()){
                return response()->json(['status'=>422,'message'=>$validator->errors()]);
            }

            $lead = Lead::create($request->all());

            return response()->json(['status' => 200, 'message' => 'Lead created successfully', 'lead' => $lead]);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error creating lead', 'error' => $e->getMessage()]);
        }
    }

    // Show a single lead
    public function show($id)
    {
        try {
            $lead = Lead::find($id);
            if (!$lead) {
                return response()->json(['status' => 404, 'message' => 'Lead not found']);
            }
            return response()->json(['status' => 200, 'message' => 'Lead fetched successfully', 'lead' => $lead]);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error fetching lead', 'error' => $e->getMessage()]);
        }
    }

    //Lead Update 
    public function update(Request $request, $id)
    {
        try {
            $lead = Lead::find($id);
            if (!$lead) {
                return response()->json(['message' => 'Lead not found'], 404);
            }

            $request->validate([
                'contact_id' => 'sometimes|exists:tbl_contacts,id',
                'source' => 'nullable|string|max:100',
                'stage' => 'sometimes|in:new,qualified,proposal,won,lost',
                'value' => 'nullable|numeric',
                'assigned_to' => 'nullable|exists:tbl_users,id',
            ]);

            $lead->update($request->all());

            return response()->json(['status' => 200, 'message' => 'Lead updated successfully', 'lead' => $lead]);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error fetching lead', 'error' => $e->getMessage()]);
        }
    }




    // Delete a lead
    public function destroy($id)
    {
        try {
            $lead = Lead::find($id);
            if (!$lead) {
                return response()->json(['status' => 404, 'message' => 'Lead not found']);
            }
            $lead->delete();
            return response()->json(['status' => 200, 'message' => 'Lead deleted successfully']);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error deleting lead', 'error' => $e->getMessage()]);
        }
    }
}
