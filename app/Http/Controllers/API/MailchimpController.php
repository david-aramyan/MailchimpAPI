<?php
namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use NZTim\Mailchimp\Mailchimp;
use Validator;


class MailchimpController
{
    private $mc;

    public $successStatus = 200;
    public $badRequestStatus = 400;
    public $serverErrorStatus = 500;

    public function __construct()
    {
     // Initialize connection to Mailchimp API
        try {
            $this->mc = new Mailchimp(env('MC_KEY'));
        }catch (\Exception $e){
            return response()->json(['status' => 'Error', 'code' => $this->serverErrorStatus, 'message' => $e->getMessage()], $this->serverErrorStatus);
        }
    }

    // Get all the exiting lists
    public function getLists()
    {
        try {
            $res = $this->mc->getLists();
        }catch (\Exception $e){
            return response()->json(['status' => 'Error', 'code' => $this->serverErrorStatus, 'message' => $e->getMessage()], $this->serverErrorStatus);
        }
        return response()->json(['status' => 'Success', 'code' => $this->successStatus, 'message' => $res], $this->successStatus);
    }

    // Add new list
    public function addList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'contact' => 'required|array',
            'contact.company' => 'required',
            'contact.address1' => 'required',
            'contact.city' => 'required',
            'contact.state' => 'required',
            'contact.zip' => 'required',
            'contact.country' => 'required',
            'permission_reminder' => 'required',
            'campaign_defaults' => 'required|array',
            'campaign_defaults.from_name' => 'required',
            'campaign_defaults.from_email' => 'required',
            'campaign_defaults.subject' => 'required',
            'campaign_defaults.language' => 'required',
            'email_type_option' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'Error', 'code' => $this->badRequestStatus, 'message' => $validator->errors()], $this->successStatus);
        }

        try{
        $this->mc->api('POST','/lists', $request->all());
        }catch (\Exception $e){
            return response()->json(['status' => 'Error', 'code' => $this->serverErrorStatus, 'message' => $e->getMessage()], $this->serverErrorStatus);
        }
        return response()->json(['status' => 'Success', 'code' => $this->successStatus, 'message' => 'The list is successfully added!'], $this->successStatus);
    }

    // update list
    public function updateList(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'contact' => 'required|array',
            'contact.company' => 'required',
            'contact.address1' => 'required',
            'contact.city' => 'required',
            'contact.state' => 'required',
            'contact.zip' => 'required',
            'contact.country' => 'required',
            'permission_reminder' => 'required',
            'campaign_defaults' => 'required|array',
            'campaign_defaults.from_name' => 'required',
            'campaign_defaults.from_email' => 'required',
            'campaign_defaults.subject' => 'required',
            'campaign_defaults.language' => 'required',
            'email_type_option' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'Error', 'code' => $this->badRequestStatus, 'message' => $validator->errors()], $this->successStatus);
        }

        try{
            $this->mc->api('PATCH','/lists/'.$request->id, $request->all());
        }catch (\Exception $e){
            return response()->json(['status' => 'Error', 'code' => $this->serverErrorStatus, 'message' => $e->getMessage()], $this->serverErrorStatus);
        }
        return response()->json(['status' => 'Success', 'code' => $this->successStatus, 'message' => 'The list is successfully updated!'], $this->successStatus);
    }

    // delete list
    public function deleteList($id)
    {
        $this->mc->api('DELETE','/lists/'.$id);
    }

    // add new member to the list or update existing one
    public function member(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'fname' => 'required',
            'lname' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'Error', 'code' => $this->badRequestStatus, 'message' => $validator->errors()], $this->successStatus);
        }


        if ($request->is('*/addMember')) {
            $successMsg = 'New member successfully added!';
        }else{
            $successMsg = 'Member successfully updated!';
        }

        try{
            $this->mc->subscribe($id, $request->email, $merge = ['FNAME' => $request->fname, 'LNAME' => $request->lname], $confirm = false);
        }catch (\Exception $e){
            return response()->json(['status' => 'Error', 'code' => $this->serverErrorStatus, 'message' => $e->getMessage()], $this->serverErrorStatus);
        }
        return response()->json(['status' => 'Success', 'code' => $this->successStatus, 'message' => $successMsg], $this->successStatus);
    }

    // delete member of the list
    public function deleteMember(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'Error', 'code' => $this->badRequestStatus, 'message' => $validator->errors()], $this->successStatus);
        }

        $emailHash = md5(strtolower($request->email));
        try{
            $this->mc->api('DELETE','/lists/'.$id.'/members/'.$emailHash);
        }catch (\Exception $e){
            return response()->json(['status' => 'Error', 'code' => $this->serverErrorStatus, 'message' => $e->getMessage()], $this->serverErrorStatus);
        }
        return response()->json(['status' => 'Success', 'code' => $this->successStatus, 'message' => 'The member successfully deleted!'], $this->successStatus);
    }

}