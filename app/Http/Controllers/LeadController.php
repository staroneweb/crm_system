<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class LeadController extends Controller
{
    // List all leads with search and pagination
    public function index(Request $request)
    {
        try {
            $query = Lead::with([
                'source:id,source_name', // Eager load lead source with name
                'status:id,status_name', // Eager load lead status with name
                'stage:id,stage_name', // Eager load lead status with name
                'assignedUser:id,name'
            ]);

            // Apply search filters
            if ($request->has('lead_source')) {
                $query->whereHas('source', function ($q) use ($request) {
                    $q->where('source_name', 'LIKE', "%{$request->lead_source}%");
                });
            }

            if ($request->has('lead_status')) {
                $query->whereHas('status', function ($q) use ($request) {
                    $q->where('status_name', 'LIKE', "%{$request->lead_status}%");
                });
            }

            if ($request->has('lead_stage')) {
                $query->whereHas('lead_stage', function ($q) use ($request) {
                    $q->where('stage_name', 'LIKE', "%{$request->lead_stage}%");
                });
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
                        'name' => $lead->name ?? '-',
                        'email' => $lead->email ?? '-',
                        'contact' => $lead->contact ?? '-',
                        'source' => $lead->source->source_name ?? null,
                        'status' => $lead->status->status_name ?? null,
                        'stage'  =>  $lead->stage->stage_name ?? null,
                        'company_name' => $lead->company_name ?? '-',
                        'opportunity_amount' => $lead->opportunity_amount ?? '-',
                        'assigned_user_name' => $lead->assignedUser->name ?? null,
                        'log' => "Create DateTime : " . ($lead->created_at ? $lead->created_at->format('d-M-Y H:i:s') : 'N/A') .
                            " | Last Modified DateTime : " . ($lead->updated_at ? $lead->updated_at->format('d-M-Y H:i:s') : 'N/A'),

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

        //dd($request);

        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|string|max:100',
                    'address' => 'required|string|max:100',
                    'email' => 'required|string|max:100',
                    'contact' => 'required|string|max:100',
                    'lead_source' => 'required|exists:tbl_source,id',
                    'lead_status' => 'required|exists:tbl_lead_status,id',
                    'lead_stage' => 'required|exists:tbl_stage,id',
                    'company_name' => 'nullable|string|max:100',
                    'company_website' => 'nullable|string|max:100',
                    'opportunity_amount' => 'required|string|max:100',
                    'description' => 'nullable|string',
                    'referred_by' => 'nullable|string|max:100',
                    'value' => 'nullable|numeric',
                    'assigned_to' => 'required|exists:tbl_users,id',
                ],
                [
                    'lead_source.required' => 'The Lead Stage ID is required.',
                    'lead_status.required' => 'The Lead Status ID is required.',
                    'lead_stage.required' => 'The  Lead Stage ID is required.',
                    'assigned_to.required' => 'The Assign User is required.',
                ]

            );

            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }

            $lead = Lead::create($request->all());

            return response()->json(['status' => 200, 'message' => 'Lead created successfully', 'lead' => $lead]);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error creating lead', 'error' => $e->getMessage()]);
        }
    }


    // Show a single lead
    public function edit(Request $request)
    {
           try {
            $lead = Lead::find($request->id);

            if (!$lead) {
                return response()->json(['status' => 404, 'message' => 'Lead not found']);
            }
            return response()->json([
                'status' => 200,
                'message' => 'Lead fetched successfully',
                'lead' => $lead
            ]);

        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error fetching lead', 'error' => $e->getMessage()]);
        }
    }



    // Show a single lead
    public function show(Request $request)
    {
        try {
            $lead = Lead::with([
                'source:id,source_name', // Eager load lead source with name
                'status:id,status_name', // Eager load lead status with name
                'stage:id,stage_name', // Eager load lead status with name
                'assignedUser:id,name'
            ])->find($request->id);

            if (!$lead) {
                return response()->json(['status' => 404, 'message' => 'Lead not found']);
            }
            return response()->json([
                'status' => 200,
                'message' => 'Lead Show successfully',
                'lead' => [
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'address' => $lead->address ?? '-',
                    'contact' => $lead->contact ?? '-',
                    'source' => $lead->source->source_name ?? null,
                    'status' => $lead->status->status_name ?? null,
                    'stage' => $lead->stage->stage_name ?? null,
                    'company_name' => $lead->company_name ?? '-',
                    'company_website' => $lead->company_website ?? '-',
                    'description' => $lead->description ?? '-',
                    'referred_by' => $lead->referred_by ?? '-',
                    'value' => $lead->value ?? '-',
                    'opportunity_amount' => $lead->opportunity_amount ?? '-',
                    'assigned_user_name' => $lead->assignedUser->name ?? '-',
                    'log' => "Create DateTime : " . ($lead->created_at ? $lead->created_at->format('d-M-Y H:i:s') : 'N/A') .
                        " | Last Modified DateTime : " . ($lead->updated_at ? $lead->updated_at->format('d-M-Y H:i:s') : 'N/A'),
                ]
            ]);

        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error fetching lead', 'error' => $e->getMessage()]);
        }
    }

    //Lead Update 
    public function update(Request $request)
    {
        try {
            $lead = Lead::find($request->id);
            if (!$lead) {
                return response()->json(['message' => 'Lead not found'], 404);
            }

            $validator = Validator::make(
                $request->all(),
                [   
                    'id' => 'required|exists:tbl_leads,id',
                    'name' => 'required|string|max:100',
                    'address' => 'required|string|max:100',
                    'email' => [
                        'required',
                        'string',
                        'email',
                        'max:255',
                        Rule::unique('tbl_leads')->ignore($request->id)
                    ],
                    'contact' => 'required|string|max:100',
                    'lead_source' => 'required|exists:tbl_source,id',
                    'lead_status' => 'required|exists:tbl_lead_status,id',
                    'lead_stage' => 'required|exists:tbl_stage,id',
                    'company_name' => 'nullable|string|max:100',
                    'company_website' => 'nullable|string|max:100',
                    'opportunity_amount' => 'required|string|max:100',
                    'description' => 'nullable|string',
                    'referred_by' => 'nullable|string|max:100',
                    'value' => 'nullable|numeric',
                    'assigned_to' => 'required|exists:tbl_users,id',
                ],
                [
                    'lead_source.required' => 'The Lead Stage ID is required.',
                    'lead_status.required' => 'The Lead Status ID is required.',
                    'lead_stage.required' => 'The  Lead Stage ID is required.',
                    'assigned_to.required' => 'The Assign User is required.',
                ]

            );

            
            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }

            $lead->update($request->all());

            return response()->json(['status' => 200, 'message' => 'Lead updated successfully', 'lead' => $lead]);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error fetching lead', 'error' => $e->getMessage()]);
        }
    }




    // Delete a lead
    public function destroy(Request $request)
    {
        try {
            $lead = Lead::find($request->id);
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
